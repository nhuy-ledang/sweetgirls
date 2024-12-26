<?php namespace Modules\Order\Traits;

/**
 * Trait CommonTrait
 *
 * @package Modules\Core\Traits
 */
trait OrderStatusTrait {
    public function getStatusNameAttribute() {
        $list = [
            'pending'    => $this->locale == 'en' ? 'Pending' : 'Chờ thanh toán',
            'in_process' => $this->locale == 'en' ? 'In process' : 'Đang thanh toán',
            'completed'  => $this->locale == 'en' ? 'Completed' : 'Đã thanh toán',
            'failed'     => $this->locale == 'en' ? 'Failed' : 'Thanh toán thất bại',
            'paid'       => $this->locale == 'en' ? 'Paid' : 'Đã thanh toán',
            'refunded'   => $this->locale == 'en' ? 'Refunded' : 'Hoàn lại',
            'canceled'   => $this->locale == 'en' ? 'Canceled' : 'Đã hủy',
            'unknown'    => $this->locale == 'en' ? 'Unknown' : 'Lỗi không xác định',
        ];
        if (!empty($this->status) && isset($list[$this->status])) {
            return $list[$this->status];
        } else {
            return '';
        }
    }

    // 'pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'
    public function getOrderStatusNameAttribute() {
        $list = [
            'pending'    => $this->locale == 'en' ? 'Pending' : 'Chờ xác nhận',
            'processing' => $this->locale == 'en' ? 'Processing' : 'Đang xử lý',
            'shipping'   => $this->locale == 'en' ? 'Shipping' : 'Đang giao hàng',
            'completed'  => $this->locale == 'en' ? 'Completed' : 'Hoàn thành',
            'canceled'   => $this->locale == 'en' ? 'Canceled' : 'Đã hủy',
            'returning'  => $this->locale == 'en' ? 'Returning' : 'Đang trả hàng',
            'returned'   => $this->locale == 'en' ? 'Returned' : 'Đã trả hàng',
        ];
        if (!empty($this->order_status) && isset($list[$this->order_status])) {
            return $list[$this->order_status];
        } else {
            return '';
        }
    }

    // 'pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'
    public function getPaymentStatusNameAttribute() {
        $list = [
            'pending'    => $this->locale == 'en' ? 'Pending' : 'Chờ thanh toán',
            'in_process' => $this->locale == 'en' ? 'In process' : 'Đang thanh toán',
            'paid'       => $this->locale == 'en' ? 'Paid' : 'Đã thanh toán',
            'failed'     => $this->locale == 'en' ? 'Failed' : 'Thanh toán thất bại',
            'unknown'    => $this->locale == 'en' ? 'Unknown' : 'Lỗi không xác định',
            'refunded'   => $this->locale == 'en' ? 'Refunded' : 'Hoàn lại',
            'canceled'   => $this->locale == 'en' ? 'Canceled' : 'Đã hủy thanh toán',
        ];
        if (!empty($this->payment_status) && isset($list[$this->payment_status])) {
            return $list[$this->payment_status];
        } else {
            return '';
        }
    }

    // 'create_order', 'delivering', 'delivered', 'return'
    public function getShippingStatusNameAttribute() {
        $list = ['create_order' => 'Đã tạo đơn', 'delivering' => 'Đang vận chuyển', 'delivered' => 'Đã giao hàng', 'return' => 'Trả hàng'];
        return !empty($this->shipping_status) && isset($list[$this->shipping_status]) ? $list[$this->shipping_status] : '';
    }
}
