<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="cabecera-pagina">
        <h1 class="titulo-pagina">Configuración de Perfil</h1>
        <button class="btn btn-oscuro btn-rojo-texto">Cerrar Sesión</button>
    </div>

    <div class="mb-6">
        <h2 class="subtitulo-seccion">Información Personal</h2>
        <div class="tarjeta">
            <div class="flex items-center gap-4 mb-6">
                <div class="avatar-grande">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icono-grande" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 style="font-weight:600; color:white;">Usuario Invitado</h3>
                    <p class="text-xs text-muted">guest@filmspo.com</p>
                </div>
            </div>

            <div class="form-grupo">
                <label class="form-label">Nombre de Usuario</label>
                <input type="text" value="UsuarioInvitado" class="form-input">
            </div>
             <div class="form-grupo">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" value="guest@filmspo.com" class="form-input">
            </div>
        </div>
    </div>

    <div>
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

<?php require_once 'includes/footer.php'; ?>
