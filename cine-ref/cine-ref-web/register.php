<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="mb-6">
        <h1 class="titulo-pagina">Crear Cuenta</h1>
        <p class="text-muted">Únete a la comunidad de FilmSpo</p>
    </div>

    <div class="tarjeta">
        <div id="error-message" style="display:none; background: rgba(248, 113, 113, 0.2); border: 1px solid var(--color-rojo); color: var(--color-rojo); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;"></div>

        <form id="register-form">
            <div class="form-grupo">
                <label class="form-label" for="username">Nombre de Usuario (Opcional)</label>
                <input type="text" id="username" name="username" class="form-input">
            </div>

            <div class="form-grupo">
                <label class="form-label" for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required class="form-input">
            </div>

            <div class="form-grupo">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" required class="form-input">
            </div>

            <div class="form-grupo">
                <label class="form-label" for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-input">
            </div>

            <div class="flex-between items-center mt-8">
                <a href="login.php" class="text-xs text-muted">¿Ya tienes cuenta? <span class="text-azul">Inicia Sesión</span></a>
                <button type="submit" class="btn btn-blanco" id="btn-submit">Registrarse</button>
            </div>
        </form>
    </div>
</div>

<script type="module">
    import { auth, db } from './js/firebase-config.js';
    import { createUserWithEmailAndPassword, updateProfile, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { doc, setDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

    // Si ya está logueado, redirigir
    onAuthStateChanged(auth, (user) => {
        if (user) {
            window.location.href = 'index.php';
        }
    });

    const registerForm = document.getElementById('register-form');
    const errorDiv = document.getElementById('error-message');

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const btn = document.getElementById('btn-submit');

        errorDiv.style.display = 'none';

        if (password !== confirmPassword) {
            errorDiv.textContent = 'Las contraseñas no coinciden';
            errorDiv.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Registrando...';

        try {
            const userCredential = await createUserWithEmailAndPassword(auth, email, password);
            
            // Actualizar el perfil con el nombre de usuario
            if (username) {
                await updateProfile(userCredential.user, {
                    displayName: username
                });
            }

            // Guardar el rol base del usuario en Firestore
            await setDoc(doc(db, "users", userCredential.user.uid), {
                uid: userCredential.user.uid,
                email: email,
                role: "user",
                displayName: username || ""
            });

            // Redirección manejada por onAuthStateChanged
        } catch (error) {
            console.error(error);
            errorDiv.textContent = 'Error: ' + error.message;
            errorDiv.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Registrarse';
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
