<?php 
namespace MHO\Includes\Pages;

class Admin {

    protected $page_slug = 'mho-hub-order';

    /**
     * Construct Function
     */
    public function __construct() {

        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts_styles' ] );
    }

    

    public function register_scripts_styles($hook) {

        if(str_contains($hook, $this->page_slug ) === false){
            return;
        }
        
        $this->load_scripts();
        $this->load_styles();
        $this->remove_jqury();
    }

    public function remove_jqury() {
        wp_dequeue_script('jquery');
    }

    /**
     * Load Scripts
     *
     * @return void
     */
    public function load_scripts() {
        wp_register_script( 'mho-admin-main', MHO_PLUGIN_URL . 'assets/js/main.js', [], rand(), true );

        wp_enqueue_script( 'mho-admin-main' );

        wp_localize_script( 'mho-admin-main', 'mhoAdminLocalizer', [
            'adminUrl'  => admin_url( '/' ),
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'apiUrl'    => home_url( '/wp-json' ),
        ] );
    }

    public function load_styles() {
        wp_register_style( 'mho-admin-style', MHO_PLUGIN_URL . 'assets/css/main.css', array(), rand(), 'all' );

        wp_enqueue_style( 'mho-admin-style' );
    }

    /**
     * Register Menu Page
     * @since 1.0.0
     */
    public function admin_menu() {
        global $submenu;

        $capability = 'manage_options';

        add_menu_page(
            __( 'Hub Order', 'mho' ),
            __( 'Hub Order', 'mho' ),
            $capability,
            $this->page_slug,
            [ $this, 'menu_page_template' ],
            'dashicons-buddicons-replies'
        );

        if( current_user_can( $capability )  ) {
            $submenu[ $this->page_slug ][] = [ __( 'Hub Order', 'mho' ), $capability, 'admin.php?page=' . $this->page_slug . '#/'];
            $submenu[ $this->page_slug ][] = [ __( 'Settings', 'mho' ), $capability, 'admin.php?page=' . $this->page_slug . '#/settings' ];
        }

        // add_action( 'load-' . $hook, [ $this, 'init_hooks' ] );
    }


    /**
     * Render Admin Page
     * @since 1.0.0
     */
    public function menu_page_template() {
        echo '<div class="wrap">
            <h2>Hub Order</h2>
            <div id="mho-hub-order-app"></div>
        </div>';
    }

}
