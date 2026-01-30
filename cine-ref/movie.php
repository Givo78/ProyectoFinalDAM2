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
        <h1 class="detalle-titulo"><?php echo htmlspecialchars($pelicula['title']); ?></h1>
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

<?php require_once 'includes/footer.php'; ?>
