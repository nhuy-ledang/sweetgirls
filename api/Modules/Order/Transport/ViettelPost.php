<?php namespace Modules\Order\Transport;

class ViettelPost extends Method {
    /**
     * Server Endpoint
     *
     * @var array
     */
    private static $endpoint = [
        self::MODE_LIVE    => 'https://partner.viettelpost.vn/v2',
        self::MODE_SANDBOX => 'https://partnerdev.viettelpost.vn/v2',
    ];

    /**
     * @var string Base URL of all API requests
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

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

    public function __construct() {
        self::_setMode(config('transport.viettelpost.mode', 'sandbox'));
        $this->username = config('transport.viettelpost.username', '');
        $this->password = config('transport.viettelpost.password', '');
        $this->url = self::getEndpoint();
    }

    /**
     * Get endpoint url
     *
     * @return string
     */
    public static function getEndpoint() {
        return self::$endpoint[self::$mode];
    }

    /**
     * Get access_token
     *
     * @return Token
     */
    protected function requestToken() {
        $data = json_decode($this->getLogToken('viettelpost'), true);
        if ($data) {
            $token = new Token($data);
            if ($token->isExpired()) $data = null;
        }
        if (!$data) {
            $params = ['USERNAME' => $this->username, 'PASSWORD' => $this->password];
            $client = $this->getHttpClient();
            $response = (string)$client->call('POST', $this->url . '/user/Login', ['body' => json_encode($params), 'headers' => ['Content-Type' => 'application/json']]);
            $response = $response ? json_decode($response, true) : null;
            if (!$response) throw new \InvalidArgumentException('Xác thực thất bại!');
            if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);
            $data = $response['data'];
            $data['expired'] = round($data['expired'] / 1000);
            $this->setLogToken(json_encode($data, true), 'viettelpost');
        }
        $this->setToken(new Token($data));

        return $this->getToken();
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
        $options = [];
        if (strtolower($method) === 'get' || strtolower($method) === 'delete') {
            $url = $url . '?' . http_build_query($params);
        } else {
            $options['body'] = json_encode($params);
        }
        $options['headers'] = ['Token' => $token->getToken(), 'Content-Type' => 'application/json'];
        $response = (string)$client->call($method, $url, $options);
        $response = $response ? json_decode($response, true) : null;
        if (!$response) throw new \InvalidArgumentException('Xác thực thất bại!');

