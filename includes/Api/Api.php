<?php
namespace MHO\Includes\Api;

use WP_REST_Controller;
use MHO\Includes\Hub\HubWebHook;
use MHO\Includes\Api\Admin\OrderList;
use MHO\Includes\Api\Admin\HubSettings;
use MHO\Includes\Api\Admin\StoreSettings;

/**
 * Rest API Handler
 */
class Api extends WP_REST_Controller {

    /**
     * Construct Function
     */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Register API routes
     */
    public function register_routes() {
        ( new StoreSettings() )->register_routes();
        ( new HubSettings() )->register_routes();
        ( new HubWebHook() )->register_routes();
        ( new OrderList() )->register_routes();
    }

}