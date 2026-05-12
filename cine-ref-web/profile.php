<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="cabecera-pagina">
        <h1 class="titulo-pagina">Configuración de Perfil</h1>
        <a href="logout.php" class="btn btn-oscuro btn-rojo-texto">Cerrar Sesión</a>
    </div>

    <div id="loading" style="text-align:center; padding: 2rem;">Cargando perfil...</div>
    <div id="error-message" style="display:none; background: rgba(248, 113, 113, 0.2); border: 1px solid var(--color-rojo); color: var(--color-rojo); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;"></div>
    <div id="success-message" style="display:none; background: rgba(34, 197, 94, 0.2); border: 1px solid var(--color-verde); color: var(--color-verde); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;"></div>

    <!-- Perfil Form -->
    <form id="profile-form" style="display:none;" class="mb-6">
        <h2 class="subtitulo-seccion">Información Personal</h2>
        <div class="tarjeta mb-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="avatar-grande">
                   <span id="avatar-initial" style="font-size:1.5rem; font-weight:700;">U</span>
                </div>
                <div>
                    <h3 id="display-name-header" style="font-weight:600; color:white;">Usuario</h3>
                    <p id="display-email-header" class="text-xs text-muted">email@ejemplo.com</p>
                </div>
            </div>

            <div class="form-grupo">
                <label class="form-label">Nombre de Usuario</label>
                <input type="text" id="username" name="username" class="form-input" required>
            </div>
             <div class="form-grupo">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-input" required disabled title="El email no se puede cambiar por ahora.">
            </div>

            <div class="form-acciones">
                <button type="submit" class="btn btn-blanco" id="btn-save">Guardar Cambios</button>
            </div>
        </div>
    </form>
    
    <!-- Sección Favoritos -->
    <div id="favorites-section" style="display:none;" class="mb-6">
        <h2 class="subtitulo-seccion">Mis Favoritos</h2>
        <div id="favorites-grid" class="grid-tarjetas" style="grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));">
            <!-- Cargando o grid de peliculas -->
            <p class="text-muted text-sm">Cargando favoritos...</p>
        </div>
    </div>

    <!-- Preferencias -->
    <div id="preferences-section" style="display:none;">
        <h2 class="subtitulo-seccion">Preferencias</h2>
        <div class="tarjeta">
            <div class="fila-preferencia borde-inferior">
                <div>
                    <p class="texto-pref-titulo">Perfil Público</p>
                    <p class="text-xs text-muted">Permitir que otros vean tus colecciones</p>
                </div>
                <div class="toggle toggle-activo">
                    <div class="toggle-circulo"></div>
                </div>
            </div>
            <div class="fila-preferencia">
                <div>
                    <p class="texto-pref-titulo">Notificaciones por Email</p>
                    <p class="text-xs text-muted">Recibir digest semanal</p>
                </div>
                <div class="toggle toggle-inactivo">
                    <div class="toggle-circulo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { auth, db } from './js/firebase-config.js';
    import { onAuthStateChanged, updateProfile } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { collection, getDocs } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

    const loading = document.getElementById('loading');
    const form = document.getElementById('profile-form');
    const pref = document.getElementById('preferences-section');
    const favSection = document.getElementById('favorites-section');
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    const favGrid = document.getElementById('favorites-grid');

    onAuthStateChanged(auth, async (user) => {
        if (user) {
            loading.style.display = 'none';
            form.style.display = 'block';
            pref.style.display = 'block';
            favSection.style.display = 'block';

            // Rellenar datos perfil
            const name = user.displayName || '';
            const email = user.email || '';
            const initial = email.charAt(0).toUpperCase();

            document.getElementById('username').value = name;
            document.getElementById('email').value = email;
            document.getElementById('display-name-header').textContent = name || 'Usuario';
            document.getElementById('display-email-header').textContent = email;
            document.getElementById('avatar-initial').textContent = initial;

            // Cargar favoritos
            await loadFavorites(user.uid);

        } else {
            window.location.href = 'login.php';
        }
    });

    async function loadFavorites(uid) {
        try {
            const querySnapshot = await getDocs(collection(db, "users", uid, "favorites"));
            
            if (querySnapshot.empty) {
                favGrid.innerHTML = '<p class="text-muted text-sm" style="grid-column: 1/-1;">No tienes películas favoritas todavía.</p>';
                return;
            }

            let html = '';
            querySnapshot.forEach((doc) => {
                const movie = doc.data();
                const posterUrl = movie.poster_path 
                    ? `<?php echo URL_IMAGEN; ?>${movie.poster_path}`
                    : 'https://via.placeholder.com/500x750?text=No+Image';

                html += `
                    <a href="movie.php?id=${movie.id}" class="tarjeta tarjeta-poster">
                        <div class="img-contenedor">
                            <img src="${posterUrl}" alt="${movie.title}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <h3 class="titulo-centro text-xs mt-2">${movie.title}</h3>
                    </a>
                `;
            });
            favGrid.innerHTML = html;

        } catch (e) {
            console.error("Error cargando favoritos:", e);
            favGrid.innerHTML = '<p class="text-rojo text-sm">Error cargando favoritos.</p>';
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const btn = document.getElementById('btn-save');

        btn.disabled = true;
        btn.textContent = 'Guardando...';
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';

        try {
            await updateProfile(auth.currentUser, {
                displayName: username
            });
            
            successDiv.textContent = 'Perfil actualizado correctamente.';
            successDiv.style.display = 'block';
            
            // Actualizar vista
            document.getElementById('display-name-header').textContent = username;
            
        } catch (error) {
            console.error(error);
            errorDiv.textContent = 'Error: ' + error.message;
            errorDiv.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Guardar Cambios';
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
