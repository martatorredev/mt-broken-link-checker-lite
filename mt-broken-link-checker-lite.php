<?php
/**
 * Plugin Name: MT Broken Link Checker Lite
 * Plugin URI:  https://github.com/sponsors/martatorredev
 * Description: Un plugin ligero para detectar y gestionar enlaces rotos en tu sitio WordPress.
 * Version:     1.0.0
 * Author:      Marta Torre
 * Author URI:  https://martatorre.dev/en/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mt-broken-link-checker-lite
 */


// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para añadir el menú en el panel de administración
function mtdev_broken_link_checker_add_admin_menu() {
    add_menu_page(
        'MT Dev Broken Link Checker Lite', // Nombre en el menú
        'Broken Links',                     // Título del menú
        'manage_options',                   // Capacidad requerida para verlo
        'mtdev_broken_link_checker_admin',  // Slug del menú
        'mtdev_broken_link_checker_admin_page', // Función que mostrará la página
        'dashicons-editor-unlink',          // Icono del menú
        80                                   // Posición del menú
    );
}
add_action('admin_menu', 'mtdev_broken_link_checker_add_admin_menu');

// Función para mostrar la página de administración del plugin
function mtdev_broken_link_checker_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('MT Dev Broken Link Checker Lite', 'mtdev-broken-link-checker-lite'); ?></h1>
        <p><?php _e('Este plugin te ayuda a detectar y gestionar enlaces rotos en tu sitio web.', 'mtdev-broken-link-checker-lite'); ?></p>
        <form method="post" action="options.php">
            <?php
            // Generar campos de configuración de WordPress
            settings_fields('mtdev_broken_link_checker_settings_group');
            do_settings_sections('mtdev_broken_link_checker_admin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrar la configuración del plugin
function mtdev_broken_link_checker_register_settings() {
    register_setting('mtdev_broken_link_checker_settings_group', 'mtdev_broken_link_checker_settings');
    add_settings_section('mtdev_broken_link_checker_main_section', __('Configuraciones principales', 'mtdev-broken-link-checker-lite'), 'mtdev_broken_link_checker_section_text', 'mtdev_broken_link_checker_admin');
    add_settings_field('mtdev_broken_link_checker_scan_interval', __('Intervalo de escaneo', 'mtdev-broken-link-checker-lite'), 'mtdev_broken_link_checker_scan_interval_field', 'mtdev_broken_link_checker_admin', 'mtdev_broken_link_checker_main_section');
}
add_action('admin_init', 'mtdev_broken_link_checker_register_settings');

// Texto de la sección de configuración
function mtdev_broken_link_checker_section_text() {
    echo '<p>' . __('Configura el plugin para escanear enlaces rotos.', 'mtdev-broken-link-checker-lite') . '</p>';
}

// Campo para el intervalo de escaneo
function mtdev_broken_link_checker_scan_interval_field() {
    $options = get_option('mtdev_broken_link_checker_settings');
    ?>
    <input type="text" name="mtdev_broken_link_checker_settings[scan_interval]" value="<?php echo isset($options['scan_interval']) ? esc_attr($options['scan_interval']) : ''; ?>" />
    <p class="description"><?php _e('Intervalo de escaneo en horas (por ejemplo: 24 para un escaneo diario)', 'mtdev-broken-link-checker-lite'); ?></p>
    <?php
}

// Función para verificar un enlace
function mtdev_broken_link_checker_check_link($url) {
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'error';
    }

    $status_code = wp_remote_retrieve_response_code($response);
    
    if ($status_code == 404) {
        return 'broken';
    } else {
        return 'ok';
    }
}