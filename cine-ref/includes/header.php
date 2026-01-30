<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmSpo</title>
    <!-- CSS Propio Simplificado -->
    <link rel="stylesheet" href="css/estilos.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <div class="navbar-contenido">
        <a href="index.php" class="marca">
            FILMSPO
        </a>

        <div class="nav-links">
            <a href="index.php">Explorar</a>
            
            <a href="profile.php">
                <div class="avatar-peque">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icono" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
            </a>
        </div>
    </div>
</nav>

<div class="contenedor-principal">
    <?php require_once 'sidebar.php'; ?>
    <main class="contenido-central">
