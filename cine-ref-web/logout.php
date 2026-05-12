<?php
// logout.php servirá una página que ejecuta el logout de firebase
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="contenedor-limitado" style="text-align:center; padding: 4rem 1rem;">
    <h1 class="titulo-pagina">Cerrando sesión...</h1>
</div>

<script type="module">
    import { auth } from './js/firebase-config.js';
    import { signOut } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

    signOut(auth).then(() => {
        window.location.href = 'index.php';
    }).catch((error) => {
        console.error('Error al cerrar sesión:', error);
        window.location.href = 'index.php';
    });
</script>

<?php require_once 'includes/footer.php'; ?>
