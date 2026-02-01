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
?>
