<?php
class ControllerCheckoutPaymentOnepay extends Controller {
    // OnePAY response message
    protected function getOnepayResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" :
                $result = "Transaction Successful";
                break;
            case "?" :
                $result = "Transaction status is unknown";
                break;
            case "1" :
                $result = "Bank system reject";
                break;
            case "2" :
                $result = "Bank Declined Transaction";
                break;
            case "3" :
                $result = "No Reply from Bank";
                break;
            case "4" :
                $result = "Expired Card";
                break;
            case "5" :
                $result = "Insufficient funds";
                break;
            case "6" :
                $result = "Error Communicating with Bank";
                break;
            case "7" :
                $result = "Payment Server System Error";
                break;
            case "8" :
                $result = "Transaction Type Not Supported";
                break;
            case "9" :
                $result = "Bank declined transaction (Do not contact Bank)";
                break;
            case "B" :
                $result = "Fraud Risk Block";
                break;
            case "Z" :
                $result = "Transaction was block by OFD";
                break;
            case "F" :
                $result = "3D Secure Authentication failed";
                break;
            case "I" :
                $result = "Card Security Code verification failed";
                break;
            case "99" :
                $result = "User Cancel";
                break;
            default  :
                $result = "Transaction failed";
        }
        return $result;
    }

    protected function null2unknown($data) {
        if ($data == "") {
            return "No Value Returned";
        } else {
            return $data;
        }
    }

    /**
     * Get One Pay Url
     * Quốc tế: vpc_CardList=INTERNATIONAL
     * Nội địa: vpc_CardList=DOMESTIC
     * QR: vpc_CardList=QR
     *
     * @param $order_id
     * @param $total
     * @param string $currency_code
     * @param string $payment_method : INTERNATIONAL|DOMESTIC|QR
     * @return string
     */
    protected function createOnepayUrl($order_id, $total, $currency_code = 'VND', $payment_method = 'DOMESTIC') {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $returnURL = rtrim($this->config->get('config_url') . $url_prefix, '/') . "/checkout/payment/onepay/callback";
        $vpcURL = $this->config->get('onepay_url') . '?';
        $secureSecret = $this->config->get('onepay_secure_secret');
        $merchantId = $this->config->get('onepay_merchantid');
        $accessCode = $this->config->get('onepay_accesscode');
        $post_variables = [
            'AgainLink'          => 'onepay.vn',
            'Title'              => 'onepay.vn',
            'vpc_Locale'         => $this->config->get('language_code_default') == 'en' ? 'en' : 'vn',//'en'//ngôn ngữ hiển thị trên cổng thanh toán
            'vpc_Version'        => '2',//Phiên bản modul
            'vpc_Command'        => 'pay',//tên hàm
            'vpc_CardList'       => strtoupper($payment_method),// Method
            'vpc_Merchant'       => $merchantId,//mã đơn vị(OP cung cấp)
            'vpc_AccessCode'     => $accessCode,//mã truy nhập cổng thanh toán (OP cung cấp)
            'vpc_MerchTxnRef'    => date('YmdHis') . rand(),//ID giao dịch (duy nhất)
            'vpc_OrderInfo'      => $order_id,//mã đơn hàng
            'vpc_Amount'         => round($total * 100),//số tiền thanh toán
            'vpc_ReturnURL'      => $returnURL,//url nhận kết quả trả về từ OnePAY
            'vpc_Customer_Email' => $this->user->getEmail,//email khách hàng
            'vpc_Customer_Id'    => $this->user->getId,//mã khách hàng
            'vpc_TicketNo'       => $this->request->server['REMOTE_ADDR'],//ip khách hàng
            'vpc_Currency'       => $currency_code
        ];
        ksort($post_variables);
        $appendAmp = 0;
        $stringHashData = '';
        foreach ($post_variables as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                //sử dụng cả tên và giá trị tham số để mã hóa
                if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
        }
        $stringHashData = rtrim($stringHashData, "&");
        if (strlen($secureSecret) > 0) {
            //$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($stringHashData));
            //Mã hóa dữ liệu
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $secureSecret)));
        }
        return $vpcURL;
    }

    public function index($order_data) {
        $data['payment_url'] = $order_data['payment_code'];
        $data['payment_url'] = $this->createOnepayUrl($order_data['order_id'], $order_data['total'], $order_data['currency_code'], $order_data['payment_code']);

        $this->load->model("checkout/order");
        $comment = '';
        $this->model_checkout_order->addOrderHistory($order_data['order_id'], 'in_process', $comment, true);

        return $data;
    }

    public function callback() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $allInput = $_GET;
        unset($allInput['_route_']);

        // get and remove the vpc_TxnResponseCode code from the response fields as we
        // do not want to include this field in the hash calculation
        $vpc_Txn_Secure_Hash = $this->request->get["vpc_SecureHash"];
        $vpc_TxnResponseCode = $this->request->get["vpc_TxnResponseCode"];

        // Define Constants
        // ----------------
        // This is secret for encoding the MD5 hash
        // This secret will vary from merchant to merchant
        // To not create a secure hash, let SECURE_SECRET be an empty string - ""
        // $secureSecret = "secure-hash-secret";
        $secureSecret = $this->config->get('onepay_secure_secret');
        if (strlen($secureSecret) > 0 && $vpc_TxnResponseCode != "7" && $vpc_TxnResponseCode != "No Value Returned") {
            ksort($allInput);
            //$md5HashData = $secureSecret;
            //khởi tạo chuỗi mã hóa rỗng
            $stringHashData = "";
            // sort all the incoming vpc response fields and leave out any with no value
            foreach ($allInput as $key => $value) {
                //if ($key != "vpc_SecureHash" or strlen($value) > 0) {
                //    $stringHashData .= $value;
                //}
                // chỉ lấy các tham số bắt đầu bằng "vpc_" hoặc "user_" và khác trống và không phải chuỗi hash code trả về
                if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
            //  Xóa dấu & thừa cuối chuỗi dữ liệu
            $stringHashData = rtrim($stringHashData, "&");

            // if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($stringHashData))) {
            //    Thay hàm tạo chuỗi mã hóa
            if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $secureSecret)))) {
                // Secure Hash validation succeeded, add a data field to be displayed
                // later.
                $hashValidated = "CORRECT";
            } else {
                // Secure Hash validation failed, add a data field to be displayed
                // later.
                $hashValidated = "INVALID HASH";
            }
        } else {
            // Secure Hash was not validated, add a data field to be displayed later.
            $hashValidated = "INVALID HASH";
        }

        // Variable results
        $txnResponseCode = $this->null2unknown($this->request->get["vpc_TxnResponseCode"]);
        $vpc_TransactionNo = $this->null2unknown($this->request->get["vpc_TransactionNo"]);
        $order_id = (int)$this->request->get["vpc_OrderInfo"];

        $this->load->model("checkout/order");
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info || ($order_info && $order_info['status'] == ORDER_SS_COMPLETED)) {
            $this->response->redirect("/{$url_prefix}checkout/failure?id=$order_id&s=" . ($order_info ? $order_info['status'] : ''));
        }

        $input = [];
        $input['transaction_no'] = $vpc_TransactionNo;
        $input['response_code'] = $txnResponseCode;
        $input['payload'] = json_encode($allInput, true);
        if ($hashValidated == "CORRECT" && $txnResponseCode == "0") {
            $input['status'] = ORDER_SS_COMPLETED;
            $input['summary'] = 'Received data via web. Payment Successful';
        } else if ($hashValidated == "CORRECT" && $txnResponseCode != "0") {
            $input['status'] = ORDER_SS_FAILED;
            $input['summary'] = 'Received data via web. Payment Failed: ' . $this->getOnepayResponseDescription($txnResponseCode);
        } else if ($hashValidated == "INVALID HASH") {
            $input['status'] = ORDER_SS_PENDING;
            $input['summary'] = 'Received data via web. Pending';
        } else {
            $input['status'] = ORDER_SS_UNKNOWN;
            $input['summary'] = 'Received data via web. Status: ' . $this->getOnepayResponseDescription($txnResponseCode);
        }
        $input['payment_at'] = date('Y-m-d H:i:s');
        $this->model_checkout_order->editOrder($order_id, $input);
        $order_info = array_merge($order_info, $input);

        /*// Send alert
        if ($order_info['status'] == ORDER_SS_COMPLETED) {
            // Setup email to admin
            $emails = [];
            $alert_email = $this->setting_repository->findByKey('config_mail_alert_email');
            if ($alert_email) {
                $alert_emails = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($alert_email)));
                foreach ($alert_emails as $alert_email) {
                    $e2 = explode(',', (string)$alert_email);
                    foreach ($e2 as $i) if (trim($i)) $emails[] = trim($i);
                }
            }
            $emails = array_unique($emails);
            // End setup email to admin
        }*/

        /*$from = $this->config->get('config_email');
        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo($order_info['email']);
        $mail->setFrom($from);
        $mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $order_info['store_name'], $order_info['id']), ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($this->load->view('product/mail/order_add', $order_info));
        $mail->send();*/

        if ($order_info['status'] == ORDER_SS_COMPLETED || $order_info['status'] == ORDER_SS_PENDING) {
            $this->response->redirect("/{$url_prefix}checkout/success?id=$order_id&s=" . $order_info['status']);
        } else {
            $this->response->redirect("/{$url_prefix}checkout/failure?id=$order_id&s=" . $order_info['status']);
        }
    }
}
