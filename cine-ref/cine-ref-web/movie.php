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

            <button id="btn-fav" class="btn btn-icon-only" title="Añadir a favoritos" style="border-radius:50%; padding:0.75rem;">Favoritos
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
                <label class="form-label">Tipo de contenido</label>
                <div class="media-type-tabs" id="media-type-tabs">
                    <button type="button" class="media-tab active" data-type="image">Imagen</button>
                    <button type="button" class="media-tab" data-type="youtube">Video YouTube</button>
                    <button type="button" class="media-tab" data-type="text">Solo Texto</button>
                </div>
            </div>
            <div class="form-grupo" id="group-image">
                <label class="form-label" for="post-image">Imagen</label>
                <input type="file" id="post-image" accept="image/*" class="form-input" style="padding: 0.5rem;">
            </div>
            <div class="form-grupo" id="group-youtube" style="display:none;">
                <label class="form-label" for="post-youtube">URL de YouTube</label>
                <input type="text" id="post-youtube" class="form-input" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            <div class="form-grupo" id="group-layout">
                <label class="form-label">Diseño del Post</label>
                <div class="layout-picker" id="layout-picker">
                    <button type="button" class="layout-option selected" data-layout="classic">
                        <div class="layout-preview">
                            <div class="lp-img" style="width:100%; height:55%;"></div>
                            <div class="lp-lines">
                                <div class="lp-line"></div>
                                <div class="lp-line lp-line--short"></div>
                                <div class="lp-line lp-line--shorter"></div>
                            </div>
                        </div>
                        <span>Clásico</span>
                    </button>
                    <button type="button" class="layout-option" data-layout="inverted">
                        <div class="layout-preview">
                            <div class="lp-lines">
                                <div class="lp-line"></div>
                                <div class="lp-line lp-line--short"></div>
                                <div class="lp-line lp-line--shorter"></div>
                            </div>
                            <div class="lp-img" style="width:100%; height:55%;"></div>
                        </div>
                        <span>Invertido</span>
                    </button>
                    <button type="button" class="layout-option" data-layout="side-by-side">
                        <div class="layout-preview" style="flex-direction:row; gap:4px;">
                            <div class="lp-img" style="width:42%; height:100%;"></div>
                            <div class="lp-lines" style="flex:1; justify-content:center;">
                                <div class="lp-line"></div>
                                <div class="lp-line lp-line--short"></div>
                                <div class="lp-line lp-line--shorter"></div>
                            </div>
                        </div>
                        <span>Img + Texto</span>
                    </button>
                    <button type="button" class="layout-option" data-layout="side-inverted">
                        <div class="layout-preview" style="flex-direction:row; gap:4px;">
                            <div class="lp-lines" style="flex:1; justify-content:center;">
                                <div class="lp-line"></div>
                                <div class="lp-line lp-line--short"></div>
                                <div class="lp-line lp-line--shorter"></div>
                            </div>
                            <div class="lp-img" style="width:42%; height:100%;"></div>
                        </div>
                        <span>Texto + Img</span>
                    </button>
                    <button type="button" class="layout-option" data-layout="overlay">
                        <div class="layout-preview" style="position:relative; padding:0; overflow:hidden;">
                            <div class="lp-img" style="width:100%; height:100%; position:absolute; top:0; left:0;"></div>
                            <div style="position:absolute; bottom:0; left:0; width:100%; background:rgba(255,255,255,0.88); padding:4px 5px; display:flex; flex-direction:column; gap:2px;">
                                <div class="lp-line" style="background:#000;"></div>
                                <div class="lp-line lp-line--short" style="background:#555;"></div>
                            </div>
                        </div>
                        <span>Superposición</span>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-blanco" id="btn-submit-post">Subir Post</button>
            <p id="post-msg" class="text-sm mt-2" style="display:none;"></p>
        </form>
    </div>

    <style>
    .layout-picker { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem; }
    .layout-option { display: flex; flex-direction: column; align-items: center; gap: 0.4rem; cursor: pointer; border: 2px solid #000; background: #fff; padding: 0.5rem; box-shadow: 3px 3px 0 #000; transition: all 0.15s; font-family: var(--fuente-principal); font-size: 0.7rem; font-weight: 700; width: 90px; }
    .layout-option:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
    .layout-option.selected { background: var(--color-amarillo); border-color: #000; transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
    .layout-preview { width: 72px; height: 52px; border: 1.5px solid #000; display: flex; flex-direction: column; gap: 3px; padding: 4px; background: #f5f5f5; box-sizing: border-box; }
    .lp-img { background: #bbb; border: 1px solid #999; flex-shrink: 0; }
    .lp-lines { display: flex; flex-direction: column; gap: 2px; justify-content: flex-start; padding: 2px 0; }
    .lp-line { height: 3px; background: #333; border-radius: 1px; width: 100%; }
    .lp-line--short { width: 75%; }
    .lp-line--shorter { width: 50%; background: #888; }

    /* Media type tabs */
    .media-type-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }
    .media-tab { cursor: pointer; border: 2px solid #000; background: #fff; padding: 0.4rem 0.9rem; box-shadow: 3px 3px 0 #000; font-family: var(--fuente-principal); font-size: 0.8rem; font-weight: 700; transition: all 0.15s; }
    .media-tab:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
    .media-tab.active { background: var(--color-amarillo); transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }

    /* YouTube embed — flex: 1 fills remaining card height without overflowing */
    .yt-embed-wrapper { position: relative; width: 100%; flex: 1 1 0; min-height: 0; background: #000; }
    .layout-side-by-side .yt-embed-wrapper,
    .layout-side-inverted .yt-embed-wrapper { width: 40%; flex: 0 0 40%; align-self: stretch; }
    .yt-embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }

    /* Zoomable image cursor */
    .img-zoomable { cursor: zoom-in; }

    /* Lightbox */
    #lightbox { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.92); z-index: 9999; align-items: center; justify-content: center; }
    #lightbox.open { display: flex; }
    #lightbox-img { max-width: 90vw; max-height: 88vh; object-fit: contain; border: 3px solid #fff; box-shadow: 0 0 40px rgba(0,0,0,0.8); }
    #lightbox-close { position: absolute; top: 1rem; right: 1.5rem; background: none; border: none; color: #fff; font-size: 2.5rem; line-height: 1; cursor: pointer; font-weight: 700; }
    #lightbox-close:hover { color: var(--color-amarillo); }
    </style>

    <div id="posts-container" class="feed-divertido">
        <p class="text-muted" id="loading-posts">Cargando posts...</p>
    </div>

    <!-- Lightbox -->
    <div id="lightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
        <button id="lightbox-close" aria-label="Cerrar">&times;</button>
        <img id="lightbox-img" src="" alt="Imagen ampliada">
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
            loadPosts();
        } else {
            btnFav.onclick = () => window.location.href = 'login.php';
        }
    });

    async function checkIfFavorite() {
        if (!currentUser) return;
        const docRef = doc(db, "users", currentUser.uid, "favorites", movieId);
        try {
            const docSnap = await getDoc(docRef);
            if (docSnap.exists()) { isFav = true; updateIcon(); }
        } catch (e) { console.error("Error comprobando favorito:", e); }
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

        if (!isFav) {
            const favsSnap = await getDocs(collection(db, "users", currentUser.uid, "favorites"));
            if (favsSnap.size >= 3) {
                alert("Solo puedes guardar hasta 3 películas en favoritos.\nElimina una antes de añadir otra.");
                return;
            }
        }

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
            isFav = !isFav;
            updateIcon();
            alert("Hubo un error al actualizar favoritos.");
        }
    });

    function extractYouTubeId(url) {
        const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/);
        return match ? match[1] : null;
    }

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

                let hash = 0;
                for (let i = 0; i < postId.length; i++) {
                    hash = postId.charCodeAt(i) + ((hash << 5) - hash);
                }
                hash = Math.abs(hash);

                const tipos = ['medium', 'large', 'wide'];
                const tipo = tipos[hash % tipos.length];

                let layout = data.layout || (() => {
                    const fallbacks = ['classic', 'inverted', 'side-by-side', 'side-inverted'];
                    return tipo === 'wide'
                        ? (hash % 2 === 0 ? 'side-by-side' : 'side-inverted')
                        : fallbacks[(hash % 7) % fallbacks.length];
                })();

                const mediaType = data.mediaType || 'image';

                // Build media HTML based on type
                let mediaHtml = '';
                if (mediaType === 'image' && data.imageUrl) {
                    mediaHtml = `<img src="${data.imageUrl}" alt="Post image" class="poster-pequeno img-zoomable" title="Clic para ampliar">`;
                } else if (mediaType === 'youtube' && data.youtubeUrl) {
                    const videoId = extractYouTubeId(data.youtubeUrl);
                    if (videoId) {
                        mediaHtml = `<div class="yt-embed-wrapper"><iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen class="yt-embed"></iframe></div>`;
                    }
                }

                const postEl = document.createElement('div');
                postEl.className = `tarjeta-divertida tarjeta-${tipo} layout-${layout}`;

                const deleteHtml = isModerator
                    ? `<button class="btn btn-oscuro text-rojo text-sm mt-3 btn-delete-post" data-id="${postId}" data-url="${data.imageUrl || ''}" data-mediatype="${mediaType}" style="width:100%; border:1px solid var(--color-rojo); background:transparent;">Borrar Post</button>`
                    : '';

                const contentHtml = `
                    <h3 class="titulo-gigante" style="font-size: 1.6rem; margin-bottom: 0.5rem; text-align: left;">${data.title}</h3>
                    <p class="text-sm text-muted mb-3">Por: <span class="text-azul">${data.username}</span></p>
                    <p class="desc-tarjeta" style="border: none; padding: 0; margin-bottom: 1rem;">${data.description}</p>
                    ${deleteHtml}
                `;

                let innerHtml = '';
                if (!mediaHtml) {
                    // Text-only: just content, no media slot
                    innerHtml = `<div class="contenido-abajo" style="width:100%;">${contentHtml}</div>`;
                } else if (layout === 'inverted') {
                    innerHtml = `<div class="contenido-arriba" style="width:100%;">${contentHtml}</div>${mediaHtml}`;
                } else if (layout === 'side-by-side') {
                    innerHtml = `${mediaHtml}<div class="contenido-lateral" style="flex:1;">${contentHtml}</div>`;
                } else if (layout === 'side-inverted') {
                    innerHtml = `<div class="contenido-lateral" style="flex:1;">${contentHtml}</div>${mediaHtml}`;
                } else {
                    innerHtml = `${mediaHtml}<div class="contenido-abajo" style="width:100%;">${contentHtml}</div>`;
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
        // Lightbox on image click
        if (e.target.classList.contains('img-zoomable')) {
            document.getElementById('lightbox-img').src = e.target.src;
            document.getElementById('lightbox').classList.add('open');
            return;
        }

        // Delete post
        if (e.target.classList.contains('btn-delete-post')) {
            if (!confirm("¿Borrar post definitivamente?")) return;
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            const url = btn.getAttribute('data-url');
            const mediaType = btn.getAttribute('data-mediatype');

            btn.disabled = true;
            btn.textContent = 'Borrando...';
            try {
                await deleteDoc(doc(db, "posts", id));
                if (mediaType === 'image' && url) {
                    try {
                        const urlObj = new URL(url);
                        const pathParts = urlObj.pathname.split('/');
                        const encodedPath = pathParts[pathParts.length - 1];
                        const actualPath = decodeURIComponent(encodedPath);
                        await deleteObject(ref(storage, actualPath));
                    } catch(err) { console.log("No se pudo eliminar la imagen", err); }
                }
                loadPosts();
            } catch(err) {
                console.error(err);
                alert("Error al borrar el post");
                btn.disabled = false;
                btn.textContent = 'Borrar Post';
            }
        }
    });

    // Lightbox close
    const lightbox = document.getElementById('lightbox');
    document.getElementById('lightbox-close').addEventListener('click', () => lightbox.classList.remove('open'));
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) lightbox.classList.remove('open');
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') lightbox.classList.remove('open');
    });

    // Layout picker
    let selectedLayout = 'classic';
    document.getElementById('layout-picker').addEventListener('click', (e) => {
        const opt = e.target.closest('.layout-option');
        if (!opt) return;
        document.querySelectorAll('.layout-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        selectedLayout = opt.dataset.layout;
    });

    // Media type tabs
    let selectedMediaType = 'image';
    document.getElementById('media-type-tabs').addEventListener('click', (e) => {
        const tab = e.target.closest('.media-tab');
        if (!tab) return;
        document.querySelectorAll('.media-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        selectedMediaType = tab.dataset.type;

        document.getElementById('group-image').style.display   = selectedMediaType === 'image'   ? 'block' : 'none';
        document.getElementById('group-youtube').style.display = selectedMediaType === 'youtube' ? 'block' : 'none';
        document.getElementById('group-layout').style.display  = selectedMediaType === 'text'    ? 'none'  : 'block';
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
        if (!currentUser) return;

        btnSubmitPost.disabled = true;
        btnSubmitPost.textContent = 'Subiendo...';
        msgPost.style.display = 'none';

        try {
            const title = document.getElementById('post-title').value;
            const desc = document.getElementById('post-desc').value;

            let imageUrl = '';
            let youtubeUrl = '';

            if (selectedMediaType === 'image') {
                const file = document.getElementById('post-image').files[0];
                if (!file) {
                    msgPost.textContent = "Selecciona una imagen.";
                    msgPost.className = "text-sm mt-2 text-rojo";
                    msgPost.style.display = 'block';
                    btnSubmitPost.disabled = false;
                    btnSubmitPost.textContent = 'Subir Post';
                    return;
                }
                const ext = file.name.split('.').pop();
                const filename = `posts/${Date.now()}_${Math.random().toString(36).substring(7)}.${ext}`;
                const storageRef = ref(storage, filename);
                await uploadBytes(storageRef, file);
                imageUrl = await getDownloadURL(storageRef);

            } else if (selectedMediaType === 'youtube') {
                const rawUrl = document.getElementById('post-youtube').value.trim();
                if (!extractYouTubeId(rawUrl)) {
                    msgPost.textContent = "URL de YouTube no válida.";
                    msgPost.className = "text-sm mt-2 text-rojo";
                    msgPost.style.display = 'block';
                    btnSubmitPost.disabled = false;
                    btnSubmitPost.textContent = 'Subir Post';
                    return;
                }
                youtubeUrl = rawUrl;
            }

            const postStatus = isModerator ? "approved" : "pending";
            await addDoc(collection(db, "posts"), {
                movieId: movieId,
                userId: currentUser.uid,
                username: currentUser.displayName || currentUser.email,
                title: title,
                description: desc,
                mediaType: selectedMediaType,
                imageUrl: imageUrl,
                youtubeUrl: youtubeUrl,
                layout: selectedMediaType === 'text' ? 'classic' : selectedLayout,
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
