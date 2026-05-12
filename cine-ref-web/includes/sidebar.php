<?php
$estrenos = obtenerEstrenos()['results'] ?? [];
$populares = obtenerPopulares()['results'] ?? [];
$top_rated = conectarApi('/movie/top_rated', ['page' => 1])['results'] ?? [];

// Limitar a 5 elementos
$estrenos = array_slice($estrenos, 0, 5);
$populares = array_slice($populares, 0, 5);
$top_rated = array_slice($top_rated, 0, 5);
?>

<aside class="sidebar">

    <!-- Próximos Estrenos -->
    <div class="bloque-sidebar">
        <h3 class="titulo-sidebar">Estrenos</h3>
        <ul class="lista-sidebar">
            <?php foreach ($estrenos as $sidebar_pelicula): ?>
                <li>
                    <a href="movie.php?id=<?php echo $sidebar_pelicula['id']; ?>" class="item-sidebar">
                        <span class="texto-destacado-sidebar"><?php echo htmlspecialchars($sidebar_pelicula['title']); ?></span>
                        <span class="texto-meta-sidebar"><?php echo date('d M', strtotime($sidebar_pelicula['release_date'])); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Películas Populares -->
    <div class="bloque-sidebar">
        <h3 class="titulo-sidebar">Tendencias</h3>
        <ul class="lista-sidebar">
            <?php foreach ($populares as $sidebar_pelicula): ?>
                <li>
                    <a href="movie.php?id=<?php echo $sidebar_pelicula['id']; ?>" class="item-sidebar">
                        <span class="texto-destacado-sidebar"><?php echo htmlspecialchars($sidebar_pelicula['title']); ?></span>
                        <span class="texto-meta-sidebar"><?php echo number_format($sidebar_pelicula['vote_average'], 1); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Películas Top Rated -->
    <div class="bloque-sidebar">
        <h3 class="titulo-sidebar">Joyas del Cine</h3>
        <ul class="lista-sidebar">
            <?php foreach ($top_rated as $sidebar_pelicula): ?>
                <li>
                    <a href="movie.php?id=<?php echo $sidebar_pelicula['id']; ?>" class="item-sidebar">
                        <span class="texto-destacado-sidebar"><?php echo htmlspecialchars($sidebar_pelicula['title']); ?></span>
                        <span class="texto-meta-sidebar"><?php echo number_format($sidebar_pelicula['vote_average'], 1); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>

<script type="module">
    import { auth, db } from './js/firebase-config.js';
    import { collection, query, where, orderBy, limit, onSnapshot } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

    const noticiasContenedor = document.getElementById('noticias-foro');

    if (noticiasContenedor) {
        const q = query(
            collection(db, "posts"),
            where("status", "==", "approved"),
            orderBy("createdAt", "desc"),
            limit(4)
        );

        onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                noticiasContenedor.innerHTML = '<li class="texto-meta-sidebar">No hay noticias recientes.</li>';
                return;
            }
            noticiasContenedor.innerHTML = '';
            snapshot.forEach((doc) => {
                const post = doc.data();
                const fecha = post.createdAt ? new Date(post.createdAt.seconds * 1000).toLocaleDateString() : 'Recientemente';
                const li = document.createElement('li');
                li.innerHTML = `
                    <a href="movie.php?id=${post.movieId}" class="item-sidebar">
                        <span class="texto-destacado-sidebar">${post.title || 'Nuevo Post'}</span>
                        <span class="texto-meta-sidebar">${fecha}</span>
                    </a>
                `;
                noticiasContenedor.appendChild(li);
            });
        }, (error) => {
            console.error("Error cargando noticias:", error);
        });
    }
</script>
