<?php
/**
 * WP-CLI commands for Panagea Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    /**
     * Apply Panagea defaults into the current theme's "Additional CSS".
     *
     * ## EXAMPLES
     *
     *     wp panagea-core apply-defaults
     *     wp panagea-core apply-defaults --force-blocksy
     */
    WP_CLI::add_command( 'panagea-core apply-defaults', function( $args, $assoc_args ) {
        $css_file = PANAGEA_CORE_PLUGIN_PATH . 'assets/css/global-overrides.css';

        if ( ! file_exists( $css_file ) ) {
            WP_CLI::error( 'global-overrides.css not found.' );
        }

        $contents = file_get_contents( $css_file );
        if ( false === $contents ) {
            WP_CLI::error( 'Unable to read global-overrides.css' );
        }

        $marker_start = '/* Panagea Core Defaults START */';
        $marker_end   = '/* Panagea Core Defaults END */';

        $existing_css = wp_get_custom_css();
        // Strip previous block if present
        $pattern      = '/' . preg_quote( $marker_start, '/' ) . '.*?' . preg_quote( $marker_end, '/' ) . '/s';
        $clean_css    = preg_replace( $pattern, '', $existing_css );
        $clean_css    = trim( $clean_css );

        $new_block = "\n\n{$marker_start}\n{$contents}\n{$marker_end}\n";
        $final_css = $clean_css . $new_block;

        $result = wp_update_custom_css_post( $final_css );

        if ( is_wp_error( $result ) ) {
            WP_CLI::error( $result->get_error_message() );
        }

        // Optional: force-reset Blocksy theme_mods
        if ( isset( $assoc_args['force-blocksy'] ) ) {
            if ( ! function_exists( 'panagea_blocksy_seed_defaults' ) ) {
                WP_CLI::warning( 'Blocksy defaults seeder not available.' );
            } else {
                $seed_result = panagea_blocksy_seed_defaults( true );
                if ( is_wp_error( $seed_result ) ) {
                    WP_CLI::warning( $seed_result->get_error_message() );
                } else {
                    WP_CLI::log( 'Blocksy theme_mods reset to Panagea defaults.' );
                }
            }
        }

        WP_CLI::success( 'Panagea defaults applied to Additional CSS for the active theme.' );
    } );

    /**
     * Seed Blocksy theme_mod defaults without touching Additional CSS.
     *
     * ## OPTIONS
     *
     * [--force]
     *    Overwrite existing values instead of filling only empty ones.
     *
     * ## EXAMPLES
     *
     *     wp panagea-core blocksy-defaults
     *     wp panagea-core blocksy-defaults --force
     */
    WP_CLI::add_command( 'panagea-core blocksy-defaults', function( $args, $assoc_args ) {
        if ( ! function_exists( 'panagea_blocksy_seed_defaults' ) ) {
            WP_CLI::error( 'Blocksy defaults seeder not available.' );
        }

        $force = isset( $assoc_args['force'] );
        $result = panagea_blocksy_seed_defaults( $force );

        if ( is_wp_error( $result ) ) {
            WP_CLI::error( $result->get_error_message() );
        }

        WP_CLI::success( $force
            ? 'Blocksy theme_mods reset to Panagea defaults.'
            : 'Blocksy theme_mods filled where empty.' );
    } );

    /**
     * Remove the Panagea marker-wrapped block from Additional CSS.
     *
     * ## EXAMPLES
     *
     *     wp panagea-core clear-additional-css
     */
    WP_CLI::add_command( 'panagea-core clear-additional-css', function() {
        $marker_start = '/* Panagea Core Defaults START */';
        $marker_end   = '/* Panagea Core Defaults END */';

        $existing_css = wp_get_custom_css();
        $pattern      = '/' . preg_quote( $marker_start, '/' ) . '.*?' . preg_quote( $marker_end, '/' ) . '/s';

        $clean_css = preg_replace( $pattern, '', $existing_css, -1, $count );
        $clean_css = trim( $clean_css );

        // If nothing changed, report and exit.
        if ( 0 === $count ) {
            WP_CLI::success( 'No Panagea marker block found in Additional CSS.' );
            return;
        }

        $result = wp_update_custom_css_post( $clean_css );
        if ( is_wp_error( $result ) ) {
            WP_CLI::error( $result->get_error_message() );
        }

        WP_CLI::success( 'Removed Panagea marker block from Additional CSS.' );
    } );
}
