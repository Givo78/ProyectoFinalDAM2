<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="contenedor-limitado">
    <div class="mb-6">
        <a href="index.php" class="enlace-volver">&larr; Volver a Colecciones</a>
        <h1 class="titulo-pagina">Crear Nueva Colección</h1>
    </div>

    <form onsubmit="event.preventDefault(); alert('Función de crear pendiente de conexión a base de datos.'); window.location.href='index.php';">
        <div class="form-grupo">
            <label class="form-label">Nombre de la Colección</label>
            <input type="text" placeholder="ej. Películas de Acción de los 90" required class="form-input">
        </div>
        
        <div class="form-grupo">
            <label class="form-label">Descripción</label>
            <textarea placeholder="¿De qué trata esta colección?" rows="4" class="form-textarea"></textarea>
        </div>

        <div class="form-acciones">
            <a href="index.php" class="btn btn-cancelar">Cancelar</a>
            <button type="submit" class="btn btn-blanco">
                Crear Colección
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
