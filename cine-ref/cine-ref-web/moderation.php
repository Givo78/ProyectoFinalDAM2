<?php
$hideSidebar = true;
require_once 'includes/header.php';
?>

<style>main.sin-sidebar { max-width: 100% !important; }</style>

<div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <!-- Cabecera del panel -->
    <div style="background: #000; border: var(--borde-grueso); padding: 2rem 2.5rem; margin-bottom: 2rem; box-shadow: 8px 8px 0px var(--color-rojo); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display:inline-block; background: var(--color-rojo); color:#fff; font-size: 0.7rem; font-weight:800; padding: 0.15rem 0.6rem; border: 2px solid #fff; letter-spacing: 2px; margin-bottom: 0.5rem; text-transform: uppercase;">ACCESO RESTRINGIDO</div>
            <h1 style="font-family: var(--fuente-titulos); font-size: 3rem; color: var(--color-amarillo); text-shadow: 4px 4px 0px var(--color-rojo); margin: 0; line-height: 1;">PANEL DE MODERACIÓN</h1>
            <p style="color: #aaa; margin-top: 0.5rem; font-size: 0.9rem;">Revisa y gestiona los posts pendientes de aprobación.</p>
        </div>
        <div id="stats-badge" style="display:none; background: var(--color-amarillo); border: var(--borde-grueso); padding: 1rem 1.5rem; text-align: center; box-shadow: 4px 4px 0px #fff; min-width: 100px;">
            <div id="stats-count" style="font-family: var(--fuente-titulos); font-size: 3rem; line-height: 1; color: #000;">0</div>
            <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #000;">Pendientes</div>
        </div>
    </div>

    <!-- Aviso de sin permisos -->
    <div id="auth-warning" style="display:none;">
        <div style="background: var(--color-rojo); border: var(--borde-grueso); padding: 3rem 2rem; text-align:center; box-shadow: var(--sombra-brutal);">
            <p style="font-family: var(--fuente-titulos); font-size: 2rem; color:#fff; text-shadow: 3px 3px 0px #000;">SIN PERMISOS</p>
            <p style="color:#fff; margin-top:0.5rem;">No tienes acceso a esta sección. Redirigiendo...</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div id="moderation-content" style="display:none;">

        <!-- Estado vacío -->
        <div id="empty-state" style="display:none; text-align:center; padding: 5rem 2rem; background: var(--color-verde); border: var(--borde-grueso); box-shadow: var(--sombra-brutal); transform: rotate(-0.5deg);">
            <p style="font-family: var(--fuente-titulos); font-size: 2.5rem; color:#000; text-shadow: 3px 3px 0px #fff; margin-bottom:0.5rem;">¡TODO AL DÍA!</p>
            <p style="font-size: 1rem; font-weight: 800; color: #000;">No hay posts pendientes de moderación.</p>
        </div>

        <!-- Cargando -->
        <div id="loading-state" style="text-align:center; padding: 4rem 2rem;">
            <div style="font-family: var(--fuente-titulos); font-size: 2rem; color:#000; animation: none;">Cargando posts...</div>
        </div>

        <!-- Grid de posts -->
        <div id="pending-posts-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;"></div>
    </div>
</div>

