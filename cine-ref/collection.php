<?php
require_once 'config.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? null;
$coleccion = null;

if ($id) {
    $coleccion = obtenerColeccion($id);
}

if (!$coleccion) {
    require_once 'includes/header.php';
    echo '<div class="mensaje-vacio">';
    echo '<h1 class="titulo-pagina">Colección no encontrada</h1>';
    echo '<a href="index.php" class="text-azul">Volver a Colecciones</a>';
    echo '</div>';
    require_once 'includes/footer.php';
    exit;
}

require_once 'includes/header.php';
?>

<div class="mb-8">
    <a href="index.php" class="enlace-volver">&larr; Volver a Colecciones</a>
    
    <div class="flex-between flex-start">
        <div>
            <h1 class="titulo-pagina mb-2"><?php echo htmlspecialchars($coleccion['titulo']); ?></h1>
            <p class="text-muted text-sm mb-4" style="max-width:40rem;">
                <?php echo htmlspecialchars($coleccion['descripcion']); ?>
            </p>
            <div class="flex items-center gap-2 text-xs text-muted">
                <span class="flex items-center gap-2">
                    <div class="avatar-xs">
                        <?php echo strtoupper(substr($coleccion['autor'], 0, 1)); ?>
                    </div>
                    <?php echo htmlspecialchars($coleccion['autor']); ?>
                </span>
                <span>&bull;</span>
                <span><?php echo count($coleccion['peliculas']); ?> películas</span>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button class="btn-icon-only" title="Editar Colección">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
            </button>
            <button class="btn-icon-only btn-eliminar" title="Eliminar Colección">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>

<?php if (empty($coleccion['peliculas'])): ?>
    <div class="mensaje-vacio">
        <p>Esta colección está vacía.</p>
    </div>
<?php else: ?>
    <section class="grid-tarjetas">
        <?php foreach ($coleccion['peliculas'] as $pelicula): ?>
            <?php
                $rutaPoster = $pelicula['poster_path'] 
                    ? URL_IMAGEN . $pelicula['poster_path'] 
                    : 'https://via.placeholder.com/500x750?text=No+Image';
            ?>
            <a href="movie.php?id=<?php echo $pelicula['id']; ?>" class="tarjeta-poster">
                <div class="img-contenedor">
                    <img
                        src="<?php echo $rutaPoster; ?>"
                        alt="<?php echo htmlspecialchars($pelicula['title']); ?>"
                        loading="lazy"
                    >
                </div>
                <p class="titulo-centro">
                    <?php echo htmlspecialchars($pelicula['title']); ?>
                </p>
            </a>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
