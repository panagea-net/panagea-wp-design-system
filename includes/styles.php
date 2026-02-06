<?php
/**
 * Typography & Font Loading Logic
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue global styles for the Panagea design system
 */
function panagea_core_enqueue_overrides() {
    wp_enqueue_style( 
        'panagea-core-global-overrides', 
        PANAGEA_CORE_PLUGIN_URL . 'assets/css/global-overrides.css', 
        array(), //Dependencies
        '1.0.0' 
    );
}
add_action( 'wp_enqueue_scripts', 'panagea_core_enqueue_overrides', 20 ); // Load after the main styles (priority 20)

function panagea_core_enqueue_global_assets() {
    wp_enqueue_style( 
        'panagea-core-global-styles', // Handle name
        PANAGEA_CORE_PLUGIN_URL . 'assets/css/style.css', // URL
        array(), //Dependencies
        '1.0.0'
    );
}
add_action( 'wp_enqueue_scripts', 'panagea_core_enqueue_global_assets', 20 ); // Load after the main styles (priority 20)

/**
 * Last-mile overrides for Blocksy theme variables.
 * Ensures comment reply buttons and other components that read --theme-button-* vars
 * use the Panagea palette even if Blocksy outputs its defaults later.
 */
function panagea_core_inline_theme_vars() {
    ?>
    <style id="panagea-core-theme-vars">
        :root {
            --theme-button-background-initial-color: var(--pan-color-primary-hover);
            --theme-button-background-hover-color: var(--pan-color-primary-hover);
            --theme-button-background-active-color: var(--pan-color-primary-hover);
        }
    </style>
    <?php
}
// add_action( 'wp_head', 'panagea_core_inline_theme_vars', 999 );
