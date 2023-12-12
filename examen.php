<?php
/*
 * Plugin Name: Plugin Examen
 * Plugin URI: http://www.danielcastelao.org/
 * Description: Cambio de contenido y titulo en referencia al día de la semana
 * Version: 1.0
 * Author: Samuel
*/

// Crear tabla al activar el plugin
function crear_tabla_contenido_dias() {
    global $wpdb;
    $tabla_nombre = $wpdb->prefix . 'contenido_diario';

    // Consulta SQL para crear la tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS $tabla_nombre (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        dia_semana tinyint(1) NOT NULL,
        titulo varchar(100) NOT NULL,
        contenido text NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB;";

    // Incluir el archivo necesario para ejecutar la consulta SQL
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql); // Ejecutar la consulta y crear la tabla si no existe
}

// Registrar la función de creación de la tabla al activar el plugin
register_activation_hook(__FILE__, 'crear_tabla_contenido_dias');

// Función para obtener el contenido según el día de la semana
function obtener_contenido_por_dia($dia_semana) {
    global $wpdb;
    $tabla_nombre = $wpdb->prefix . 'contenido_diario';

    // Consulta SQL para obtener el contenido según el día de la semana
    $resultado = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_nombre WHERE dia_semana = %d", $dia_semana));

    return $resultado;
}

// Función para cambiar el título y contenido según el día de la semana
function cambiar_titulo_contenido_segun_dia() {
    $dia_semana = date("l"); // Obtener el nombre del día de la semana

    // Obtener contenido según el día de la semana desde la base de datos
    $contenido_dia = obtener_contenido_por_dia($dia_semana);

    if ($contenido_dia) {
        // Obtener el título y contenido desde la base de datos para el día actual
        $titulo_actual = "Hoy es " . $contenido_dia->titulo;
        $contenido_actual = "Feliz " . $contenido_dia->contenido;

        // Verificar si se está visualizando una publicación individual
        if (is_singular()) {
            global $post;
            // Asignar el título y contenido obtenidos a la publicación actual
            $post->post_title = $titulo_actual;
            $post->post_content = $contenido_actual;
        }
    }
}

// Gancho que ejecuta el método después de que se han cargado todos los plugins en WordPress.
add_action( 'plugins_loaded', 'cambiar_titulo_contenido_segun_dia' );
?>
