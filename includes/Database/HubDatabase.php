<?php 
namespace MHO\Includes\Database;
use MHO\Includes\Hub\ApiDataClass;

class HubDatabase {
    private static $table_name = 'mho_orders';
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id mediumint(9) NOT NULL,
            customer_name varchar(100) NOT NULL,
            customer_email varchar(100) NOT NULL,
            order_status varchar(100) NOT NULL,
            order_date date NOT NULL,
            shipping_date date NOT NULL,
            order_notes text NULL,
            hub_notes text NULL,
            created_at datetime NOT NULL DEFAULT current_timestamp(),
            updated_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get($order_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_id = %d", $order_id);
        return $wpdb->get_row( $sql );
    }

    public static function get_all() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $sql = "SELECT * FROM $table_name";
        // return as associative array
        return $wpdb->get_results( $sql, 'ARRAY_A' );
    }

    public static function insert( ApiDataClass $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $wpdb->insert(
            $table_name,
            array(
                'order_id' => $data->order_id,
                'customer_name' => $data->customer_name,
                'customer_email' => $data->customer_email,
                'order_status' => $data->order_status,
                'order_date' => $data->order_date,
                'shipping_date' => $data->shipping_date,
                'order_notes' => $data->order_notes,
            )
        );
    }

    public static function update( $order_id, $hub_notes, $order_status ) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;

        $wpdb->update(
            $table_name,
            array(
                'order_status' => $order_status,
                'hub_notes' => $hub_notes,
            ),
            array( 'order_id' => $order_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );
    }

    public static function delete( $order_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $sql = $wpdb->prepare("DELETE FROM $table_name WHERE order_id = %d", $order_id);
        $wpdb->query( $sql );
    }

    public static function drop_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );
    }
}
