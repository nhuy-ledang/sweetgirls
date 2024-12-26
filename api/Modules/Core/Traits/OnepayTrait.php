<?php namespace Modules\Core\Traits;

/**
 * Trait OnepayTrait
 *
 * @package Modules\Core\Traits
 */
trait OnepayTrait {
    // OnePAY response message
    protected function getOnepayResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" :
                $result = "Giao dịch thành công";
                break;
            case "?" :
                $result = "Tình trạng giao dịch không xác định";
                break;
            case "1" :
                $result = "Hệ thống ngân hàng từ chối giao dịch";
                break;
            case "2" :
                $result = "Ngân hàng từ chối giao dịch";
                break;
            case "3" :
                $result = "Không có phản hồi từ ngân hàng";
                break;
            case "4" :
                $result = "Thẻ hết hạn";
                break;
            case "5" :
                $result = "Không đủ số dư";
                break;
            case "6" :
                $result = "Lỗi kết nối với ngân hàng";
                break;
            case "7" :
                $result = "Lỗi hệ thống máy chủ thanh toán giao dịch";
                break;
            case "8" :
                $result = "Loại giao dịch không được hỗ trợ";
                break;
            case "9" :
                $result = "Ngân hàng từ chối giao dịch (Vui lòng không liên hệ với ngân hàng)";
                break;
            case "B" :
                $result = "Chặn do Nguy cơ gian lận";
                break;
            case "Z" :
                $result = "Giao dịch bị chặn bởi Hệ thống phòng chống gian lận (OFD)";
                break;
            case "F" :
                $result = "Xác thực 3D Secure không thành công";
                break;
            case "I" :
                $result = "Xác minh Mã bảo mật thẻ không thành công";
                break;
            case "99" :
                $result = "Người dùng hủy bỏ";
                break;
            default  :
                $result = "Giao dịch thất bại";
        }
        return $result;
    }

    protected function null2unknown($data) {
        if ($data == "") {
            return "Không có giá trị trả về";
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
     * @param string $order_id
     * @param double $total
     * @param string $currency_code
     * @param string $payment_method : INTERNATIONAL|DOMESTIC|QR
     * @param string $order_no
     * @return string
     */
    protected function createOnepayUrl($order_id, $total, $currency_code = 'VND', $payment_method = 'DOMESTIC', $order_no = '', $email = '') {
        $user_id = '';
        if($this->isLogged()) {
            $email = $this->auth->email;
            $user_id = $this->auth->id;
        }
        $mode = env('ONEPAY_MODE', 'sandbox');
        $returnURL = config('app.url') . "/api/v1/index.php/orders/onepay_callback";
        $vpcURL = config("transaction.onepay.$mode.url") . "?";
        $secureSecret = config("transaction.onepay.$mode.secure_secret");
        $merchantId = config("transaction.onepay.$mode.merchantid");
        $accessCode = config("transaction.onepay.$mode.accesscode");
        $payment_method = strtoupper($payment_method);
        if ($payment_method == 'DOMESTIC') $payment_method = 'DOMESTIC,QR';
        $post_variables = [
            'AgainLink'          => 'onepay.vn',
            'Title'              => 'onepay.vn',
            'vpc_Locale'         => $this->locale == 'en' ? 'en' : 'vn',//'en'//ngôn ngữ hiển thị trên cổng thanh toán
            'vpc_Version'        => '2',//Phiên bản modul
            'vpc_Command'        => 'pay',//tên hàm
            'vpc_CardList'       => $payment_method,// Method
            'vpc_Merchant'       => $merchantId,//mã đơn vị(OP cung cấp)
            'vpc_AccessCode'     => $accessCode,//mã truy nhập cổng thanh toán (OP cung cấp)
            'vpc_MerchTxnRef'    => $order_no ? $order_no : date('YmdHis') . rand(),//ID giao dịch (duy nhất)
            'vpc_OrderInfo'      => $order_id,//mã đơn hàng
            'vpc_Amount'         => round($total * 100),//số tiền thanh toán
            'vpc_ReturnURL'      => $returnURL,//url nhận kết quả trả về từ OnePAY
            'vpc_Customer_Email' => $email,//email khách hàng
            'vpc_Customer_Id'    => $user_id,//mã khách hàng
            'vpc_TicketNo'       => $_SERVER['REMOTE_ADDR'],//ip khách hàng
            'vpc_Currency'       => $currency_code,
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
                    $vpcURL .= '&' . urlencode($key) . '=' . urlencode($value);
                }
                //sử dụng cả tên và giá trị tham số để mã hóa
                if ((strlen($value) > 0) && ((substr($key, 0, 4) == 'vpc_') || (substr($key, 0, 5) == 'user_'))) {
                    $stringHashData .= $key . '=' . $value . '&';
                }
            }
        }
        $stringHashData = rtrim($stringHashData, '&');
        if (strlen($secureSecret) > 0) {
            //$vpcURL .= '&vpc_SecureHash=' . strtoupper(md5($stringHashData));
            //Mã hóa dữ liệu
            $vpcURL .= '&vpc_SecureHash=' . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $secureSecret)));
        }
        return $vpcURL;
    }

    /**
     * Get Onepay Callback
     *
     * @return array
     */
    protected function getOnepayCallback() {
        $mode = env('ONEPAY_MODE', 'sandbox');
        $allInput = $this->request->all();

        // get and remove the vpc_TxnResponseCode code from the response fields as we
        // do not want to include this field in the hash calculation
        $vpc_Txn_Secure_Hash = $this->request->get('vpc_SecureHash');
        $vpc_TxnResponseCode = $this->request->get('vpc_TxnResponseCode');

        // Define Constants
        // ----------------
        // This is secret for encoding the MD5 hash
        // This secret will vary from merchant to merchant
        // To not create a secure hash, let SECURE_SECRET be an empty string - ""
        // $secureSecret = "secure-hash-secret";
        $secureSecret = config("transaction.onepay.$mode.secure_secret");
        if (strlen($secureSecret) > 0 && $vpc_TxnResponseCode != '7' && $vpc_TxnResponseCode != 'No Value Returned') {
            ksort($allInput);
            //$md5HashData = $secureSecret;
            //khởi tạo chuỗi mã hóa rỗng
            $stringHashData = '';
            // sort all the incoming vpc response fields and leave out any with no value
            foreach ($allInput as $key => $value) {
                //if ($key != 'vpc_SecureHash' or strlen($value) > 0) {
                //    $stringHashData .= $value;
                //}
                // chỉ lấy các tham số bắt đầu bằng 'vpc_' hoặc 'user_' và khác trống và không phải chuỗi hash code trả về
                if ($key != 'vpc_SecureHash' && (strlen($value) > 0) && ((substr($key, 0, 4) == 'vpc_') || (substr($key, 0, 5) == 'user_'))) {
                    $stringHashData .= $key . '=' . $value . '&';
                }
            }
            //  Xóa dấu & thừa cuối chuỗi dữ liệu
            $stringHashData = rtrim($stringHashData, '&');

            // if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($stringHashData))) {
            //    Thay hàm tạo chuỗi mã hóa
            if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $secureSecret)))) {
                // Secure Hash validation succeeded, add a data field to be displayed
                // later.
                $hashValidated = 'CORRECT';
            } else {
                // Secure Hash validation failed, add a data field to be displayed
                // later.
                $hashValidated = 'INVALID HASH';
            }
        } else {
            // Secure Hash was not validated, add a data field to be displayed later.
            $hashValidated = 'INVALID HASH';
        }

        // Variable results
        $txnResponseCode = $this->null2unknown($this->request->get('vpc_TxnResponseCode'));
        $transactionNo = $this->null2unknown($this->request->get('vpc_TransactionNo'));
        $order_id = (string)$this->request->get('vpc_OrderInfo');

        return [$order_id, $hashValidated, $txnResponseCode, $transactionNo, $allInput];
    }
}
