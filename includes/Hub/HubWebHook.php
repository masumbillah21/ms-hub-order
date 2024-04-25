<?php
namespace MHO\Includes\Hub;

use MHO\Includes\Database\HubDatabase;
use WP_REST_Controller;

class HubWebHook extends WP_REST_Controller  {

    protected $namespace;
    protected $rest_base;

    protected $token_key;

    protected $whitelisted_domains;

            

    public function __construct() {
        $this->namespace = 'mho/v1';
        $this->rest_base = 'hub-order';
        $this->token_key = get_option( 'mho_token_key', true );
        $this->whitelisted_domains = explode(',', get_option('mho_whitelisted_domains', true));
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

        if(isset($request->get_params()['order_id'])) {

            $order_id = $request->get_param('order_id');
            $data = HubDatabase::get( $order_id );

            $response = [
                'data' => $data,
            ];
            
        } else {

            $data = HubDatabase::get_all();
            $response = [
                'data' => $data,
            ];
        }

        return rest_ensure_response( $response );
    }

    /**
     * Get items permission check
     */
    public function get_items_permission_check( $request ) {
        $origin = $request->get_header('Origin');
        $token = $request->get_header('token');
       
        return in_array($origin, $this->whitelisted_domains) && $token == $this->token_key;
    }

    /**
     * Create item response
     */
    public function create_items( $request ) {
        $order_id = $request->get_param( 'order_id' );
        $order_status = $request->get_param( 'order_status' );

        if($order =  HubDatabase::get( $order_id ) ) {
            HubDatabase::update( $order_id, $order->hub_notes, $order_status );
        } else {
            $customer_name = $request->get_param( 'customer_name' );
            $customer_email = $request->get_param( 'customer_email' );
            $order_date = $request->get_param( 'order_date' );
            $shipping_date = $request->get_param( 'shipping_date' );
            $shipping_info = json_encode($request->get_param( 'shipping_info' ));
            $order_notes = $request->get_param( 'order_notes' ); 

        $data = new ApiDataClass( $order_id, $customer_name, $customer_email, $order_status, $order_date, $shipping_date, $shipping_info, $order_notes );
            HubDatabase::insert( $data );
        }

        $response = [
            'response' => 'Order created successfully',
        ];

        return rest_ensure_response( $response );
        
    }

    /**
     * Create item permission check
     */
    public function create_items_permission_check( $request ) {

        $origin = $request->get_header('Origin');
        $token = $request->get_header('token');

        return in_array($origin, $this->whitelisted_domains) && $token == $this->token_key;

    }

    /**
     * Retrives the query parameters for the items collection
     */
    public function get_collection_params() {
        return [];
    }

}