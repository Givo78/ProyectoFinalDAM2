<?php
require_once 'config.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? null;
$pelicula = null;

if ($id) {
    $pelicula = obtenerDetalle($id);
}

if (!$pelicula || isset($pelicula['success']) && $pelicula['success'] === false) {
    require_once 'includes/header.php';
    echo '<div class="mensaje-vacio">';
    echo '<h1 class="titulo-pagina">Película no encontrada</h1>';
    echo '<a href="index.php" class="text-azul">Volver a Colecciones</a>';
    echo '</div>';
    require_once 'includes/footer.php';
    exit;
}

require_once 'includes/header.php';
?>

<div class="detalle-layout">
    <div class="detalle-poster">
        <?php 
        $rutaPoster = $pelicula['poster_path'] 
            ? URL_IMAGEN . $pelicula['poster_path'] 
            : 'https://via.placeholder.com/500x750?text=No+Image';
        ?>
        <div class="detalle-poster-img">
            <img
                src="<?php echo $rutaPoster; ?>"
                alt="<?php echo htmlspecialchars($pelicula['title']); ?>"
            >
        </div>
    </div>

    <div class="detalle-info">
        <div class="flex-between items-center mb-4">
            <h1 class="detalle-titulo mb-0"><?php echo htmlspecialchars($pelicula['title']); ?></h1>
            
            <button id="btn-fav" class="btn btn-icon-only" title="Añadir a favoritos" style="border-radius:50%; padding:0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" id="icon-fav" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>

        <p class="detalle-fecha">
            <?php echo isset($pelicula['release_date']) ? htmlspecialchars($pelicula['release_date']) : 'Fecha desconocida'; ?>
        </p>
        <p class="detalle-desc">
            <?php echo htmlspecialchars($pelicula['overview'] ?? 'Sin descripción disponible.'); ?>
        </p>

        <div class="mt-4">
            <h2 class="subtitulo-seccion">Géneros:</h2>
            <ul class="etiquetas">
                <?php if (!empty($pelicula['genres'])): ?>
                    <?php foreach ($pelicula['genres'] as $genero): ?>
                        <li class="etiqueta">
                            <?php echo htmlspecialchars($genero['name']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-muted text-sm">Sin géneros</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="mt-8">
            <a href="javascript:history.back()" class="btn btn-oscuro">
                &larr; Volver
            </a>
        </div>
    </div>
</div>

<hr style="border:0; border-top: 1px solid var(--color-gris); margin: 2rem 0;">

<div class="foro-section">
    <div class="flex-between items-center mb-4">
        <h2 class="subtitulo-seccion mb-0">Foro de la Película</h2>
        <button id="btn-toggle-post" class="btn btn-oscuro" style="display:none;">Crear Post</button>
    </div>

    <div id="form-post-container" class="tarjeta mb-6" style="display:none;">
        <h3 class="text-lg font-bold mb-4">Nuevo Post</h3>
        <form id="form-post">
            <div class="form-grupo">
                <label class="form-label" for="post-title">Título</label>
                <input type="text" id="post-title" required class="form-input" placeholder="Título impactante">
            </div>
            <div class="form-grupo">
                <label class="form-label" for="post-desc">Descripción</label>
                <textarea id="post-desc" required class="form-input" rows="3" placeholder="¿Qué opinas?"></textarea>
            </div>
            <div class="form-grupo">
                <label class="form-label" for="post-image">Imagen</label>
                <input type="file" id="post-image" accept="image/*" required class="form-input" style="padding: 0.5rem;">
            </div>
            <button type="submit" class="btn btn-blanco" id="btn-submit-post">Subir Post</button>
            <p id="post-msg" class="text-sm mt-2" style="display:none;"></p>
        </form>
    </div>

    <div id="posts-container" class="feed-divertido">
        <p class="text-muted" id="loading-posts">Cargando posts...</p>
    </div>
</div>

<script type="module">
    import { auth, db, storage } from './js/firebase-config.js';
    import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { doc, getDoc, setDoc, deleteDoc, collection, addDoc, query, where, getDocs, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
    import { ref, uploadBytes, getDownloadURL, deleteObject } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";

    const btnFav = document.getElementById('btn-fav');
    const iconFav = document.getElementById('icon-fav');
    const movieId = "<?php echo $pelicula['id']; ?>";
    const movieData = {
        id: movieId,
        title: "<?php echo addslashes($pelicula['title']); ?>",
        poster_path: "<?php echo $pelicula['poster_path']; ?>",
        release_date: "<?php echo $pelicula['release_date']; ?>"
    };

    let currentUser = null;
    let isFav = false;
    let isModerator = false;

    // Verificar auth
    onAuthStateChanged(auth, async (user) => {
        if (user) {
            currentUser = user;
            try {
                const userDoc = await getDoc(doc(db, "users", user.uid));
                if (userDoc.exists() && userDoc.data().role === 'moderator') {
                    isModerator = true;
                }
            } catch(e) { console.error("Error obteniendo rol", e); }
            
            checkIfFavorite();
            document.getElementById('btn-toggle-post').style.display = 'block';
            loadPosts(); // Recargar para mostrar botones de borrar si es mod
        } else {
            // Si no hay user, deshabilitar o redirigir al pulsar
            btnFav.onclick = () => window.location.href = 'login.php';
        }
    });

    async function checkIfFavorite() {
        if (!currentUser) return;
        
        const docRef = doc(db, "users", currentUser.uid, "favorites", movieId);
        try {
            const docSnap = await getDoc(docRef);
            if (docSnap.exists()) {
                isFav = true;
                updateIcon();
            }
        } catch (e) {
            console.error("Error comprobando favorito:", e);
        }
    }

    function updateIcon() {
        if (isFav) {
            iconFav.setAttribute("fill", "currentColor");
            iconFav.classList.add("text-rojo");
            iconFav.classList.remove("text-muted");
        } else {
            iconFav.setAttribute("fill", "none");
            iconFav.classList.remove("text-rojo");
            iconFav.classList.add("text-muted");
        }
    }

    btnFav.addEventListener('click', async () => {
        if (!currentUser) return;

        // Optimistic UI update
        isFav = !isFav;
        updateIcon();

        const docRef = doc(db, "users", currentUser.uid, "favorites", movieId);
        
        try {
            if (isFav) {
                await setDoc(docRef, movieData);
            } else {
                await deleteDoc(docRef);
            }
        } catch (e) {
            console.error("Error actualizando favorito:", e);
            // Revertir si falla
            isFav = !isFav;
            updateIcon();
            alert("Hubo un error al actualizar favoritos.");
        }
    });

    // Lógica de Posts/Foro
    async function loadPosts() {
        const postsContainer = document.getElementById('posts-container');
        try {
            const q = query(
                collection(db, "posts"), 
                where("movieId", "==", movieId),
                where("status", "==", "approved")
            );
            const querySnapshot = await getDocs(q);
            
            postsContainer.innerHTML = '';
            
            if (querySnapshot.empty) {
                postsContainer.innerHTML = '<p class="text-muted">No hay posts todavía. ¡Sé el primero en comentar!</p>';
                return;
            }

            querySnapshot.forEach((docSnap) => {
                const data = docSnap.data();
                const postId = docSnap.id;
                
                // Hash the postId to generate a deterministic random layout
                let hash = 0;
                for (let i = 0; i < postId.length; i++) {
                    hash = postId.charCodeAt(i) + ((hash << 5) - hash);
                }
                hash = Math.abs(hash);
                
                const tipos = ['medium', 'large', 'wide']; // We exclude 'small' to ensure text fits
                const layouts = ['classic', 'inverted', 'side-by-side', 'side-inverted'];
                
                const tipo = tipos[hash % tipos.length];
                let layout = layouts[(hash % 7) % layouts.length];
                
                if (tipo === 'wide') {
                    layout = (hash % 2 === 0) ? 'side-by-side' : 'side-inverted';
                }

                const postEl = document.createElement('div');
                postEl.className = `tarjeta-divertida tarjeta-${tipo} layout-${layout}`;
                
                const deleteHtml = isModerator ? `<button class="btn btn-oscuro text-rojo text-sm mt-3 btn-delete-post" data-id="${postId}" data-url="${data.imageUrl}" style="width:100%; border:1px solid var(--color-rojo); background:transparent;">Borrar Post</button>` : '';

                const imgHtml = `<img src="${data.imageUrl}" alt="Post image" class="poster-pequeno">`;
                const contentHtml = `
                    <h3 class="titulo-gigante" style="font-size: 1.6rem; margin-bottom: 0.5rem; text-align: left;">${data.title}</h3>
                    <p class="text-sm text-muted mb-3">Por: <span class="text-azul">${data.username}</span></p>
                    <p class="desc-tarjeta" style="border: none; padding: 0; margin-bottom: 1rem;">${data.description}</p>
                    ${deleteHtml}
                `;

                let innerHtml = '';
                if (layout === 'inverted') {
                    innerHtml = `<div class="contenido-arriba" style="width:100%;">${contentHtml}</div>${imgHtml}`;
                } else if (layout === 'side-by-side') {
                    innerHtml = `${imgHtml}<div class="contenido-lateral" style="flex:1;">${contentHtml}</div>`;
                } else if (layout === 'side-inverted') {
                    innerHtml = `<div class="contenido-lateral" style="flex:1;">${contentHtml}</div>${imgHtml}`;
                } else { // classic
                    innerHtml = `${imgHtml}<div class="contenido-abajo" style="width:100%;">${contentHtml}</div>`;
                }

                postEl.innerHTML = innerHtml;
                postsContainer.appendChild(postEl);
            });
        } catch (e) {
            console.error("Error cargando posts:", e);
            postsContainer.innerHTML = '<p class="text-rojo">Error al cargar el foro.</p>';
        }
    }
    
    loadPosts();

    document.getElementById('posts-container').addEventListener('click', async (e) => {
        if(e.target.classList.contains('btn-delete-post')) {
            if(!confirm("¿Borrar post definitivamente?")) return;
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            const url = btn.getAttribute('data-url');
            
            btn.disabled = true;
            btn.textContent = 'Borrando...';
            try {
                await deleteDoc(doc(db, "posts", id));
                try {
                    const urlObj = new URL(url);
                    const pathParts = urlObj.pathname.split('/');
                    const encodedPath = pathParts[pathParts.length - 1];
                    const actualPath = decodeURIComponent(encodedPath);
                    await deleteObject(ref(storage, actualPath));
                } catch(err) { console.log("No se pudo eliminar la imagen", err); }
                loadPosts();
            } catch(err) {
                console.error(err);
                alert("Error al borrar el post");
                btn.disabled = false;
                btn.textContent = 'Borrar Post';
            }
        }
    });

    const btnTogglePost = document.getElementById('btn-toggle-post');
    const formContainer = document.getElementById('form-post-container');
    const formPost = document.getElementById('form-post');
    const msgPost = document.getElementById('post-msg');
    const btnSubmitPost = document.getElementById('btn-submit-post');

    btnTogglePost.addEventListener('click', () => {
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    });

    formPost.addEventListener('submit', async (e) => {
        e.preventDefault();
        if(!currentUser) return;

        btnSubmitPost.disabled = true;
        btnSubmitPost.textContent = 'Subiendo...';
        msgPost.style.display = 'none';

        try {
            const file = document.getElementById('post-image').files[0];
            const title = document.getElementById('post-title').value;
            const desc = document.getElementById('post-desc').value;

            // 1. Upload Image
            const ext = file.name.split('.').pop();
            const filename = `posts/${Date.now()}_${Math.random().toString(36).substring(7)}.${ext}`;
            const storageRef = ref(storage, filename);
            await uploadBytes(storageRef, file);
            const imageUrl = await getDownloadURL(storageRef);

            // 2. Save Document
            const postStatus = isModerator ? "approved" : "pending";
            await addDoc(collection(db, "posts"), {
                movieId: movieId,
                userId: currentUser.uid,
                username: currentUser.displayName || currentUser.email,
                title: title,
                description: desc,
                imageUrl: imageUrl,
                status: postStatus,
                createdAt: serverTimestamp()
            });

            if (isModerator) {
                msgPost.textContent = "Post publicado automáticamente (Eres Moderador).";
                loadPosts();
            } else {
                msgPost.textContent = "Post enviado con éxito. Pendiente de moderación.";
            }
            msgPost.className = "text-sm mt-2 text-azul";
            msgPost.style.display = 'block';
            formPost.reset();
            
        } catch (error) {
            console.error("Error al crear post:", error);
            msgPost.textContent = "Error al subir el post.";
            msgPost.className = "text-sm mt-2 text-rojo";
            msgPost.style.display = 'block';
        } finally {
            btnSubmitPost.disabled = false;
            btnSubmitPost.textContent = 'Subir Post';
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
