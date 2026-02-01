<?php
require_once 'config.php';
require_once 'includes/functions.php';

$query = $_GET['q'] ?? null;
$titulo = "Películas Populares";

if ($query) {
    $datos = buscarPeliculas($query);
    $titulo = "Resultados para: " . htmlspecialchars($query);
} else {
    $datos = obtenerPopulares();
}

$peliculas = $datos['results'] ?? [];

require_once 'includes/header.php';
?>

<div class="cabecera-pagina">
    <h1 class="titulo-pagina"><?php echo $titulo; ?></h1>
    <?php if ($query): ?>
        <a href="index.php" class="text-sm text-muted">Borrar busqueda</a>
    <?php endif; ?>
</div>

<?php if (empty($peliculas)): ?>
    <div class="mensaje-vacio">
        <h3 class="texto-vacio-titulo">No se encontraron resultados</h3>
        <p class="texto-vacio-desc">Intenta con otro título.</p>
    </div>
<?php else: ?>
    <div class="grid-tarjetas">
        <?php foreach ($peliculas as $pelicula): ?>
            <?php 
                $poster = $pelicula['poster_path'] 
                    ? URL_IMAGEN . $pelicula['poster_path'] 
                    : 'https://via.placeholder.com/500x750?text=No+Image';
            ?>
            <a href="movie.php?id=<?php echo $pelicula['id']; ?>" class="tarjeta tarjeta-poster">
                <div class="img-contenedor">
                    <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy">
                </div>
                <h3 class="titulo-centro"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
