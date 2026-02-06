<?php
/**
 * Seed Blocksy theme_mod defaults (colors, buttons, typography) for new sites.
 *
 * We only set values when they are empty, to avoid overwriting site-specific tweaks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default Blocksy theme_mod values used by Panagea.
 */
function panagea_blocksy_default_theme_mods() {
    return array(
        // Global palette
        'paletteColor1' => '#A3C79F',
        'paletteColor2' => '#93827F',
        'paletteColor3' => '#ffffff',
        'paletteColor4' => '#f5f7f8',
        // Accent
        'accentColor'   => '#A3C79F',
        // Links
        'linkInitialColor' => '#A3C79F',
        'linkHoverColor'   => '#49715A',
        'linkVisitedColor' => '#8c9e86',
        // Buttons
        'buttonTextInitialColor'       => '#ffffff',
        'buttonTextHoverColor'         => '#ffffff',
        'buttonTextActiveColor'        => '#ffffff',
        'buttonBackgroundInitialColor' => '#A3C79F',
        'buttonBackgroundHoverColor'   => '#49715A',
        'buttonBackgroundActiveColor'  => '#49715A',
        'buttonBorderInitialColor'     => 'rgba(0,0,0,0)',
        'buttonBorderHoverColor'       => 'rgba(0,0,0,0)',
        'buttonBorderActiveColor'      => 'rgba(0,0,0,0)',
        // Typography
        'fontFamily' => array(
            'family'   => 'Jost',
            'variant'  => '500',
            'fallback' => 'sans-serif'
        ),
        'fontSize'        => '16', // px
        'fontLineHeight'  => '1.6',
        'headingsFontFamily' => array(
            'family'   => 'Jost',
            'variant'  => '700',
            'fallback' => 'sans-serif'
        ),
        'headingH1FontSize'     => '48', // px
        'headingH1LineHeight'   => '1.1',
        'headingH2FontSize'     => '38',
        'headingH2LineHeight'   => '1.15',
        'headingH3FontSize'     => '30',
        'headingH3LineHeight'   => '1.2',
        'headingH4FontSize'     => '24',
        'headingH4LineHeight'   => '1.3',
        'headingH5FontSize'     => '20',
        'headingH5LineHeight'   => '1.35',
        'headingH6FontSize'     => '16',
        'headingH6LineHeight'   => '1.4',
    );
}

/**
 * Seed Blocksy theme_mods. If $force is true, overwrite existing values.
 */
function panagea_blocksy_seed_defaults( $force = false ) {
    if ( ! function_exists( 'blocksy_manager' ) ) {
        return new WP_Error( 'blocksy_inactive', 'Blocksy is not active.' );
    }

    $defaults = panagea_blocksy_default_theme_mods();
    foreach ( $defaults as $key => $value ) {
        $current = get_theme_mod( $key, null );
        if ( $force || null === $current || '' === $current ) {
            set_theme_mod( $key, $value );
        }
    }

    return true;
}

/**
 * Apply defaults on after_setup_theme (runs early, after theme is loaded).
 * Non-destructive: only fills empty theme_mods.
 */
add_action( 'after_setup_theme', function() {
    panagea_blocksy_seed_defaults( false );
} );
