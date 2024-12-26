<?php
class ControllerProductMailOrder extends Controller {
    // catalog/model/product/order/addOrderHistory/before
    public function index(&$route, &$args) {
        if (isset($args[0])) {
            $order_id = $args[0];
        } else {
            $order_id = 0;
        }

        if (isset($args[1])) {
            $status = $args[1];
        } else {
            $status = '';
        }

        if (isset($args[2])) {
            $comment = $args[2];
        } else {
            $comment = '';
        }

        if (isset($args[3])) {
            $notify = $args[3];
        } else {
            $notify = '';
        }
    }
}
