<?php

namespace MHO\Includes\Hub;

class ApiDataClass
{

    public $order_id;
    public $customer_name;
    public $customer_email;
    public $order_status;
    public $order_date;
    public $shipping_date;
    public $shipping_info;
    public $order_notes;

    public function __construct( $order_id, $customer_name, $customer_email, $order_status, $order_date, $shipping_date, $shipping_info, $order_notes )
    {
        $this->order_id = $order_id;
        $this->customer_name = $customer_name;
        $this->customer_email = $customer_email;
        $this->order_status = $order_status;
        $this->order_date = $order_date;
        $this->shipping_date = $shipping_date;
        $this->shipping_info = $shipping_info;
        $this->order_notes = $order_notes;
    }

}