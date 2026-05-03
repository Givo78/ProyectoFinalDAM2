<?php
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="mb-6">
        <h1 class="titulo-pagina">Panel de Moderación</h1>
        <p class="text-muted">Revisa y aprueba los posts pendientes de los usuarios.</p>
    </div>

    <div id="auth-warning" style="display:none;" class="tarjeta">
        <p class="text-rojo text-center">No tienes permisos para ver esta página.</p>
    </div>

    <div id="moderation-content" style="display:none;">
        <div id="pending-posts-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(clamp(18%, 200px, 48%), 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            <p class="text-muted" id="loading-msg">Cargando posts pendientes...</p>
        </div>
    </div>
</div>

<script type="module">
    import { auth, db, storage } from './js/firebase-config.js';
    import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { doc, getDoc, collection, query, where, getDocs, updateDoc, deleteDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
    import { ref, deleteObject } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";

    const authWarning = document.getElementById('auth-warning');
    const modContent = document.getElementById('moderation-content');
    const postsContainer = document.getElementById('pending-posts-container');
    const loadingMsg = document.getElementById('loading-msg');

    onAuthStateChanged(auth, async (user) => {
        if (user) {
            try {
                const userDoc = await getDoc(doc(db, "users", user.uid));
                if (userDoc.exists() && userDoc.data().role === 'moderator') {
                    // Es moderador
                    modContent.style.display = 'block';
                    loadPendingPosts();
                } else {
                    authWarning.style.display = 'block';
                    setTimeout(() => window.location.href = 'index.php', 2000);
                }
            } catch (e) {
                console.error("Error verificando rol:", e);
                authWarning.style.display = 'block';
            }
        } else {
            window.location.href = 'login.php';
        }
    });

    async function loadPendingPosts() {
        try {
            const q = query(
                collection(db, "posts"), 
                where("status", "==", "pending")
            );
            const querySnapshot = await getDocs(q);
            
            postsContainer.innerHTML = '';
            
            if (querySnapshot.empty) {
                postsContainer.innerHTML = '<p class="text-muted col-span-full">No hay posts pendientes de moderación.</p>';
                return;
            }

            querySnapshot.forEach((docSnap) => {
                const data = docSnap.data();
                const postId = docSnap.id;
                
                const postEl = document.createElement('div');
                postEl.className = 'tarjeta flex flex-col justify-between';
                postEl.style.padding = '1rem';
                postEl.innerHTML = `
                    <div>
                        <div style="background-color: #111; border-radius: 0.5rem; overflow: hidden; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                            <img src="${data.imageUrl}" alt="Post image" style="width: 100%; height: auto; max-height: 350px; object-fit: contain;">
                        </div>
                        <h4 class="font-bold text-lg mb-1" style="color:var(--color-texto);">${data.title}</h4>
                        <p class="text-sm text-muted mb-1">Por: <span class="text-azul">${data.username}</span></p>
                        <p class="text-sm text-muted mb-3">ID Película: ${data.movieId}</p>
                        <p class="text-sm mb-4" style="color:var(--color-texto-secundario);">${data.description}</p>
                    </div>
                    <div class="flex gap-2 mt-4" style="display:flex; gap:0.5rem;">
                        <button class="btn btn-blanco flex-1 btn-approve" data-id="${postId}">Aprobar</button>
                        <button class="btn btn-oscuro flex-1 btn-reject" data-id="${postId}" data-url="${data.imageUrl}">Rechazar</button>
                    </div>
                `;
                postsContainer.appendChild(postEl);
            });

            // Añadir eventos a los botones
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', handleApprove);
            });
            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', handleReject);
            });

        } catch (e) {
            console.error("Error cargando posts:", e);
            postsContainer.innerHTML = '<p class="text-rojo">Error al cargar los posts pendientes.</p>';
        }
    }

    async function handleApprove(e) {
        const btn = e.target;
        const postId = btn.getAttribute('data-id');
        btn.disabled = true;
        btn.textContent = '...';

        try {
            await updateDoc(doc(db, "posts", postId), {
                status: "approved"
            });
            // Recargar lista
            loadPendingPosts();
        } catch (error) {
            console.error("Error aprobando:", error);
            alert("Error al aprobar el post");
            btn.disabled = false;
            btn.textContent = 'Aprobar';
        }
    }

    async function handleReject(e) {
        if (!confirm("¿Seguro que quieres rechazar y eliminar este post?")) return;

        const btn = e.target;
        const postId = btn.getAttribute('data-id');
        const imageUrl = btn.getAttribute('data-url');
        btn.disabled = true;
        btn.textContent = '...';

        try {
            // Eliminar documento
            await deleteDoc(doc(db, "posts", postId));
            
            // Intentar eliminar imagen de storage (opcional, requiere extraer el path del URL)
            // Para simplificar, lo dejamos así, pero en un entorno real deberíamos extraer el path:
            try {
                // firebase storage url decode
                const urlObj = new URL(imageUrl);
                const pathParts = urlObj.pathname.split('/');
                const encodedPath = pathParts[pathParts.length - 1];
                const actualPath = decodeURIComponent(encodedPath);
                
                const imageRef = ref(storage, actualPath);
                await deleteObject(imageRef);
            } catch(e) {
                console.log("No se pudo eliminar la imagen de Storage (quizás ruta incorrecta)", e);
            }

            // Recargar lista
            loadPendingPosts();
        } catch (error) {
            console.error("Error rechazando:", error);
            alert("Error al rechazar el post");
            btn.disabled = false;
            btn.textContent = 'Rechazar';
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
