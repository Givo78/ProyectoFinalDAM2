<?php
require_once 'config.php';
require_once 'includes/functions.php';

$query = $_GET['q'] ?? null;
$titulo = "Selección de Inspiración Visual";

if ($query) {
    $datos = buscarPeliculas($query);
    $titulo = "Resultados para: " . htmlspecialchars($query);
} else {
    $datos = obtenerPeliculasInspiradoras();
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
    <div class="feed-divertido">
        <?php foreach ($peliculas as $index => $pelicula): ?>
            <?php 
                $poster = $pelicula['poster_path'] 
                    ? URL_IMAGEN . $pelicula['poster_path'] 
                    : 'https://via.placeholder.com/500x750?text=No+Image';
                
                $hash = crc32($pelicula['id']);
                $tipos = ['small', 'medium', 'large', 'wide', 'hero'];
                $layouts = [
                    'classic', 'inverted', 'side-by-side', 'overlay', 
                    'text-only', 'side-inverted', 'poster-bg'
                ];
                
                $tipo = $tipos[$hash % count($tipos)];
                $layout = $layouts[($hash / 10) % count($layouts)];

                if ($tipo === 'small') $layout = 'image-only';
                if ($tipo === 'wide' && !in_array($layout, ['overlay', 'text-only', 'poster-bg'])) {
                    $layout = ($hash % 2 === 0) ? 'side-by-side' : 'side-inverted';
                }
                
                $bgColor = '';
                if ($layout === 'text-only') {
                    $colors = ['var(--color-amarillo)', 'var(--color-azul)', 'var(--color-acento)', 'var(--color-verde)'];
                    $bgColor = $colors[$hash % count($colors)];
                }
            ?>
            <a href="movie.php?id=<?php echo $pelicula['id']; ?>" class="tarjeta-divertida tarjeta-<?php echo $tipo; ?> layout-<?php echo $layout; ?>" <?php echo $bgColor ? 'style="background: '.$bgColor.'; border-color: #000;"' : ''; ?>>
                
                <?php if ($layout === 'text-only'): ?>
                    <div class="contenido-texto-completo">
                        <h3 class="titulo-gigante" style="font-size: 2.8rem; text-shadow: 4px 4px 0px #fff; color: #000;"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                        <?php if (!empty($pelicula['overview'])): ?>
                            <p class="desc-tarjeta mt-4" style="border:none; padding:0; font-size: 1.1rem; color: #000; font-weight: bold; line-height: 1.5;"><?php echo htmlspecialchars(substr($pelicula['overview'], 0, 150)) . '...'; ?></p>
                        <?php endif; ?>
                    </div>
                
                <?php elseif ($layout === 'poster-bg'): ?>
                    <div class="bg-blur" style="background-image: url('<?php echo $poster; ?>');"></div>
                    <div class="contenido-encima">
                        <h3 class="titulo-gigante" style="color: #fff; text-shadow: 3px 3px 0px #000; font-size: 3rem; line-height: 1;"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                    </div>
                
                <?php elseif ($layout === 'inverted'): ?>
                    <div class="contenido-arriba" style="width: 100%;">
                        <h3 class="titulo-gigante"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                        <?php if ($tipo === 'large' || $tipo === 'hero'): ?>
                            <p class="desc-tarjeta mt-4"><?php echo htmlspecialchars(substr($pelicula['overview'], 0, 80)) . '...'; ?></p>
                        <?php endif; ?>
                    </div>
                    <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy" class="poster-pequeno mt-4">
                
                <?php elseif ($layout === 'image-only'): ?>
                    <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy" class="poster-pequeno m-0">
                
                <?php elseif ($layout === 'side-by-side' || $layout === 'side-inverted'): ?>
                    <?php if ($layout === 'side-by-side'): ?>
                        <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy" class="poster-pequeno">
                    <?php endif; ?>
                    
                    <div class="contenido-lateral" style="flex: 1; width: 100%;">
                        <h3 class="titulo-gigante" style="text-align: <?php echo $layout === 'side-inverted' ? 'right' : 'left'; ?>;"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                        <?php if (!empty($pelicula['overview'])): ?>
                            <p class="desc-tarjeta mt-4"><?php echo htmlspecialchars(substr($pelicula['overview'], 0, 100)) . '...'; ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($layout === 'side-inverted'): ?>
                        <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy" class="poster-pequeno">
                    <?php endif; ?>
                
                <?php else: /* classic & overlay */ ?>
                    <img src="<?php echo $poster; ?>" alt="<?php echo htmlspecialchars($pelicula['title']); ?>" loading="lazy" class="poster-pequeno">
                    <div class="contenido-abajo" style="width: 100%;">
                        <h3 class="titulo-gigante"><?php echo htmlspecialchars($pelicula['title']); ?></h3>
                        <?php if (($tipo === 'large' || $tipo === 'hero') && !empty($pelicula['overview']) && $layout !== 'overlay'): ?>
                            <p class="desc-tarjeta mt-4"><?php echo htmlspecialchars(substr($pelicula['overview'], 0, 90)) . '...'; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
