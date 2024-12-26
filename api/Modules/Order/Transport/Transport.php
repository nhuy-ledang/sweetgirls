<?php namespace Modules\Order\Transport;

class Transport {
    public function __construct() {
    }

    private function getMethod($carrier = 'viettelpost') {
        $method = new ViettelPost();

        return $method;
    }

    public function getProvinces() {
        $method = $this->getMethod();

        return $method->getProvinces();
    }

    public function getDistricts($provinceId) {
        $method = $this->getMethod();

        return $method->getDistricts($provinceId);
    }

    // Test login
    public function login() {
        $method = $this->getMethod();

        return $method->login();
    }

    /**
     * Get Price All | Lấy danh sách dịch vụ phù hợp với hành trình
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPriceAll($params = []) {
        $method = $this->getMethod();

        return $method->getPriceAll($params);
    }

    /**
     * Get Price | Tính cước
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPrice($params = []) {
        $method = $this->getMethod();

        return $method->getPrice($params);
    }

    /**
     * Create Order Nlp | Tạo đơn
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function createOrderNlp($params = []) {
        $method = $this->getMethod();

        return $method->createOrderNlp($params);
    }

    /**
     * Create Order | Tạo đơn đầy đủ thông tin
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function createOrder($params = []) {
        $method = $this->getMethod();

        return $method->createOrder($params);
    }

    /**
     * Update order | Cập nhật thông tin đơn hàng | As same createOrderNlp
     * Request và response giống với Api tạo đơn(5), tuy nhiên đơn hàng chỉ được sửa khi trạng thái(ORDER_STATUS) < 200.
     * @param array $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function updateOrder($params = []) {
        $method = $this->getMethod();

        return $method->updateOrder($params);
    }

    /**
     * Update status order | Cập nhật trạng thái vận đơn
     * @param $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function updateStatusOrder($params) {
        $method = $this->getMethod();

        return $method->updateStatusOrder($params);
    }

    /**
     * Get Printing Code | Lấy link in vận đơn
     * @param $params
     * @return mixed
     * @throws TokenExpiredException
     */
    public function getPrintingCode($params) {
        $method = $this->getMethod();

        return $method->getPrintingCode($params);
    }
}
