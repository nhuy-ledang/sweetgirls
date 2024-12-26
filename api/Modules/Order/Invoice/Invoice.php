<?php

namespace Modules\Order\Invoice;

use Modules\Order\Invoice\Http\ArrayLogger;
use Psr\Log\LoggerInterface;

class Invoice {
    /**
     * @var string Base URL of all API requests
     */
    protected $url = 'https://testapp.meinvoice.vn/api/v2';

    /**
     * @var string URL used to request an access token
     */
    protected $tokenUri = 'https://app.meinvoice.vn/api/v2/oauth';

    /**
     * @var string
     */
    protected $taxCode;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $grantType;

    /**
     * @var boolean Determines if API calls should be logged
     */
    protected $debug = false;

    /**
     * @var Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $httpLogAdapter;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var Array
     */
    protected $tokenInfo;

    public function __construct() {
        $this->taxCode = config('invoice.taxCode', '0316540474');
        $this->username = config('invoice.username', 'ketoan4@cuasovang.vn');
        $this->password = config('invoice.password', 'Slife123');
        $this->grantType = config('invoice.grantType', 'password');
    }

    /**
     * Write Token To Log
     *
     * @param string $scope
     * @param string $token
     */
    protected function setLogToken($scope = 'create-invoice', $token) {
        $filepath = storage_path("app/invoice-{$scope}.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        // Empty file
        $handle = fopen($filepath, 'w+');
        fclose($handle);
        // Write data
        $handle = fopen($filepath, 'a+');
        fwrite($handle, serialize($token));
        fclose($handle);
    }

    /**
     * Read Token From Log
     *
     * @param string $scope
     * @return mixed|string
     */
    protected function getLogToken($scope = 'create-invoice') {
        $filepath = storage_path("app/invoice-{$scope}.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        $handle = fopen($filepath, 'r');
        flock($handle, LOCK_SH);
        $size = filesize($filepath);
        $token = $size > 0 ? unserialize(trim(fread($handle, $size))) : '';
        flock($handle, LOCK_UN);
        fclose($handle);

        return $token;
    }

    /**
     * @return Token
     */
    protected function getToken() {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    protected function setToken($token) {
        $this->token = $token;
    }

    /**
     * @return LoggerInterface
     */
    protected function getHttpLogAdapter() {
        // If a log adapter hasn't been set, we default to the null adapter
        if (!$this->httpLogAdapter) $this->httpLogAdapter = new ArrayLogger();

        return $this->httpLogAdapter;
    }

    /**
     * @return Http\ClientInterface
     */
    protected function getHttpClient() {
        if (!$this->httpClient) return new Http\GuzzleHttpClient($this->debug, $this->getHttpLogAdapter());

        return $this->httpClient;
    }

    /**
     * Get access_token
     *
     * @param string $scope
     * @return Token
     */
    public function requestAccessToken($scope = 'create-invoice') {
        $tokenArr = json_decode($this->getLogToken($scope), true);
        if ($tokenArr) {
            $token = new Token($tokenArr);
            if ($token->isExpired()) $tokenArr = null;
        }
        if (!$tokenArr) {
            $params = ['username' => $this->username, 'password' => $this->password, 'grant_type' => $this->grantType];
            $client = $this->getHttpClient();
            $tokenInfo = $client->call('POST', $this->tokenUri, ['body' => http_build_query($params), 'headers' => ['taxcode' => (string)$this->taxCode, 'Content-Type' => 'text/plain']]);
            $tokenArr = json_decode($tokenInfo, true);
            if (!$tokenArr) throw new \InvalidArgumentException('Xác thực oauth2 thất bại!');
            $tokenArr['created_at'] = time();
            $this->setLogToken($scope, json_encode($tokenArr, true));
        }
        $this->setToken(new Token($tokenArr));
        $this->tokenInfo = $tokenArr;
        return $this->getToken();
    }

    /**
     * Checks if the current token is null or expired
     *
     * @return boolean
     */
    protected function isTokenExpired() {
        $token = $this->getToken();

        if (!is_object($token)) return true;

        return $token->isExpired();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     *
     * @throws TokenExpiredException
     * @return mixed
     */
    protected function restfulRequest($method, $url, $params = []) {
        // Before making the request, we can make sure that the token is still
        // valid by doing a check on the end of life.
        $token = $this->getToken();
        if ($this->isTokenExpired()) throw new TokenExpiredException;
        $client = $this->getHttpClient();
        $full_params = [];
        if (strtolower($method) === 'get' || strtolower($method) === 'delete') {
            $url = $url . '?' . http_build_query($params);
        } else {
            $full_params['body'] = json_encode($params);
        }
        $full_params['headers'] = ['Authorization' => 'Bearer ' . $token->getAccessToken(), 'taxcode' => $this->taxCode, 'Content-Type' => 'application/json'];
        $response = (string)$client->call($method, $url, $full_params);

        return $response ? json_decode($response, true) : null;
    }



    /**
     * Get TypeInvoice
     *
     * @param int $type_invoice
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getTypeInvoice($type_invoice = 0) {
        $this->requestAccessToken('create-invoice');
        $params = [
            'TypeInvoice' => $type_invoice, // {loại hóa đơn: 0,1,2,3,4,5,6}
            'TaxCode' => $this->taxCode,
            'UserName' => $this->username,
            'Password' => $this->password,
        ];

        return $this->restfulRequest('post', 'https://app.meinvoice.vn/api/v2/v3common/code/template', $params);
    }

    /**
     * Create invoice
     *
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function createInvoice($params = []) {
        $this->requestAccessToken('create-invoice');
        $res_type_invoice = $this->getTypeInvoice();
        $data_type_invoice = '';
        if ($res_type_invoice['data']) $data_type_invoice = json_decode($res_type_invoice['data'], true)[0]; // $res_type_invoice['data'] | Array

        $detail = [[
            //'RefDetailID'       => '', // ID của dòng hàng hóa | Guild | Yes
            //'RefID'             => '', // ID của hóa đơn ở master | Guild | Yes
            //'InventoryItemType' => '', // Loại hàng hóa: Product = 1, Promotion = 2, Description = 3, Discount = 4 | int | Yes
            //'InventoryItemCode' => '', // Mã hàng hóa | string | Yes
            //'Description'       => '', // Tên hàng hóa | string | Yes
            //'UnitName'          => '', // đơn vị tính | string | Yes
            //'Quantity'          => '', // số lượng | decimal | Yes
            //'UnitPrice'         => '', // đơn giá | decimal | Yes
            //'AmountOC'          => '', // thành tiền nguyên tệ = Quantity UnitPrice | decimal | Yes
            //'Amount'            => '', // thành tiền quy đổi = AmountOC ExchangeRate | decimal | Yes
            //'DiscountRate'      => '', // phần trăm chiết khấu | decimal | Yes
            //'DiscountAmountOC'  => '', // số tiền chiết khấu nguyên tệ | decimal | Yes
            //'DiscountAmount'    => '', // số tiền chiết khấu quy đổi = DiscountAmountOC ExchangeRate | decimal | Yes
            //'VATRate'           => '', // thuế suất | decimal | Yes
            //'VATAmountOC'       => '', // tiền thuế VAT nguyên tệ = AmountOC VATRate/100 | decimal | Yes
            //'VATAmount'         => '', // tiền thuế VAT quy đổi = VATAmountOC * ExchangeRate | decimal | Yes
            //'SortOrder'         => '', // số thứ tự | int | Yes
            //'SortOrderView'     => '', // số thứ tự hiển thị(lưu ý: đối với hàng hóa InventoryItemType:3,4 thì SortOrderView:null | int | Yes
        ]];
        $params = array_merge([
            //'RefID'                    => '', // Khóa chính của hóa đơn | Guid | Yes
            'CompanyID'                => $this->tokenInfo['CompanyID'], // ID của công ty (lấy từ kết quả trả về của api get token) | int | Yes
            'OrganizationUnitID'       => $this->tokenInfo['OrganizationUnitID'], // id định danh đơn vị (lấy từ kết quả trả về của api get token) | string | Yes
            'UserID'                   => $this->tokenInfo['UserID'], // ID của user lập HĐ (lấy từ kết quả trả về của api get token) | string | Yes
            'InvoiceType'              => $data_type_invoice['InvoiceType'], // Loại hóa đơn (lấy theo kết quả trả về của api get template) | string | Yes
            'InvTemplateNo'            => $data_type_invoice['InvTemplateNo'], // mẫu số HĐ (lấy theo kết quả trả về của api get template) | string | Yes
            'InvoiceTemplateID'        => $data_type_invoice['IPTemplateID'], // ID mẫu hóa đơn (lấy theo kết quả trả về của api get template) | string | Yes
            'IsInheritFromOldTemplate' => $data_type_invoice['TemplateType'], // kiểu mẫu HĐ (lấy theo kết quả trả về của api get template) | string | Yes
            //'InvDate'                  => '', // Ngày hóa đơn | Datetime | Yes
            'InvNo'                    => '<Chưa cấp số>', // Số hóa đơn (mặc định truyền "Chưa cấp số") | string | Yes
            'SourceType'               => 0, // giá trị mặc định: 0 | int | Yes
            'SendInvoiceStatus'        => 0, // giá trị mặc định: 0 | int | Yes
            'SendNumber'               => 0, // giá trị mặc định: 0 | int | Yes
            'CurrencyCode'             => 'VND', // Mã loại tiền tệ. ví dụ: VND,USD... | string | Yes
            'CurrencyID'               => 'VND', // Mã loại tiền tệ. ví dụ: VND,USD... | string | Yes
            'ExchangeRate'             => 1, // tỷ giá (nếu là VND thì ExchangeRate:1) | decimal | Yes
            //'TypeDiscount'             => 0, // Loại chiết khấu: 0: Không có chiết khấu 1: Chiết khấu theo dòng hàng 2: Chiết khấu theo tổng giá trị hóa đơn | int | Yes
            //'DiscountRate'             => 0, // Phần trăm chiết khấu | decimal | Yes
            'IsMoreVATRate'            => false, // Đánh dấu HĐ nhiều thuế suất hay 1 thuế suất: 1 thuế suất: false nhiều thuế suất: true | bool | Yes
            'VATRate'                  => 0, // Loại thuế suất(0%,5%,8%,10%,KCT,KKKNT,...) | decimal | Yes
            'ExchangeRateOperation'    => 0, // giá trị mặc định: 0 | decimal | yes
            'EInvoiceStatus'           => 0, // giá trị mặc định: 0 | int | yes
            'PaymentStatus'            => 0, // giá trị mặc định: 0 | int | yes
            'PaymentRule'              => 0, // kiểu thanh toán,giá trị mặc định: 0 | int | yes
            'ApproveStep'              => -3, // giá trị mặc định: -3 | int | yes
            'CreatedDate'              => date('Y-m-d'), // giá trị mặc định: DateTime.Now() | DateTime | Yes
            'ModifiedDate'             => date('Y-m-d'), // giá trị mặc định: DateTime.Now() | DateTime | Yes
            'EditVersion'              => 0, // giá trị mặc định: 0 | int | yes
            'OrgInvoiceType'           => 1, // giá trị mặc định: 1 | int | yes
            //'TotalSaleAmountOC'        => '', // = Sum(AmountOC, InventoryItemType = 0) - Sum(AmountOC, InventoryItemType = 4) | decimal | yes
            //'TotalSaleAmount'          => '', // = TotalSaleAmountOC * ExchangeRate | decimal | yes
            //'TotalVATAmountOC'         => '', // = Sum(VATAmountOC, InventoryItemType = 0) - Sum(VATAmountOC, InventoryItemType = 4) | decimal | yes
            //'TotalVATAmount'           => '', // = TotalVATAmountOC * ExchangeRate | decimal | yes
            //'TotalDiscountAmountOC'    => '', // = Sum(DiscountAmountOC) | decimal | yes
            //'TotalDiscountAmount'      => '', // = TotalDiscountAmountOC * ExchangeRate | decimal | yes
            //'TotalAmountOC'            => '', // = TotalSaleAmountOC – TotalDiscountAmountOC + TotalVATAmountOC | decimal | yes
            //'TotalAmount'              => '', // = TotalAmountOC * ExchangeRate | decimal | No
            //'AccountObjectTaxCode'     => '', // Mã số thuế của KH | string | No
            //'AccountObjectName'        => '', // Tên đơn vị | string | No
            //'AccountObjectCode'        => '', // Mã KH | string | No
            //'ContactName'              => '', // Người mua hàng | string | No
            //'ReceiverEmail'            => '', // email người mua hàng | string | No
            //'ReceiverName'             => '', // Invoice recipient’s name | string | No
            //'ReceiverMobile'           => '', // Số điện thoại người mua hàng | string | No
            //'PaymentMethod'            => '', // Hình thức thanh toán (TM,Ck,TM/CK,...) | string | yes
            'IsTaxReduction43'         => false, // Đánh dấu nếu hđ có thuế suất 8% (true,false) | bool | yes
            //'InvoiceDetails'           => $detail,
        ], $params);
//return $params;
        return $this->restfulRequest('post', 'https://app.meinvoice.vn/api/v2/v3sainvoice/insertone', $params);
    }

//    /**
//     * Update invoice
//     *
//     * @param string $id_attr
//     * @param array $params
//     * @return mixed
//     * @throws TokenExpiredException
//     */
//    public function updateInvoice($id_attr, $params = []) {
//        $this->requestAccessToken('create-invoice');
//        $params = array_merge([
//            'date_export'  => date('Y-m-d'),
//            'currency'     => 'VND',
//            'payment_type' => 2,
//        ], $params, [
//            'id_attr' => $id_attr,
//            'action'  => 'update',
//        ]);
//
//        return $this->restfulRequest('post', 'https://cpanel.hoadon30s.vn/api/invoice/create', $params);
//    }
//
//    /**
//     * Sync data invoice
//     *
//     * @param string $id_attr
//     * @return mixed
//     * @throws TokenExpiredException
//     */
//    public function syncInvoice($id_attr) {
//        $this->requestAccessToken('create-invoice');
//
//        return $this->restfulRequest('post', "https://cpanel.hoadon30s.vn/api/invoice/sync-data?id_attr=$id_attr&type=HDGTGT");
//    }
//
//    /**
//     * Lookup
//     *
//     * @param string $q
//     * @return mixed
//     * @throws TokenExpiredException
//     */
//    public function lookup($q) {
//        $this->requestAccessToken('invoice-lookup');
//        $params = ['matracuu' => $q];
//
//        return $this->restfulRequest('post', 'https://cpanel.hoadon30s.vn/api/invoice/lookup', $params);
//    }
//
//    /**
//     * Get Xml Sign
//     *
//     * @param string $id_attr
//     * @return mixed
//     * @throws TokenExpiredException
//     */
//    public function getXmlSign($id_attr) {
//        $this->requestAccessToken('xml-signed');
//        $params = ['id_attr' => $id_attr, 'type' => 'HDGTGT', 'xmlSigned' => '1'];
//
//        return $this->restfulRequest('post', 'https://cpanel.hoadon30s.vn/api/invoice/xml-sign', $params);
//    }
}
