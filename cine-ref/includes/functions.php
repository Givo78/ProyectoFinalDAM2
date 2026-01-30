<?php
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

function obtenerDetalle($id) {
    return conectarApi('/movie/' . $id);
}

function obtenerColecciones() {
    return [
        [
            'id' => 1,
            'titulo' => 'Esenciales Cinefilo',
            'descripcion' => 'Lista imprescindible de películas.',
            'cantidad_peliculas' => 3,
            'autor' => 'Alex'
        ]
    ];
}

function obtenerColeccion($id) {
    if ($id == 1) {
        return [
            'id' => 1,
            'titulo' => 'Esenciales Cinefilo',
            'descripcion' => 'Lista imprescindible de películas.',
            'autor' => 'Alex',
            'peliculas' => [
                ['id' => 550, 'title' => 'Fight Club', 'poster_path' => '/pB8BM7pdSp6B6Ih7Qf4n6a8mi75.jpg'],
                ['id' => 27205, 'title' => 'Inception', 'poster_path' => '/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg'],
                ['id' => 157336, 'title' => 'Interstellar', 'poster_path' => '/gEU2QniL6E77NI6lCU6MxlNBvIx.jpg']
            ]
        ];
    }
    return null;
}
?>
