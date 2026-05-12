<?php
// includes/header.php
require_once __DIR__ . '/functions.php'; 
?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmSpo</title>
    <!-- CSS Propio Simplificado -->
    <link rel="stylesheet" href="css/estilos.css?v=<?php echo time(); ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lógica Global de Auth (Firebase) -->
    <script type="module">
        import { auth, db } from './js/firebase-config.js';
        import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
        import { doc, getDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

        const navLinks = document.querySelector('.nav-auth-container');

        onAuthStateChanged(auth, async (user) => {
            if (user) {
                // Usuario logueado
                const inicial = user.email ? user.email.charAt(0).toUpperCase() : 'U';
                const nombre = user.displayName ? user.displayName : user.email;
                
                let adminLink = '';
                try {
                    const userDoc = await getDoc(doc(db, "users", user.uid));
                    if (userDoc.exists() && userDoc.data().role === 'moderator') {
                        adminLink = `<a href="moderation.php" class="btn btn-oscuro" style="margin-right: 1rem; padding: 0.35rem 0.75rem; font-size: 0.8rem;">Panel Moderador</a>`;
                    }
                } catch(e) { console.error("Error fetching user role:", e); }

                navLinks.innerHTML = `
                    <div style="display: flex; align-items: center;">
                        ${adminLink}
                        <a href="profile.php" title="Perfil de ${nombre}">
                            <div class="avatar-peque">
                                <span style="font-size:0.875rem; font-weight:700;">${inicial}</span>
                            </div>
                        </a>
                    </div>
                `;
            } else {
                // Invitado
                navLinks.innerHTML = `
                    <a href="login.php" class="btn btn-oscuro" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                        Iniciar Sesión
                    </a>
                `;
            }
        });
    </script>
</head>
<body>

<nav class="navbar">
    <div class="navbar-contenido">
        <a href="index.php" class="marca">
            FILMSPO
        </a>

        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Buscar películas..." class="search-input" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            <button type="submit" class="search-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" style="width:1rem; height:1rem;" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </button>
        </form>

        <div class="nav-links">
            <a href="index.php">Explorar</a>
            
            <!-- Contenedor dinámico para auth -->
            <div class="nav-auth-container">
                <!-- Se rellena con JS -->
                <a href="login.php" class="btn btn-oscuro" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; opacity: 0.5;">
                    Cargando...
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="contenedor-principal">
    <?php require_once 'sidebar.php'; ?>
    <main class="contenido-central">
