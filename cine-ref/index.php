<?php
require_once 'config.php';
require_once 'includes/functions.php';

$colecciones = obtenerColecciones();

require_once 'includes/header.php';
?>

<div class="cabecera-pagina">
    <h1 class="titulo-pagina">Colecciones de Usuarios</h1>
    <a href="create_collection.php" class="btn btn-blanco">
        + Nueva Colección
    </a>
</div>

<?php if (empty($colecciones)): ?>
    <div class="mensaje-vacio">
        <h3 class="texto-vacio-titulo">No hay colecciones todavía</h3>
        <p class="texto-vacio-desc">Empieza a curar tus películas favoritas hoy mismo.</p>
        <a href="create_collection.php" class="text-azul">Crea tu primera colección &rarr;</a>
    </div>
<?php else: ?>
    <div class="grid-tarjetas">
        <?php foreach ($colecciones as $coleccion): ?>
            <a href="collection.php?id=<?php echo $coleccion['id']; ?>" class="tarjeta">
                <h3 class="titulo-tarjeta"><?php echo htmlspecialchars($coleccion['titulo']); ?></h3>
                <p class="desc-tarjeta"><?php echo htmlspecialchars($coleccion['descripcion']); ?></p>
                
                <div class="flex-between text-xs text-muted">
                    <span>por <?php echo htmlspecialchars($coleccion['autor']); ?></span>
                    <span class="badge"><?php echo $coleccion['cantidad_peliculas']; ?> películas</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
