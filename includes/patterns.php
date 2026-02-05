<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function panagea_register_patterns() {

    // 1. Register a Category (so your patterns appear in their own tab)
    register_block_pattern_category(
        'panagea-general',
        array( 'label' => __( 'Panagea Corporate', 'panagea-core' ) )
    );

    register_block_pattern(
        'panagea/panagea-pillars-section',
        array(
            'title'       => __( 'Corporate Pillars Section', 'panagea-core' ),
            'description' => _x( 'Main pillars section with icons and descriptions', 'Pattern description', 'panagea-core' ),
            'categories'  => array( 'panagea-general' ),
            'content'     => file_get_contents( PANAGEA_CORE_PLUGIN_PATH . 'patterns/pillars-section.php' ),
        )
    );
}
add_action( 'init', 'panagea_register_patterns' );