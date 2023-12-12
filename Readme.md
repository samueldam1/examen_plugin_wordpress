# Plugin Cambiador de Contenido y Título en WordPress según Día de la Semana

Este plugin para WordPress permite cambiar dinámicamente el contenido y el título de las publicaciones basándose en el día de la semana.

## Métodos

### Crear Tabla

```php
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
```
Esta función se ejecuta *cuando el plugin es activado*. Utiliza consultas SQL para crear una tabla en la base de datos si esta no existe. La tabla almacenará el contenido para cada día de la semana.

### Obtener contenido

```php
function obtener_contenido_por_dia($dia_semana) {
    global $wpdb;
    $tabla_nombre = $wpdb->prefix . 'contenido_diario';

    // Consulta SQL para obtener el contenido según el día de la semana
    $resultado = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_nombre WHERE dia_semana = %d", $dia_semana));

    return $resultado;
}
```

Esta función recupera el contenido correspondiente al día de la semana desde la base de datos utilizando una consulta SQL.

### Cambiar Título y Contenido según el Día de la Semana

```php
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
```

Aquí se obtiene el nombre del día de la semana actual y se utiliza para obtener el contenido almacenado en la base de datos para ese día. Luego, se verifica si se está mostrando una publicación individual (is_singular()) y, en ese caso, se actualiza el título y el contenido de esa publicación.

## Registro de Funciones

```php
register_activation_hook(__FILE__, 'crear_tabla_contenido_dias');
```
Este código registra la función crear_tabla_contenido_dias() para ser ejecutada en cuanto el plugin es activado.

## Acciones

```php
function cambiar_titulo_contenido_segun_dia() {
  //Codigo...
}

add_action( 'plugins_loaded', 'cambiar_titulo_contenido_segun_dia' );
```
