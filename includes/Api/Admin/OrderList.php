<?php
namespace MHO\Includes\Api\Admin;

use MHO\Includes\Database\HubDatabase;
use WP_REST_Controller;

class OrderList extends WP_REST_Controller  {

    protected $namespace;
    protected $rest_base;

    protected $current_user;

    protected $create_perssions;

    public function __construct() {
        $this->namespace = 'mho/v1';
        $this->rest_base = 'order-list';
        $this->current_user = wp_get_current_user();
        $this->create_perssions = [
            'administrator',
            'customer_support',
        ];
    }

    /**
     * Register Routes
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [ $this, 'get_items_permission_check' ],
                    'args'                => $this->get_collection_params()
                ],
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_items' ],
                    'permission_callback' => [ $this, 'create_items_permission_check' ],
                    'args'                => $this->get_endpoint_args_for_item_schema(true )
                ]
            ]
        );
    }
    /**
     * Get items response
     */
    public function get_items( $request ) {
        
        $data = HubDatabase::get_all();
        
        return rest_ensure_response( $data );
    }

    /**
     * Get items permission check
     */
    public function get_items_permission_check( $request ) {
        if ( array_intersect( $this->create_perssions, $this->current_user->roles ) ) {
            return true;
        }

        return false;
    }

    /**
     * Create item response
     */
    public function create_items( $request ) {

        HubDatabase::update( $request['order_id'], $request['hub_notes'], $request['order_status'] );
        $res = $this->send_data_store($request['order_id'], $request['hub_notes'], $request['order_status']);

        $response = [
            'data' => $res
        ];

        return rest_ensure_response( $response );
        
    }

    private function send_data_store( $order_id, $hub_notes, $order_status ) {
        $token = get_option('mho_store_token_key', true);
        $url = get_option( 'mho_webhook_url', true );

        $body = [
            'order_id' => $order_id,
            'hub_notes' => $hub_notes,
            'order_status' => $order_status
        ];

        $origin = str_replace( ['https://', 'http://'], '', get_site_url() );

        $response = wp_remote_post( $url, array(
            'method'  => 'POST',
            'body'    => json_encode($body),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Token' => $token,
                'Origin' => $origin,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return $error_message;
        } else {
            $response_body = wp_remote_retrieve_body( $response );
            return $response_body;
        }

    }

    /**
     * Create item permission check
     */
    public function create_items_permission_check( $request ) {
        if ( array_intersect( $this->create_perssions, $this->current_user->roles ) ) {
            return true;
        }

        return false;
    }

    /**
     * Retrives the query parameters for the items collection
     */
    public function get_collection_params() {
        return [];
    }

}