<script type="module">
    import { auth, db, storage } from './js/firebase-config.js';
    import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { doc, getDoc, collection, query, where, getDocs, updateDoc, deleteDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
    import { ref, deleteObject } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";

    const authWarning  = document.getElementById('auth-warning');
    const modContent   = document.getElementById('moderation-content');
    const postsGrid    = document.getElementById('pending-posts-container');
    const loadingState = document.getElementById('loading-state');
    const emptyState   = document.getElementById('empty-state');
    const statsBadge   = document.getElementById('stats-badge');
    const statsCount   = document.getElementById('stats-count');

    onAuthStateChanged(auth, async (user) => {
        if (!user) { window.location.href = 'login.php'; return; }

        try {
            const userDoc = await getDoc(doc(db, "users", user.uid));
            if (userDoc.exists() && userDoc.data().role === 'moderator') {
                modContent.style.display = 'block';
                statsBadge.style.display = 'block';
                loadPendingPosts();
            } else {
                authWarning.style.display = 'block';
                setTimeout(() => window.location.href = 'index.php', 2000);
            }
        } catch (e) {
            console.error("Error verificando rol:", e);
            authWarning.style.display = 'block';
        }
    });

    async function loadPendingPosts() {
        loadingState.style.display = 'block';
        emptyState.style.display   = 'none';
        postsGrid.innerHTML        = '';

        try {
            const q = query(collection(db, "posts"), where("status", "==", "pending"));
            const snap = await getDocs(q);

            loadingState.style.display = 'none';
            statsCount.textContent = snap.size;

            if (snap.empty) { emptyState.style.display = 'block'; return; }

            snap.forEach((docSnap) => {
                const data   = docSnap.data();
                const postId = docSnap.id;
                const mediaType = data.mediaType || 'image';

                let mediaHtml = '';
                if (mediaType === 'image' && data.imageUrl) {
                    mediaHtml = `<img src="${data.imageUrl}" alt="Imagen del post" style="width:100%; height:100%; object-fit:cover; display:block; border:none;">`;
                } else if (mediaType === 'youtube' && data.youtubeUrl) {
                    const match   = data.youtubeUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/);
                    const videoId = match ? match[1] : null;
                    if (videoId) {
                        mediaHtml = `<iframe src="https://www.youtube.com/embed/${videoId}" style="width:100%; height:100%; border:0;" allowfullscreen></iframe>`;
                    }
                } else {
                    mediaHtml = `<div style="display:flex; align-items:center; justify-content:center; height:100%; color:#555; font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px;">Post de texto</div>`;
                }

                const card = document.createElement('div');
                card.className = 'tarjeta';
                card.style.cssText = 'display:flex; flex-direction:column; padding:0; overflow:hidden;';
                card.innerHTML = `
                    <!-- Media -->
                    <div style="height:220px; background:#111; border-bottom: var(--borde-grueso); overflow:hidden; flex-shrink:0;">
                        ${mediaHtml}
                    </div>

                    <!-- Info -->
                    <div style="padding:1.25rem; flex:1; display:flex; flex-direction:column; gap:0.5rem;">
                        <h4 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; border-bottom:2px solid #000; padding-bottom:0.4rem; margin-bottom:0.25rem; color:#000;">${data.title || 'Sin título'}</h4>

                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <span style="background:var(--color-azul); border:2px solid #000; padding:0.1rem 0.5rem; font-size:0.72rem; font-weight:800; text-transform:uppercase;">
                                @${data.username || 'anónimo'}
                            </span>
                            <span style="background:var(--color-amarillo); border:2px solid #000; padding:0.1rem 0.5rem; font-size:0.72rem; font-weight:800;">
                                ID: ${data.movieId || '—'}
                            </span>
                        </div>

                        <p style="font-size:0.85rem; color:#333; flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; margin-top:0.25rem;">${data.description || ''}</p>
                    </div>

                    <!-- Botones -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; border-top: var(--borde-grueso);">
                        <button class="btn-mod-aprobar" data-id="${postId}"
                            style="background:var(--color-verde); color:#000; border:none; border-right: var(--borde-grueso); padding:0.85rem; font-weight:800; font-size:0.85rem; cursor:pointer; font-family:var(--fuente-principal); text-transform:uppercase; letter-spacing:1px; transition:all 0.1s;">
                            ✓ Aprobar
                        </button>
                        <button class="btn-mod-rechazar" data-id="${postId}" data-url="${data.imageUrl || ''}" data-mediatype="${mediaType}"
                            style="background:#fff; color:#000; border:none; padding:0.85rem; font-weight:800; font-size:0.85rem; cursor:pointer; font-family:var(--fuente-principal); text-transform:uppercase; letter-spacing:1px; transition:all 0.1s;">
                            ✕ Rechazar
                        </button>
                    </div>
                `;

                card.querySelector('.btn-mod-aprobar').addEventListener('mouseenter', e => { e.target.style.background = '#00cc55'; });
                card.querySelector('.btn-mod-aprobar').addEventListener('mouseleave', e => { e.target.style.background = 'var(--color-verde)'; });
                card.querySelector('.btn-mod-rechazar').addEventListener('mouseenter', e => { e.target.style.background = 'var(--color-rojo)'; e.target.style.color = '#fff'; });
                card.querySelector('.btn-mod-rechazar').addEventListener('mouseleave', e => { e.target.style.background = '#fff'; e.target.style.color = '#000'; });

                card.querySelector('.btn-mod-aprobar').addEventListener('click',   handleApprove);
                card.querySelector('.btn-mod-rechazar').addEventListener('click',  handleReject);

                postsGrid.appendChild(card);
            });

        } catch (e) {
            console.error("Error cargando posts:", e);
            loadingState.style.display = 'none';
            postsGrid.innerHTML = '<div style="grid-column:1/-1; background:var(--color-rojo); border:var(--borde-grueso); padding:2rem; text-align:center; color:#fff; font-weight:800;">Error al cargar los posts pendientes.</div>';
        }
    }

    async function handleApprove(e) {
        const btn    = e.currentTarget;
        const postId = btn.getAttribute('data-id');
        setButtonLoading(btn, 'Aprobando...');
        try {
            await updateDoc(doc(db, "posts", postId), { status: "approved" });
            loadPendingPosts();
        } catch (err) {
            console.error("Error aprobando:", err);
            alert("Error al aprobar el post");
            restoreButton(btn, '✓ Aprobar');
        }
    }

    async function handleReject(e) {
        if (!confirm("¿Seguro que quieres rechazar y eliminar este post?")) return;
        const btn       = e.currentTarget;
        const postId    = btn.getAttribute('data-id');
        const imageUrl  = btn.getAttribute('data-url');
        const mediaType = btn.getAttribute('data-mediatype');
        setButtonLoading(btn, 'Eliminando...');
        try {
            await deleteDoc(doc(db, "posts", postId));
            if (mediaType === 'image' && imageUrl) {
                try {
                    const urlObj      = new URL(imageUrl);
                    const pathParts   = urlObj.pathname.split('/');
                    const actualPath  = decodeURIComponent(pathParts[pathParts.length - 1]);
                    await deleteObject(ref(storage, actualPath));
                } catch(e) { console.log("No se pudo eliminar imagen de Storage", e); }
            }
            loadPendingPosts();
        } catch (err) {
            console.error("Error rechazando:", err);
            alert("Error al rechazar el post");
            restoreButton(btn, '✕ Rechazar');
        }
    }

    function setButtonLoading(btn, text) {
        btn.disabled = true;
        btn.textContent = text;
        btn.style.opacity = '0.6';
        btn.style.cursor  = 'not-allowed';
    }

    function restoreButton(btn, text) {
        btn.disabled = false;
        btn.textContent = text;
        btn.style.opacity = '1';
        btn.style.cursor  = 'pointer';
    }
</script>

<?php require_once 'includes/footer.php'; ?>
