<?php
/**
 * Plugin Name: G Virtual Try On
 * Description: Product Try On give vitrualy environment to user for try eyeglasses virtualy from product page.
 * Version: 1.0 
 * Author: Girirajsinh Gohil
 * Author URI: https://gvtechnolab.in
 * License: GPL2
 * Text Domain: gvtryon
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

define('PLUGIN_TEXT_DOMAIN', 'gvtryon');
define('GVTRYON_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GVTRYON_PLUGIN_PATH', dirname(__FILE__));

function gvtryon_activation()
{
}

register_activation_hook(__FILE__, 'gvtryon_activation');

function gvtryon_deactivation()
{
}

register_deactivation_hook(__FILE__, 'gvtryon_deactivation');

if (!function_exists('gvtryon_constructor')) {
    /**
     * Bootstrap function; loads all required dependencies and start the process
     *
     * @return void
     * @since 1.0.0
     */
    function gvtryon_constructor()
    {

        load_plugin_textdomain('gvtryon', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        /**
         * add Settings/Information to admin page
         */
        require_once GVTRYON_PLUGIN_PATH . '/inc/plugin_settings.php';
        /**
         * Metabox 
         */
        require_once GVTRYON_PLUGIN_PATH . '/inc/product_metabox.php';


        add_action('wp_enqueue_scripts', 'gvtryon_enqueue_scripts');
        add_action('admin_enqueue_scripts', 'gvtryon_admin_enqueue_scripts');

        /**
         * add tryon button to product details page 
         */
        require_once GVTRYON_PLUGIN_PATH . '/inc/tryon_button.php';
    }
}
add_action('gvtryon_init', 'gvtryon_constructor');

function gvtryon_enqueue_scripts()
{
    wp_enqueue_style('gvtryon_styles', GVTRYON_PLUGIN_URL . 'assets/styles/styles.css');
    wp_enqueue_style('gvtryon_camera_styles', GVTRYON_PLUGIN_URL . 'assets/styles/camera-style.css');

    wp_enqueue_script('tfjs-core-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tf-core2_6_0.min.js', array(), '', false);
    wp_enqueue_script('tfjs-backend-cpu-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-backend-cpu.js', array(), '', false);
    wp_enqueue_script('tfjs-converter-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-converter2_6_0.min.js', array(), '', false);
    wp_enqueue_script('tfjs-backend-webgl-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-backend-webgl2_6_0.min.js', array(), '', false);

    wp_enqueue_script('facemeshmodels-js', GVTRYON_PLUGIN_URL . 'assets/js/facemesh.js', array(), '', false);

    wp_enqueue_script('adapter-min-js', GVTRYON_PLUGIN_URL . 'assets/js/camera/adapter.min.js', array('jquery'), '', true);
    wp_enqueue_script('screenfull-js', GVTRYON_PLUGIN_URL . 'assets/js/camera/screenfull.min.js', array('jquery'), '', true);
    wp_enqueue_script('html2canvas_min-js', GVTRYON_PLUGIN_URL . 'assets/js/html2canvas.min.js', array(), '', true);
    wp_enqueue_script('canvas2image-js', GVTRYON_PLUGIN_URL . 'assets/js/canvas2image.js', array(), '', true);
    wp_enqueue_script('camera-main-js', GVTRYON_PLUGIN_URL . 'assets/js/camera/main.js', array('jquery'), '', true);
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
}

// add admin Script
function gvtryon_admin_enqueue_scripts()
{
    wp_enqueue_style('admin_gvtryon_styles', GVTRYON_PLUGIN_URL . 'assets/styles/adminStyle.css');

    wp_enqueue_script('tfjs-core-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tf-core2_6_0.min.js', array(), '', false);
    wp_enqueue_script('tfjs-backend-cpu-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-backend-cpu.js', array(), '', false);
    wp_enqueue_script('tfjs-converter-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-converter2_6_0.min.js', array(), '', false);
    wp_enqueue_script('tfjs-backend-webgl-js', GVTRYON_PLUGIN_URL . 'assets/js/tensorflow/tfjs-backend-webgl2_6_0.min.js', array(), '', false);

    wp_enqueue_script('facemeshmodels-js', GVTRYON_PLUGIN_URL . 'assets/js/facemesh.js', array(), '', false);
    wp_enqueue_script('admin-canvas-js', GVTRYON_PLUGIN_URL . 'assets/js/adminScript.js', array('jquery'), '', true);
}

if (!function_exists('gvtryon_install')) {
    /**
     * Performs pre-flight checks, and gives green light for plugin bootstrap
     *
     * @return void
     * @since 1.0.0
     */
    function gvtryon_install()
    {

        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        if (!function_exists('WC')) {
            add_action('admin_notices', 'gvtryon_install_woocommerce_admin_notice');
        } else {
            do_action('gvtryon_init');
        }
    }
}
add_action('plugins_loaded', 'gvtryon_install', 11);

if (!function_exists('gvtryon_install_woocommerce_admin_notice')) {
    /**
     * Shows admin notice when plugin is activated without WooCommerce
     *
     * @return void
     * @since 2.0.0
     */
    function gvtryon_install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p>
                <?php echo esc_html('G Virtual Try On ' . __('is enabled but not effective. It requires WooCommerce to work.', 'veyewtryon')); ?>
            </p>
        </div>
        <?php
    }
}