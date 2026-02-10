<?php
/**
 * Plugin Name: Panagea Core Functionality
 * Description: Shared design system, patterns, and logic for all Panagea sites.
 * Version: 1.0.0
 * Author: Fabio Sanvido
 */

// Security: Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants for paths (useful for loading assets later)
define( 'PANAGEA_CORE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PANAGEA_CORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load Modules
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/typography.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/styles.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/patterns.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/reusable-blocks.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/blocksy-defaults.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/cli.php';
require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/disable-comments.php';
// require_once PANAGEA_CORE_PLUGIN_PATH . 'includes/woocommerce-tweaks.php'; // Future expansion

register_activation_hook( __FILE__, 'panagea_seed_reusable_blocks' );
