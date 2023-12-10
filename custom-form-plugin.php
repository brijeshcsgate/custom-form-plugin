<?php
/*
Plugin Name: Custom Form Plugin
Description: A simple plugin for creating a form and adding values to the database.
Version: 1.0
Author: Your Name
*/

// Enqueue styles and scripts
function custom_form_enqueue_scripts() {
    wp_enqueue_style('custom-form-styles', plugins_url('css/style.css', __FILE__));
    wp_enqueue_script('custom-form-script', plugins_url('js/script.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'custom_form_enqueue_scripts');

// Shortcode to display the form
function custom_form_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/form-template.php');
    return ob_get_clean();
}
add_shortcode('custom_form', 'custom_form_shortcode');

// Process form submission and save to the database
function custom_form_process() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
        // Sanitize and validate form data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);

        // Save to the database (assuming you have a custom table named 'custom_form_entries')
        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_form_entries';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
            ),
            array('%s', '%s')
        );
    }
}

function custom_form_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_form_entries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'custom_form_create_table');

add_action('init', 'custom_form_process');
