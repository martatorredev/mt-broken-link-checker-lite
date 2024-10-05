<?php
<?php
/**
 * Plugin Name: MT Dev Broken Link Checker Lite
 * Plugin URI:  https://github.com/martatorredev/mt-broken-link-checker-lite
 * Description: A lightweight plugin to detect and manage broken links on your WordPress site.
 * Version:     1.0.0
 * Author:      Marta Torre
 * Author URI:  https://martatorre.dev/en/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mtdev-broken-link-checker-lite
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load text domain for translations
function mtdev_broken_link_checker_load_textdomain() {
    load_plugin_textdomain('mtdev-broken-link-checker-lite', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'mtdev_broken_link_checker_load_textdomain');

// Function to add menu in the admin panel
function mtdev_broken_link_checker_add_admin_menu() {
    add_menu_page(
        'MT Dev Broken Link Checker Lite', // Menu name
        'Broken Links',                     // Menu title
        'manage_options',                   // Capability required to view
        'mtdev_broken_link_checker_admin',  // Menu slug
        'mtdev_broken_link_checker_admin_page', // Function to display the page
        'dashicons-editor-unlink',          // Menu icon
        80                                   // Menu position
    );
}
add_action('admin_menu', 'mtdev_broken_link_checker_add_admin_menu');

// Function to display the admin page of the plugin
function mtdev_broken_link_checker_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('MT Dev Broken Link Checker Lite', 'mtdev-broken-link-checker-lite'); ?></h1>
        <p><?php _e('This plugin helps you detect and manage broken links on your website.', 'mtdev-broken-link-checker-lite'); ?></p>
        <form method="post" action="options.php">
            <?php
            // Generate WordPress settings fields
            settings_fields('mtdev_broken_link_checker_settings_group');
            do_settings_sections('mtdev_broken_link_checker_admin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register the plugin settings
function mtdev_broken_link_checker_register_settings() {
    register_setting('mtdev_broken_link_checker_settings_group', 'mtdev_broken_link_checker_settings');
    add_settings_section('mtdev_broken_link_checker_main_section', __('Main Settings', 'mtdev-broken-link-checker-lite'), 'mtdev_broken_link_checker_section_text', 'mtdev_broken_link_checker_admin');
    add_settings_field('mtdev_broken_link_checker_scan_interval', __('Scan Interval', 'mtdev-broken-link-checker-lite'), 'mtdev_broken_link_checker_scan_interval_field', 'mtdev_broken_link_checker_admin', 'mtdev_broken_link_checker_main_section');
}
add_action('admin_init', 'mtdev_broken_link_checker_register_settings');

// Section text for settings
function mtdev_broken_link_checker_section_text() {
    echo '<p>' . __('Configure the plugin to scan for broken links.', 'mtdev-broken-link-checker-lite') . '</p>';
}

// Field for scan interval
function mtdev_broken_link_checker_scan_interval_field() {
    $options = get_option('mtdev_broken_link_checker_settings');
    ?>
    <input type="text" name="mtdev_broken_link_checker_settings[scan_interval]" value="<?php echo isset($options['scan_interval']) ? esc_attr($options['scan_interval']) : ''; ?>" />
    <p class="description"><?php _e('Scan interval in hours (e.g., 24 for a daily scan)', 'mtdev-broken-link-checker-lite'); ?></p>
    <?php
}

// Function to check a link
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
