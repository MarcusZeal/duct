<?php
/**
 * Plugin Name: Digitile Use Case
 * Plugin URI: https://www.eternalworks.com/
 * Description: This powers the complex use-case section shortcode. Example: [ust id=sales].
 * Version: 1.5.0
 * Author: Eternal Works
 * Author URI: https://www.eternalworks.com/
 * Text Domain: uct
 */

// Include ACF
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__FILE__) . '/includes/acf-addons.php';

if (!class_exists('acf')) {
    include_once plugin_dir_path(__FILE__) . 'acf/acf.php';
    add_filter('acf/settings/path', 'uct_acf_settings_path');
    add_filter('acf/settings/dir', 'uct_acf_settings_dir');
    add_filter('acf/settings/show_admin', '__return_true');
    add_filter('acf/settings/save_json', 'uct_acf_json_save_point');
    add_filter('acf/settings/load_json', 'uct_acf_json_load_point');
    add_filter('site_transient_update_plugins', 'uct_stop_acf_update_notifications', 11);
} else {
    add_filter('acf/settings/load_json', 'uct_acf_json_load_point');
}

// Link to Use Case ACF
include plugin_dir_path(__FILE__) . '/includes/use-case-acf.php';
include plugin_dir_path(__FILE__) . '/includes/use-case-card-acf.php';
include plugin_dir_path(__FILE__) . '/DigitileUseCase.php';

// function uct_load_plugin_css()
// {
//     //  wp_enqueue_style('style2', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');

// }
// add_action('wp_enqueue_scripts', 'uct_load_plugin_css');

$instance = new DigitileUseCase;

add_action('init', function () use ($instance) {
    $instance->addCustomUseCase('Use_Case_Card', 'Use Case Cards', 'Use Case Card');
    $instance->addCustomUseCase('use_case', 'Use Cases', 'Use Case');
});

add_shortcode("uct_cards", function ($atts) use ($instance) {
    $skips = shortcode_atts(['skip' => null], $atts);
    return $instance->getCards(1, $skips['skip']);
});

add_action('wp_enqueue_scripts', function () use ($instance) {return $instance->scripts();});

add_action( 'init', 'usecasecard_add_new_image_size' );
function usecasecard_add_new_image_size() {
    add_image_size( 'use_case_card', 350, 220, true ); //use case card
}

include_once plugin_dir_path(__FILE__) . '/includes/custom-use-case.php';

function cards_ajax()
{
    $instance = new DigitileUseCase;

    $page = (int) $_GET['page'] ?? 1;
    $skip = $_GET['skip'] ?? null;

    echo json_encode(['cards' => $instance->getCards($page, $skip), 'hasMore' => $instance->hasMore]);

    exit;
}

add_action('wp_ajax_load_uct_cards', 'cards_ajax');
add_action('wp_ajax_nopriv_load_uct_cards', 'cards_ajax');
