<?php
/**
 * Typography & Font Loading Logic
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load Jost Google fonts locally
 */
function panagea_core_load_local_fonts() {
    // Register the style
    wp_register_style(
        'panagea-core-local-fonts', // Handle
        PANAGEA_CORE_PLUGIN_URL . 'assets/css/fonts.css', // URL to the file
        array(), // Dependencies
        '1.0.0' // Version (change this if you update the font file)
    );

    // Enqueue it
    wp_enqueue_style( 'panagea-core-local-fonts' );
}

add_action( 'wp_enqueue_scripts', 'panagea_core_load_local_fonts', 20 );          // Load on Frontend

/**
 * Preload font files for better performance
 */
function panagea_core_preload_fonts() {
    // Define the path to your fonts relative to the plugin URL
    $font_url_regular = PANAGEA_CORE_PLUGIN_URL . 'assets/fonts/Jost-Regular.ttf';
    $font_url_bold    = PANAGEA_CORE_PLUGIN_URL . 'assets/fonts/Jost-Bold.ttf';
    // Output the HTML tags
    // NOTE: 'crossorigin' is MANDATORY for fonts, even if self-hosted! Without it, the browser might download the font twice.
    ?>
    <link rel="preload" href="<?php echo esc_url( $font_url_regular ); ?>" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $font_url_bold ); ?>" as="font" type="font/ttf" crossorigin>
    <?php
}
add_action( 'wp_head', 'panagea_core_preload_fonts', 5 );     // Hook into the <head>