<?php namespace Modules\Core\Traits;

/**
 * Trait MomoTrait
 *
 * @package Modules\Core\Traits
 */
trait MomoTrait {
    // Momo response message
    protected function getMomoResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" :
                $result = "Giao dịch thành công.";
                break;
            case "9000" :
                $result = "Giao dịch đã được xác nhận thành công.";
                break;
            case "8000" :
                $result = "Giao dịch đang ở trạng thái cần được người dùng xác nhận thanh toán lại.";
                break;
            case "7000" :
                $result = "Giao dịch đang được xử lý.";
                break;
            case "1000" :
                $result = "Giao dịch đã được khởi tạo, chờ người dùng xác nhận thanh toán.	";
                break;
            case "11" :
                $result = "Truy cập bị từ chối.";
                break;
            case "12" :
                $result = "Phiên bản API không được hỗ trợ cho yêu cầu này.";
                break;
            case "13" :
                $result = "Xác thực doanh nghiệp thất bại.";
                break;
            case "20" :
                $result = "Yêu cầu sai định dạng.";
                break;
            case "21" :
                $result = "Số tiền giao dịch không hợp lệ.";
                break;
            case "40" :
                $result = "RequestId bị trùng.";
                break;
            case "41" :
                $result = "OrderId bị trùng.";
                break;
            case "42" :
                $result = "OrderId không hợp lệ hoặc không được tìm thấy.";
                break;
            case "43" :
                $result = "Yêu cầu bị từ chối vì xung đột trong quá trình xử lý giao dịch.";
                break;
            case "1001" :
                $result = "Giao dịch thanh toán thất bại do tài khoản người dùng không đủ tiền.";
                break;
            case "1002" :
                $result = "Giao dịch bị từ chối do nhà phát hành tài khoản thanh toán.";
                break;
            case "1003" :
                $result = "Giao dịch bị đã bị hủy.";
                break;
            case "1004" :
                $result = "Giao dịch thất bại do số tiền thanh toán vượt quá hạn mức thanh toán của người dùng.";
                break;
            case "1005" :
                $result = "Giao dịch thất bại do url hoặc QR code đã hết hạn.";
                break;
            case "1006" :
                $result = "Giao dịch thất bại do người dùng đã từ chối xác nhận thanh toán.";
                break;
            case "1007" :
                $result = "Giao dịch bị từ chối vì tài khoản người dùng đang ở trạng thái tạm khóa.";
                break;
            case "1026" :
                $result = "Giao dịch bị hạn chế theo thể lệ chương trình khuyến mãi.";
                break;
            case "1080" :
                $result = "Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu không được tìm thấy.";
                break;
            case "1081" :
                $result = "Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu có thể đã được hoàn.";
                break;
            case "2001" :
                $result = "Giao dịch thất bại do sai thông tin liên kết.";
                break;
            case "2007" :
                $result = "Giao dịch thất bại do liên kết hiện đang bị tạm khóa.";
                break;
            case "3001" :
                $result = "Liên kết thất bại do người dùng từ chối xác nhận.";
                break;
            case "3002" :
                $result = "Liên kết bị từ chối do không thỏa quy tắc liên kết.";
                break;
            case "3003" :
                $result = "Hủy liên kết bị từ chối do đã vượt quá số lần hủy.";
                break;
            case "3004" :
                $result = "Liên kết này không thể hủy do có giao dịch đang chờ xử lý.";
                break;
            case "4001" :
                $result = "Giao dịch bị hạn chế do người dùng chưa hoàn tất xác thực tài khoản.";
                break;
            case "4010" :
                $result = "Quá trình xác minh OTP thất bại.";
                break;
            case "4011" :
                $result = "OTP chưa được gửi hoặc hết hạn.";
                break;
            case "4100" :
                $result = "Giao dịch thất bại do người dùng không đăng nhập thành công.";
                break;
            case "4015" :
                $result = "Quá trình xác minh 3DS thất bại.";
                break;
            case "10" :
                $result = "Hệ thống đang được bảo trì.";
                break;
            case "99" :
                $result = "Lỗi không xác định.";
                break;
            default  :
                $result = "Transaction failed";
        }
        return $result;
    }

    public function execPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)]
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    /**
     * @param $order_id
     * @param $total
     * @param string $currency_code
     * @return mixed
     */
    protected function createMomoUrl($order_id, $total, $currency_code = 'VND') {
        $mode = env('MOMO_MODE', 'sandbox');
        $endpoint = config("transaction.momo.$mode.url"); //"https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = config("transaction.momo.$mode.partner_code"); //'MOMOBKUN20180529';
        $accessKey = config("transaction.momo.$mode.access_key"); //'klm05TvNBzhg7h7j';
        $secretKey = config("transaction.momo.$mode.secret_key"); //'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";
        $amount = $total;
        $orderId = $order_id;
        $redirectUrl = config('app.url') . '/api/v1/index.php/orders/momo_callback';
        $ipnUrl = config('app.url') . '/api/v1/index.php/orders/momo_callback';
        $extraData = "";

        $requestId = time() . "";
        $requestType = "captureWallet";
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = ['partnerCode' => $partnerCode,
                 'partnerName' => "Test",
                 "storeId"     => "MomoTestStore",
                 'requestId'   => $requestId,
                 'amount'      => $amount,
                 'orderId'     => $orderId,
                 'orderInfo'   => $orderInfo,
                 'redirectUrl' => $redirectUrl,
                 'ipnUrl'      => $ipnUrl,
                 'lang'        => 'vi',
                 'extraData'   => $extraData,
                 'requestType' => $requestType,
                 'signature'   => $signature];
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json

        //Just a example, please check more in there
        return $jsonResult['payUrl'];
    }

    /**
     * Get Onepay Callback
     *
     * @return array
     */
    protected function getMomoCallback() {
        $mode = env('MOMO_MODE', 'sandbox');
        $allInput = $this->request->all();
        $m2signature = $this->request->get('signature'); //MoMo signature
        $accessKey = config("transaction.momo.$mode.access_key");
        $secretKey = config("transaction.momo.$mode.secret_key");;
        //Checksum
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $allInput['amount'] . "&extraData=" . $allInput['extraData'] . "&message=" . $allInput['message'] . "&orderId=" . $allInput['orderId'] . "&orderInfo=" . $allInput['orderInfo'] .
            "&orderType=" . $allInput['orderType'] . "&partnerCode=" . $allInput['partnerCode'] . "&payType=" . $allInput['payType'] . "&requestId=" . $allInput['requestId'] . "&responseTime=" . $allInput['responseTime'] .
            "&resultCode=" . $allInput['resultCode'] . "&transId=" . $allInput['transId'];
        $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
        if ($m2signature == $partnerSignature) {
            if ($allInput['resultCode'] == '0') {
                $hashValidated = 'CORRECT';
            } else {
                $hashValidated = 'INVALID HASH';
            }
        } else {
            $hashValidated = 'HACKED';
        }
        // Variable results
        $txnResponseCode = $allInput['resultCode'];
        $transactionNo = $allInput['transId'];
        $order_id = $allInput['orderId'];

        return [$order_id, $hashValidated, $txnResponseCode, $transactionNo, $allInput];

    }
}
