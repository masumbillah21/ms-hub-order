<?php 
/**
 * Plugin Name:       MS Hub Order
 * Plugin URI:        http://masum-billah.com
 * Description:       To connect store through API To Update Orders
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            H M Masum Billah
 * Author URI:        http://masum-billah.com
 * Text Domain:       mho
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }


require_once __DIR__ . '/autoload.php';

use MHO\Includes\Api\Api;
use MHO\Includes\Pages\Admin;
use MHO\Includes\Hub\HubWebHook;
use MHO\Includes\Database\HubDatabase;

final class MHOInit {

    /**
     * Define Plugin Version
     */
    const VERSION = '1.0.0';

    /**
     * Construct Function
     */
    public function __construct() {
        $this->plugin_constants();
        add_action( 'init', [$this, 'mho_textdomain_load'] );
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [$this, 'mho_api_settings'] );
    }

    /**
     * Plugin Constants
     * @since 1.0.0
     */
    public function plugin_constants() {
        define( 'MHO_VERSION', self::VERSION );
        define( 'MHO_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'MHO_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
        define( 'MHO_NONCE', 'b?le*;K7.T2jk_*(+3&[G[xAc8O~Fv)2T/Zk9N:GKBkn$piN0.N%N~X91VbCn@.4' );
    }
    
 
/**
 * Load plugin textdomain.
 */
    public function mho_textdomain_load() {
        load_plugin_textdomain( 'mho', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
    }

    public function mho_api_settings( array $links ) {
        $url = get_admin_url() . "admin.php?page=mho-hub-order";
        $settings_link = '<a href="' . $url . '">' . __('Settings', 'waw') . '</a>';
        $links[] = $settings_link;
        return $links;
    }
    

    /**
     * Singletone Instance
     * @since 1.0.0
     */
    public static function init() {
        static $instance = false;

        if( !$instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * On Plugin Activation
     * @since 1.0.0
     */
    public function activate() {
        $is_installed = get_option( 'mho_is_installed' );
        HubDatabase::create_table();

        if( ! $is_installed ) {
            update_option( 'mho_is_installed', time() );
        }

        update_option( 'mho_is_installed', MHO_VERSION );
    }

    /**
     * On Plugin De-actiavtion
     * @since 1.0.0
     */
    public function deactivate() {

        HubDatabase::drop_table();

        if( get_option( 'mho_is_installed' ) ) {
            delete_option( 'mho_is_installed' );
        }
    }

    /**
     * Init Plugin
     * @since 1.0.0
     */
    public function init_plugin() {
        new Admin();
        new Api();
        new HubWebHook();
    }

}

/**
 * Initialize Main Plugin
 * @since 1.0.0
 */
function mho_init() {
    return MHOInit::init();
}
mho_init();
