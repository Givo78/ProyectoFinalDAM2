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

<script type="module">
    import { auth, db } from './js/firebase-config.js';
    import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { doc, getDoc, setDoc, deleteDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

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

    // Verificar auth
    onAuthStateChanged(auth, async (user) => {
        if (user) {
            currentUser = user;
            checkIfFavorite();
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
</script>

<?php require_once 'includes/footer.php'; ?>
