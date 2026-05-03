<?php
// includes/functions.php
require_once 'config.php';

// Ya no necesitamos db.php ni session_start para auth de servidor, 
// pero mantenemos functions por si necesitamos cosas de API en el servidor (aunque idealmente todo iría a JS)

// --- Mock Data y API (Server Side Rendering for SEO/Initial Load) ---
// Aun usamos esto para pintar el index.php rápido antes de cargar JS
function conectarApi($ruta, $parametros = []) {
    $url = URL_API . $ruta . '?api_key=' . CLAVE_API . '&language=es-ES';
    
    foreach ($parametros as $clave => $valor) {
        $url .= '&' . $clave . '=' . urlencode($valor);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $respuesta = curl_exec($ch);
    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($codigo !== 200) return null;

    return json_decode($respuesta, true);
}

function obtenerPopulares() {
    return conectarApi('/movie/popular', ['page' => 1]);
}

function buscarPeliculas($query) {
    return conectarApi('/search/movie', ['query' => $query]);
}

function obtenerDetalle($id) {
    return conectarApi('/movie/' . $id);
}

function obtenerPeliculasInspiradoras() {
    $ids = [
        438148, // Dune
        10315,  // Fantastic Mr. Fox
        545611, // Everything Everywhere All at Once
        376867, // Moonlight
        120467, // El Gran Hotel Budapest
        540473, // The French Dispatch
        792307, // Poor Things
        531428, // Portrait of a Lady on Fire
        335984, // Blade Runner 2049
        324857, // Spider-Man Into the Spider-Verse
        64690,  // Drive
        313369, // La La Land
        844,    // In the Mood for Love
        496243, // Parasite
        152601, // Her
        530385, // Midsommar
        76341,  // Mad Max Fury Road
        34647,  // Enter the Void
        301365, // The Neon Demon
        194,    // Amelie
        129,    // El Viaje de Chihiro
        11906,  // Suspiria
        62,     // 2001: A Space Odyssey
        329865, // Arrival
        157336, // Interstellar
        603,    // The Matrix
        747188, // Asteroid City
        762504, // Nope
        414906, // The Batman
        872585, // Oppenheimer
        569094, // Spider-Man: Across the Spider-Verse
        290098, // The Handmaiden
        503919, // The Lighthouse
        473033, // Uncut Gems
        399174, // Isle of Dogs
        399055, // The Shape of Water
        530915, // 1917
        49047,  // Gravity
        113,    // A Clockwork Orange
        11658,  // Oldboy
        37165,  // The Truman Show
        11104,  // Chungking Express
        146,    // Crouching Tiger, Hidden Dragon
        14784,  // The Fall
        149,    // Akira
        9323,   // Ghost in the Shell
        1417,   // Pan's Labyrinth
        268896, // Roma
        8967,   // The Tree of Life
        361292  // Suspiria (2018)
    ];
    
    $resultados = [];
    foreach ($ids as $id) {
        $detalle = obtenerDetalle($id);
        if ($detalle && !isset($detalle['success'])) {
            $resultados[] = $detalle;
        }
    }
    
    return ['results' => $resultados];
}
?>
