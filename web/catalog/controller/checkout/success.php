<?php
class ControllerCheckoutSuccess extends Controller {
    public function index() {
        $this->load->language('checkout/success');

        $this->cart->clear();

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['guest']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        unset($this->session->data['totals']);

        $this->document->setTitle($this->language->get('heading_title'));

        $pd_success = $this->config->get('pd_success');
        $data['pd_success'] = '';
        if (is_string($pd_success)) {
            $data['pd_success'] = $pd_success;
        } else if (is_array($pd_success) && $pd_success[$this->config->get('config_language')]) {
            $data['pd_success'] = $pd_success[$this->config->get('config_language')];
        }

        $id = isset($this->request->get['id']) ? (int)$this->request->get['id'] : 0;
        $info = false;
        if ($id) {
            $this->load->model('checkout/order');
            $info = $this->model_checkout_order->getOrder($id);
        }
        $data['info'] = $info;
        $data['text_message'] = '';
        if ($info) {
            $userData = $this->registry->get('userData');
            $userInfo = $userData ? $userData['info'] : false;
            $email = $userInfo ? $userInfo['email'] : $info['email'];
            $phone_number = $userInfo && $userInfo['phone_number'] ? $userInfo['phone_number'] : ($info['phone_number'] ? $info['phone_number'] : $info['shipping_phone_number']);
            $date = date('d/m/Y', strtotime($info['created_at']));
            $total = $info['total'];

            $config_card_holder = $this->config->get('config_card_holder');
            $config_bank_number = $this->config->get('config_bank_number');
            $config_bank_name = $this->config->get('config_bank_name');
            if (in_array($info['payment_code'], [PAYMENT_MT_DOMESTIC, PAYMENT_MT_FOREIGN, PAYMENT_MT_MOMO])) {
                $data['text_title'] = $this->language->get('text_title_online');
                //$data['text_message'] = sprintf($this->language->get('text_message_online'), $info['id']);
            } else if ($info['payment_code'] == PAYMENT_MT_COD) {
                $content = "<div class=\"row text-left mx-auto col-sm-9\"><div class=\"col-sm-6\"><ul>
                              <li>Mã đơn hàng: <b>{$info['idx']}</b></li>
                              <li>Email: <b>{$email}</b></li>
                              <li>Số điện thoại: <b>{$phone_number}</b></li>
                            </ul></div><div class=\"col-sm-6\"><ul>
                              <li>Địa chỉ giao hàng: <b>{$info['shipping_address_1']}, {$info['shipping_ward']}, {$info['shipping_district']}, {$info['shipping_province']}</b></li>
                              <li>Phương thức thanh toán: <b>COD</b></li>
                              <li>Tổng số tiền sẽ thanh toán: <b class=\"text-danger\">{$total}đ</b></li>
                            </ul></div></div>";
                $data['text_title'] = $this->language->get('text_title_cod');
                $data['text_message'] = $content;
            } else {
                $content = "<div class=\"row text-left\"><div class=\"col-sm-6\"><ul>
                              <li>Mã đơn hàng: <b>{$info['idx']}</b></li>
                              <li>Ngày: <b>{$date}</b></li>
                              <li>Email: <b>{$email}</b></li>
                              <li>Tổng cộng: <b class=\"text-danger\">{$total}đ</b></li>
                              <li>Phương thức thanh toán: <b>Chuyển khoản ngân hàng</b></li>
                            </ul></div><div class=\"col-sm-6\"><ul>
                              <li>Tên tài khoản: <b>$config_card_holder</b></li>
                              <li>Số tài khoản: <b>$config_bank_number</b></li>
                              <li>Tên ngân hàng: <b>$config_bank_name</b></li>
                              <li>Nội dung chuyển khoản: <b>{$info['idx']} - {$info['shipping_phone_number']}</b></li>
                            </ul></div></div>";

                $data['text_title'] = $this->language->get('text_title_transfer');
                $data['text_message'] = sprintf($this->language->get('text_message_transfer'), $info['idx'], $date, $date, $content);
            }
        }

        $data['hotline'] = $this->config->get('config_hotline');

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');

        if ($data['global']['config_wheel_order_status']) {
            $this->load->model('marketing/wheel');
            $info_wheel = $this->model_marketing_wheel->getWheels($info['created_at']);
            $total_without_shipping = $info['org_total'] + ($info['org_shipping_fee'] - $info['shipping_discount']);
            if ($info_wheel && $info['org_total'] and $total_without_shipping >= $data['global']['config_wheel_order_total'] and $info['payment_status'] == PAYMENT_SS_PAID) {
                $data['wheel'] = $this->load->view('utility/wheel', ['info' => $info_wheel, 'global' => $data['global'], 'order_id' => $id]);
            }
        }

        $this->response->setOutput($this->load->view('checkout/success', $data));
    }
}
