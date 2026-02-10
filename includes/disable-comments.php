<?php
/**
 * Strip comment functionality from the site (front‑end and admin).
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Remove comment/trackback support from all post types. This also hides the
 * “Allow Comments” checkbox (Discussion panel) in the block editor.
 */
function panagea_core_disable_comments_support() {
    $post_types = get_post_types();

    foreach ( $post_types as $post_type ) {
        if ( post_type_supports( $post_type, 'comments' ) ) {
            remove_post_type_support( $post_type, 'comments' );
            remove_post_type_support( $post_type, 'trackbacks' );
        }
    }
}

add_action( 'init', 'panagea_core_disable_comments_support', 100 );


add_action( 'admin_init', 'panagea_core_disable_comments_support' );  // Remove "Comments" support from Post Types (Removes the checkbox in Editor)
add_action( 'admin_menu', 'panagea_core_disable_comments_support' );  // Remove "Comments" from Admin Menu

// Force comments and pings closed everywhere.
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

add_filter( 'comments_array', '__return_empty_array', 10, 2 );  // Hide any existing comments from rendering.

// Remove Comments admin page and block direct access.
function panagea_core_disable_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'panagea_core_disable_comments_admin_menu' );

function panagea_core_disable_comments_admin_redirect() {
    global $pagenow;

    if ( 'edit-comments.php' === $pagenow ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}
add_action( 'admin_init', 'panagea_core_disable_comments_admin_redirect' );

// Remove Comments from the admin bar.
function panagea_core_disable_comments_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'panagea_core_disable_comments_admin_bar', 60 );

// Clean up the dashboard widget.
function panagea_core_disable_comments_dashboard() {
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'panagea_core_disable_comments_dashboard' );

/**
 * Disables XML-RPC to prevent DDoS and Brute Force attacks.
 * Note: This will break plugins like Jetpack or the WP Mobile App 
 * if they rely on XML-RPC.
 */
add_filter('xmlrpc_enabled', '__return_false');

// Remove the X-Pingback HTTP header (advertises the xmlrpc.php endpoint)
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});