        return $response;
    }

    /**
     * @return mixed
     */
    public function getProvinces() {
        $client = $this->getHttpClient();
        $response = (string)$client->call('GET', $this->url . '/categories/listProvince', ['headers' => ['Content-Type' => 'application/json']]);
        $response = $response ? json_decode($response, true) : null;
        if (!$response) throw new \InvalidArgumentException('Xác thực thất bại!');
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    /**
     * @param $provinceId
     * @return mixed
     */
    public function getDistricts($provinceId) {
        $client = $this->getHttpClient();
        $response = (string)$client->call('GET', $this->url . "/categories/listDistrict?provinceId=$provinceId", ['headers' => ['Content-Type' => 'application/json']]);
        $response = $response ? json_decode($response, true) : null;
        if (!$response) throw new \InvalidArgumentException('Xác thực thất bại!');
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    // Test login
    public function login() {
        $token = $this->requestToken();

        return $token->getToken();
    }

    /**
     * Get Price All | Lấy danh sách dịch vụ phù hợp với hành trình
     * 1    Token                   Header    String    Token tạo đơn của tài khoản client(Lấy ở mục 1)
     * 2    SENDER_PROVINCE         Body    Long    ID Tỉnh gửi hàng
     * 3    SENDER_DISTRICT         Body    Long    ID Huyện gửi hàng
     * 4    RECEIVER_PROVINCE       Body    Long    ID Tỉnh nhận hàng
     * 5    RECEIVER_DISTRICT       Body    Long    ID Huyện nhận hàng
     * 6    PRODUCT_TYPE            Body    String    Loại hàng hóa:
     *      -    TH: Thư
     *      -    HH: Hàng
     * 7    PRODUCT_WEIGHT          Body    Long    Trọng lượng(Gr)
     * 8    PRODUCT_PRICE           Body    Long    Giá trị hàng(VNĐ)
     * 9    MONEY_COLLECTION        Body    Long    Tiền hàng cần thu hộ thu hộ(VNĐ), không bao gồm tiền cước cần thu hộ.
     * 10   TYPE                    Body    Long    Loại bảng giá
     *      -    0: Bảng giá quốc tế
     *      -    1: Bảng giá trong nước
     * 11   PRODUCT_LENGTH          Body    Long    Chiều dài(cm), không bắt buộc
     * 12   PRODUCT_WIDTH           Body    Long    Chiều rộng(cm), không bắt buộc
     * 13   PRODUCT_HEIGHT          Body    Long    Chiều cao(cm), không bắt buộc
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPriceAll($params = []) {
        $this->requestToken();
        $params = array_merge([
//            'SENDER_DISTRICT'   => 12,
//            'SENDER_PROVINCE'   => 1,
//            'RECEIVER_DISTRICT' => 12,
//            'RECEIVER_PROVINCE' => 1,
//            'PRODUCT_WEIGHT'    => 100,
//            'PRODUCT_PRICE'     => 5000000,
//            'MONEY_COLLECTION'  => 5000000,
            'PRODUCT_TYPE' => 'HH',
            'TYPE'         => 1,
        ], $params);

        return $this->restfulRequest('post', $this->url . '/order/getPriceAll', $params);
    }

    /**
     * Get Price | Tính cước
     * 1    Token                   Header    String    Token tạo đơn của tài khoản client(Lấy ở mục 1)
     * 2    SENDER_PROVINCE         Body    Long    ID Tỉnh gửi hàng
     * 3    SENDER_DISTRICT         Body    Long    ID Huyện gửi hàng
     * 4    RECEIVER_PROVINCE       Body    Long    ID Tỉnh nhận hàng
     * 5    RECEIVER_DISTRICT       Body    Long    ID Huyện nhận hàng
     * 6    PRODUCT_TYPE            Body    String    Loại hàng hóa:
     *      -    TH: Thư
     *      -    HH: Hàng
     * 7    PRODUCT_WEIGHT          Body    Long    Trọng lượng(Gr)
     * 8    PRODUCT_PRICE           Body    Long    Giá trị hàng(VNĐ)
     * 9    MONEY_COLLECTION        Body    Long    Tiền hàng cần thu hộ thu hộ(VNĐ), không bao gồm tiền cước cần thu hộ.
     * 10   NATIONAL_TYPE           Body    Long    Loại bảng giá
     *      -    0: Bảng giá quốc tế
     *      -    1: Bảng giá trong nước
     * 11   PRODUCT_LENGTH          Body    Long    Chiều dài(cm), không bắt buộc
     * 12   PRODUCT_WIDTH           Body    Long    Chiều rộng(cm), không bắt buộc
     * 13   PRODUCT_HEIGHT          Body    Long    Chiều cao(cm), không bắt buộc
     * 14   ORDER_SERVICE           Body    String    Mã dịch vụ
     * 15   ORDER_SERVICE_ADD       Body    String    Mã dịch vụ cộng thêm
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPrice($params = []) {
        $this->requestToken();
        $params = array_merge([
//            'SENDER_DISTRICT'   => 12,
//            'SENDER_PROVINCE'   => 1,
//            'RECEIVER_DISTRICT' => 12,
//            'RECEIVER_PROVINCE' => 1,
//            'PRODUCT_WEIGHT'    => 100,
//            'PRODUCT_PRICE'     => 96000,
//            'MONEY_COLLECTION'  => 0,
//            'ORDER_SERVICE_ADD' => '',
//            'ORDER_SERVICE'     => 'VCBO',
            'PRODUCT_TYPE'  => 'HH',
            'NATIONAL_TYPE' => 1,
        ], $params);

        $response = $this->restfulRequest('post', $this->url . '/order/getPrice', $params);
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    /**
     * Create Order Nlp | Tạo đơn
     * 1    Token               Header    String    Token tạo đơn của tài khoản client(Lấy ở mục 1)
     * 2    ORDER_NUMBER        Body    String    Mã đơn hàng
     * 3    SENDER_FULLNAME     Body    String    Tên khách hàng gửi
     * 4    SENDER_PHONE        Body    String    Số điện thoại khách hàng gửi
     * 5    SENDER_ADDRESS      Body    String    Địa chỉ đầy đủ của khách hàng gửi, địa chỉ tối đa 150 byte.
     * 6    RECEIVER_FULLNAME   Body    String    Tên khách hàng nhận
     * 7    RECEIVER_PHONE      Body    String    Số điện thoại khách hàng nhận
     * 8    RECEIVER_ADDRESS    Body    String    Địa chỉ đầy đủ của khách hàng nhận, địa chỉ tối đa 150 byte.
     * 9    PRODUCT_NAME        Body    String    Tên gói hàng
     * 10   PRODUCT_DESCRIPTION Body    String    Mô tả(Cho xem hàng, thời gian giao, …), tối đa 150 byte.
     * 11   PRODUCT_QUANTITY    Body    Long    Tổng số lượng sản phẩm trong gói
     * 12   PRODUCT_PRICE       Body    Long    Tổng giá trị các sản phẩm trong gói
     * 13   PRODUCT_WEIGHT      Body    Long    Tổng trọng lượng các sản phẩm trong gói
     * 14   PRODUCT_LENGTH      Body    Long    Chiều dài(cm), không bắt buộc
     * 15   PRODUCT_WIDTH       Body    Long    Chiều rộng(cm), không bắt buộc
     * 16   PRODUCT_HEIGHT      Body    Long    Chiều cao(cm), không bắt buộc
     * 17   ORDER_PAYMENT       Body    Long    Loại vận đơn
     *      1. Không thu hộ
     *      2. Thu hộ tiền hàng và tiền cước
     *      3. Thu hộ tiền hàng
     *      4. Thu hộ tiền cước
     * 18   ORDER_SERVICE       Body    String    Mã dịch vụ, lấy từ Api lấy danh sách dịch vụ phù hợp hoặc tính cước.
     * 19   ORDER_SERVICE_ADD   Body    String    Mã dịch vụ cộng thêm lấy từ api danh sách dịch vụ phù hợp hoặc theo thông báo của nhân viên kinh doanh. Có thể chọn nhiều dịch vụ, mỗi dịch vụ cách nhau bởi dấu phẩy(,).
     * 20   ORDER_NOTE          Body    String    Ghi chú
     * 21   MONEY_COLLECTION    Body    Long    Tiền hàng cần thu hộ
     * 22   LIST_ITEM           Body    List< Object>    Danh sách hàng hóa chi tiết(Chỉ dùng để đối soát khi có thất thoát).
     *      Danh sách các Object có các thuộc tính như sau:
     *      -    PRODUCT_NAME: tên sản phẩm, String.
     *      -    PRODUCT_QUANTITY: Số lượng, Long.
     *      -    PRODUCT_PRICE: Giá trị, Long.
     *      -    PRODUCT_WEIGHT: Trọng lượng, Long.
     * 23   CHECK_UNIQUE        Body    Boolean    Không bắt buộc, giá trị = true/false tương đương với yêu cầu kiểm trùng mã đơn hàng hoặc không.
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function createOrderNlp($params = []) {
        $this->requestToken();
        $params = array_merge([
            'ORDER_NUMBER'        => 'BM848893946',
            'SENDER_FULLNAME'     => 'Duong An-04',
            'SENDER_ADDRESS'      => 'Soso18, Phường Thạnh Xuân, Quận 12,Hồ Chí Minh',
            'SENDER_PHONE'        => '09335656565',
            'RECEIVER_FULLNAME'   => 'Nguyễn Văn A',
            'RECEIVER_ADDRESS'    => 'Soso18, Phường Thạnh Xuân, Quận 12,Hồ Chí Minh',
            'RECEIVER_PHONE'      => '0987654321',
            'PRODUCT_NAME'        => 'Hàng test',
            'PRODUCT_DESCRIPTION' => ' Cho khách xem hàng khi nhận, cho xem hàng',
            'PRODUCT_QUANTITY'    => 1,
            'PRODUCT_PRICE'       => 10000000,
            'PRODUCT_WEIGHT'      => 10000,
            'PRODUCT_LENGTH'      => 0,
            'PRODUCT_WIDTH'       => 0,
            'PRODUCT_HEIGHT'      => 0,
            'ORDER_PAYMENT'       => 3,
            'ORDER_SERVICE'       => 'VCN',
            'ORDER_SERVICE_ADD'   => null,
            'ORDER_NOTE'          => ' Cho khách xem hàng khi nhận, cho xem hàng',
            'MONEY_COLLECTION'    => 56827,
            'CHECK_UNIQUE'        => true,
            'LIST_ITEM'           => [
                [
                    'PRODUCT_NAME'     => 'Hàng test',
                    'PRODUCT_QUANTITY' => 1,
                    'PRODUCT_PRICE'    => 10000000,
                    'PRODUCT_WEIGHT'   => 10000,
                ],
            ],
        ], $params);

        $response = $this->restfulRequest('post', $this->url . '/order/createOrderNlp', $params);
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    /**
     * Create Order | Tạo đơn đầy đủ thông tin
     * 1    Token               Header    String    Token tạo đơn của tài khoản client(Lấy ở mục 1)
     * 2    ORDER_NUMBER        Body    String    Mã đơn hàng
     * 3    SENDER_FULLNAME     Body    String    Tên khách hàng gửi
     * 4    SENDER_PHONE        Body    String    Số điện thoại khách hàng gửi
     * 5    SENDER_ADDRESS      Body    String    Địa chỉ đầy đủ của khách hàng gửi, địa chỉ tối đa 150 byte.
     * 6    SENDER_PROVINCE     Body    Long    ID Tỉnh gửi.
     * 7    SENDER_DISTRICT     Body    Long    ID Huyện gửi
     * 8    SENDER_WARDS        Body    Long    ID Phường xã gửi hàng
     * 9    RECEIVER_FULLNAME   Body    String    Tên khách hàng nhận
     * 10   RECEIVER_PHONE      Body    String    Số điện thoại khách hàng nhận
     * 11   RECEIVER_ADDRESS    Body    String    Địa chỉ đầy đủ của khách hàng nhận, địa chỉ tối đa 150 byte.
     * 12   RECEIVER_PROVINCE   Body    Long    ID Tỉnh nhận hàng
     * 13   RECEIVER_DISTRICT   Body    Long    ID Huyện nhận hàng
     * 14   RECEIVER_WARDS      Body    Long    ID Phường xã nhận hàng
     * 15   PRODUCT_NAME        Body    String    Tên gói hàng
     * 16   PRODUCT_DESCRIPTION Body    String    Mô tả(Cho xem hàng, thời gian giao, …), tối đa 150 byte.
     * 17   PRODUCT_QUANTITY    Body    Long    Tổng số lượng sản phẩm trong gói
     * 18   PRODUCT_PRICE       Body    Long    Tổng giá trị các sản phẩm trong gói
     * 19   PRODUCT_WEIGHT      Body    Long    Tổng trọng lượng các sản phẩm trong gói
     * 20   PRODUCT_LENGTH      Body    Long    Chiều dài(cm), không bắt buộc
     * 21   PRODUCT_WIDTH       Body    Long    Chiều rộng(cm), không bắt buộc
     * 22   PRODUCT_HEIGHT      Body    Long    Chiều cao(cm), không bắt buộc
     * 23   ORDER_PAYMENT       Body    Long    Loại vận đơn
     *      1. Không thu hộ
     *      2. Thu hộ tiền hàng và tiền cước
     *      3. Thu hộ tiền hàng
     *      4. Thu hộ tiền cước
     * 24   ORDER_SERVICE       Body    String    Mã dịch vụ, lấy từ Api lấy danh sách dịch vụ phù hợp hoặc tính cước.
     * 25   ORDER_SERVICE_ADD   Body    String    Mã dịch vụ cộng thêm lấy từ api danh sách dịch vụ phù hợp hoặc theo thông báo của nhân viên kinh doanh.
     * 26   ORDER_NOTE          Body    String    Ghi chú
     * 27   MONEY_COLLECTION    Body    Long    Tiền hàng cần thu hộ
     * 28   LIST_ITEM           Body    List<Object>    Danh sách hàng hóa chi tiết(Chỉ dùng để đối soát khi có thất thoát).
     *      List gồm các Object json có tham số như sau:
     *      -    PRODUCT_NAME: tên sản phẩm, kiểu String
     *      -    PRODUCT_QUANTITY: Số lượng, kiểu Long.
     *      -    PRODUCT_PRICE: Giá trị, kiểu Long.
     *      -    PRODUCT_WEIGHT: Trọng lượng(gr), kiểu Long.
     * 29   RETURN_ADDRESS      Body    Object    Thông tin địa chỉ hoàn hàng. Dạng json Object có các thuộc tính như sau:
     *      - REQUIRED: Xác nhận hoàn theo địa chỉ này, kiểu Boolean(true/false).
     *      - FULLADDRESS: Địa chỉ hoàn đầy đủ, kiểu String.
     *      - PROVINCE_ID: ID Tỉnh hoàn về, kiểu Long.
     *      - DISTRICT_ID: ID quận/Huyện hoàn về, kiểu Long.
     *      - WARDS_ID: ID Phường/xã hoàn về, kiểu Long.
     * 30   GROUPADDRESS_ID     Body    Long    Để = 0
     * 31   CHECK_UNIQUE        Body    Boolean(true/False)    Không bắt buộc. Sử dụng để check trùng Mã đơn hàng.
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function createOrder($params = []) {
        $this->requestToken();
        /*$params = array_merge([
            'ORDER_NUMBER'        => '12',
            'GROUPADDRESS_ID'     => 5818802,
            'CUS_ID'              => 722,
            'DELIVERY_DATE'       => '11/10/2018 15:09:52',
            'SENDER_FULLNAME'     => 'Yanme Shop',
            'SENDER_ADDRESS'      => 'Số 5A ngách 22 ngõ 282 Kim Giang, Đại Kim, Hoàng Mai, Hà Nội',
            'SENDER_PHONE'        => '0967.363.789',
            'SENDER_EMAIL'        => 'vanchinh.libra@gmail.com',
            'SENDER_WARD'         => 25,
            'SENDER_DISTRICT'     => 4,
            'SENDER_PROVINCE'     => 1,
            'SENDER_LATITUDE'     => 0,
            'SENDER_LONGITUDE'    => 0,
            'RECEIVER_FULLNAME'   => 'Hoàng - Test',
            'RECEIVER_ADDRESS'    => '1 NKKN P.Nguyễn Thái Bình, Quận 1, TP Hồ Chí Minh',
            'RECEIVER_PHONE'      => '0907882792',
            'RECEIVER_EMAIL'      => 'hoangnh50@fpt.com.vn',
            'RECEIVER_WARD'       => 25,
            'RECEIVER_DISTRICT'   => 43,
            'RECEIVER_PROVINCE'   => 2,
            'RECEIVER_LATITUDE'   => 0,
            'RECEIVER_LONGITUDE'  => 0,
            'PRODUCT_NAME'        => 'Máy xay sinh tố Philips HR2118 2.0L ',
            'PRODUCT_DESCRIPTION' => 'Máy xay sinh tố Philips HR2118 2.0L ',
            'PRODUCT_QUANTITY'    => 1,
            'PRODUCT_PRICE'       => 2292764,
            'PRODUCT_WEIGHT'      => 40000,
            'PRODUCT_LENGTH'      => 38,
            'PRODUCT_WIDTH'       => 24,
            'PRODUCT_HEIGHT'      => 25,
            'PRODUCT_TYPE'        => 'HH',
            'ORDER_PAYMENT'       => 3,
            'ORDER_SERVICE'       => 'VCN',
            'ORDER_SERVICE_ADD'   => '',
            'ORDER_VOUCHER'       => '',
            'ORDER_NOTE'          => 'cho xem hàng, không cho thử',
            'MONEY_COLLECTION'    => 2292764,
            'CHECK_UNIQUE'        => true,
            'LIST_ITEM'           => [
                [
                    'PRODUCT_NAME'     => 'Máy xay sinh tố Philips HR2118 2.0L ',
                    'PRODUCT_PRICE'    => 2150000,
                    'PRODUCT_WEIGHT'   => 2500,
                    'PRODUCT_QUANTITY' => 1,
                ],
            ],
        ], $params);*/

        $response = $this->restfulRequest('post', $this->url . '/order/createOrder', $params);
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    /**
     * Update order | Cập nhật thông tin đơn hàng | As same createOrderNlp
     * Request và response giống với Api tạo đơn(5), tuy nhiên đơn hàng chỉ được sửa khi trạng thái(ORDER_STATUS) < 200.
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function updateOrder($params = []) {
        $this->requestToken();
        $params = array_merge([
            'ORDER_NUMBER'        => 'BM848893946',
            'SENDER_FULLNAME'     => 'Duong An-04',
            'SENDER_ADDRESS'      => 'Soso18, Phường Thạnh Xuân, Quận 12,Hồ Chí Minh',
            'SENDER_PHONE'        => '09335656565',
            'RECEIVER_FULLNAME'   => 'Nguyễn Văn A',
            'RECEIVER_ADDRESS'    => 'Soso18, Phường Thạnh Xuân, Quận 12,Hồ Chí Minh',
            'RECEIVER_PHONE'      => '0987654321',
            'PRODUCT_NAME'        => 'Hàng test',
            'PRODUCT_DESCRIPTION' => ' Cho khách xem hàng khi nhận, cho xem hàng',
            'PRODUCT_QUANTITY'    => 1,
            'PRODUCT_PRICE'       => 10000000,
            'PRODUCT_WEIGHT'      => 10000,
            'PRODUCT_LENGTH'      => 0,
            'PRODUCT_WIDTH'       => 0,
            'PRODUCT_HEIGHT'      => 0,
            'ORDER_PAYMENT'       => 3,
            'ORDER_SERVICE'       => 'VCN',
            'ORDER_SERVICE_ADD'   => null,
            'ORDER_NOTE'          => ' Cho khách xem hàng khi nhận, cho xem hàng',
            'MONEY_COLLECTION'    => 56827,
            'CHECK_UNIQUE'        => true,
            'LIST_ITEM'           => [
                [
                    'PRODUCT_NAME'     => 'Hàng test',
                    'PRODUCT_QUANTITY' => 1,
                    'PRODUCT_PRICE'    => 10000000,
                    'PRODUCT_WEIGHT'   => 10000,
                ],
            ],
        ], $params);

        $response = $this->restfulRequest('post', $this->url . '/order/edit', $params);
        if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];
    }

    /**
     * Update status order | Cập nhật trạng thái vận đơn
     * - TYPE là loại cập nhật, bao gồm các loại sau
     *      Loại trạng thái:
     *      1. Duyệt đơn hàng
     *      2. Duyệt hoàn, gọi sau khi trạng thái 505(Thông báo chuyển hoàn) và khách hàng yêu cầu hoàn.
     *      3. Phát tiếp, gọi sau khi trạng thái 505(Thông báo chuyển hoàn) và khách hàng yêu cầu phát tiếp.
     *      4. Hủy đơn hàng, gọi khi đơn chưa nhận về(trạng thái < 200 và khác 105, 107)
     *      11. Xóa đơn hàng đã hủy, gọi sau khi trạng thái 107(Hủy đơn hàng).
     * - ORDER_NUMBER là  mã vận đơn cần cập nhật trạng thái
     * - NOTE là lý do cập nhật trạng thái. Truyền dạng String, không qua 150 ký tự.
     * @param $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function updateStatusOrder($params) {
        $this->requestToken();
        /*$params = array_merge([
            'TYPE'         => 4,
            'ORDER_NUMBER' => '11506020148',
            'NOTE'         => 'Ghi chú',
        ], $params);*/

        $response = $this->restfulRequest('post', $this->url . '/order/UpdateOrder', $params);

        return $response;
        /*if ($response['status'] != 200) throw new \InvalidArgumentException($response['message']);

        return $response['data'];*/
    }

    /**
     * Get Printing Code | Lấy link in vận đơn
     * - ORDER_ARRAY: Là mảng mã vận đơn cần tạo link in, tối đa 100 vận đơn
     * - EXPIRY_TIME: Là thời gian link hết hạn, đơn vị epoch milisecond.
     * @param $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPrintingCode($params) {
        $this->requestToken();
        $params = array_merge([
            'EXPIRY_TIME' => 0,
            'ORDER_ARRAY' => [],
        ], $params);

        return $this->restfulRequest('post', $this->url . '/order/printing-code', $params);
    }
}
