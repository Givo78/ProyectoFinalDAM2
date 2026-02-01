<?php
require_once 'includes/functions.php';
// Ya no redirigimos con PHP, lo haremos con JS si detectamos sesión
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="mb-6">
        <h1 class="titulo-pagina">Iniciar Sesión</h1>
        <p class="text-muted">Bienvenido de nuevo a FilmSpo</p>
    </div>

    <div class="tarjeta">
        <div id="error-message" style="display:none; background: rgba(248, 113, 113, 0.2); border: 1px solid var(--color-rojo); color: var(--color-rojo); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;"></div>

        <form id="login-form">
            <div class="form-grupo">
                <label class="form-label" for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required class="form-input">
            </div>

            <div class="form-grupo">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" required class="form-input">
            </div>

            <div class="flex-between items-center mt-8">
                <a href="register.php" class="text-xs text-muted">¿No tienes cuenta? <span class="text-azul">Regístrate</span></a>
                <button type="submit" class="btn btn-blanco" id="btn-submit">Entrar</button>
            </div>
        </form>
    </div>
</div>

<script type="module">
    import { auth } from './js/firebase-config.js';
    import { signInWithEmailAndPassword, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

    // Si ya está logueado, redirigir
    onAuthStateChanged(auth, (user) => {
        if (user) {
            window.location.href = 'index.php';
        }
    });

    const loginForm = document.getElementById('login-form');
    const errorDiv = document.getElementById('error-message');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const btn = document.getElementById('btn-submit');

        errorDiv.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Verificando...';

        try {
            await signInWithEmailAndPassword(auth, email, password);
            // La redirección ocurrirá automáticamente por el onAuthStateChanged
        } catch (error) {
            console.error(error);
            errorDiv.textContent = 'Error: ' + error.message;
            errorDiv.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Entrar';
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
