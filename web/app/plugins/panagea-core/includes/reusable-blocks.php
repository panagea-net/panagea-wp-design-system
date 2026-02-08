<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Seed locale-specific reusable blocks so design updates propagate from the plugin.
 *
 * Structure is flexible so new reusable blocks can be added by declaring another
 * entry in the $blocks map below (base_slug + files per locale).
 */
function panagea_seed_reusable_blocks() {
    if ( ! post_type_exists( 'wp_block' ) ) {
        return;
    }

    // List reusable blocks to seed. Add more entries as needed.
    $blocks = array(
        'pillars' => array(
            'version'   => '0.1.0',
            'base_slug' => 'pillars', // final slug => panagea-{$base_slug}-{$locale}
            'categories'=> array( 'panagea-general' ),
            'titles'    => array(
                'en_US' => __( 'Pillars Section (EN)', 'panagea-core' ),
                'es_ES' => __( 'Sección Pilares (ES)', 'panagea-core' ),
                'it_IT' => __( 'Sezione Pilastri (IT)', 'panagea-core' ),
            ),
            'files'     => array(
                'en_US' => 'assets/reusable-blocks/pillars-section/pillars-section-en.html',
                'es_ES' => 'assets/reusable-blocks/pillars-section/pillars-section-es.html',
                'it_IT' => 'assets/reusable-blocks/pillars-section/pillars-section-it.html',
            ),
        ),
    );

    foreach ( $blocks as $key => $block ) {
        $version    = $block['version'];
        $option_key = 'panagea_reusable_block_version_' . $key;
        $stored     = get_option( $option_key );
        $should_seed = ( $stored !== $version );

        foreach ( $block['files'] as $locale => $relative_path ) {
            $slug     = panagea_build_reusable_slug( $block['base_slug'], $locale );
            $existing = get_page_by_path( $slug, OBJECT, 'wp_block' );
            if ( ! $existing ) {
                $should_seed = true;
            }
        }

        if ( ! $should_seed ) {
            continue;
        }

        foreach ( $block['files'] as $locale => $relative_path ) {
            $path = PANAGEA_CORE_PLUGIN_PATH . $relative_path;
            if ( ! file_exists( $path ) ) {
                continue;
            }

            $slug    = panagea_build_reusable_slug( $block['base_slug'], $locale );
            $title   = isset( $block['titles'][ $locale ] ) ? $block['titles'][ $locale ] : ucwords( $block['base_slug'] );
            $content = file_get_contents( $path );

            $categories = isset( $block['categories'] ) ? (array) $block['categories'] : array();

            panagea_upsert_reusable_block( $slug, $title, $content, $locale, $categories );
        }

        update_option( $option_key, $version );
    }
}
add_action( 'init', 'panagea_seed_reusable_blocks', 20 );

/**
 * Build a slug that stays short but encodes locale.
 */
function panagea_build_reusable_slug( $base_slug, $locale ) {
    // Prefer explicit map so we can keep slugs tidy.
    $map = array(
        'en_US' => 'en',
        'es_ES' => 'es',
        'it_IT' => 'it',
    );

    $suffix = isset( $map[ $locale ] ) ? $map[ $locale ] : strtolower( substr( $locale, 0, 2 ) );

    return 'panagea-' . sanitize_title( $base_slug ) . '-' . $suffix;
}

/**
 * Create or update a reusable block post with locale metadata.
 */
function panagea_upsert_reusable_block( $slug, $title, $content, $locale, $categories = array() ) {
    $post_data = array(
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_type'    => 'wp_block',
        'post_status'  => 'publish',
        'post_content' => $content,
    );

    $existing = get_page_by_path( $slug, OBJECT, 'wp_block' );

    if ( $existing ) {
        $post_data['ID'] = $existing->ID;
        $post_id         = wp_update_post( $post_data );
    } else {
        $post_id = wp_insert_post( $post_data );
    }

    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }

    update_post_meta( $post_id, '_panagea_locale', $locale );

    // Add to pattern categories so it surfaces under Panagea Corporate in the inserter.
    if ( taxonomy_exists( 'wp_pattern_category' ) && ! empty( $categories ) ) {
        wp_set_object_terms( $post_id, $categories, 'wp_pattern_category', false );
    }

    return $post_id;
}
