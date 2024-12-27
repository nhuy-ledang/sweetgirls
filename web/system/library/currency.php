<?php
class Currency {
    protected $currencies = [];

    public function __construct($registry) {
        $this->language = $registry->get('language');

        // Predefined currency data (replace with your own data)
        $this->currencies = [
            'USD' => [
                'id'            => 1,
                'title'         => 'US Dollar',
                'symbol_left'   => '$',
                'symbol_right'  => '',
                'decimal_place' => 2,
                'value'         => 1.0
            ],
            'EUR' => [
                'id'            => 2,
                'title'         => 'Việt Nam Đồng',
                'symbol_left'   => '',
                'symbol_right'  => 'đ',
                'decimal_place' => 0,
                'value'         => 1.00000000
            ],
            // Add more currencies as needed
        ];
    }

    public function format($number, $currency, $value = '', $format = true) {
        if (!isset($this->currencies[$currency])) {
            return 'Currency not found';
        }

        $symbol_left = $this->currencies[$currency]['symbol_left'];
        $symbol_right = $this->currencies[$currency]['symbol_right'];
        $decimal_place = $this->currencies[$currency]['decimal_place'];

        if (!$value) {
            $value = $this->currencies[$currency]['value'];
        }

        $amount = $value ? (float)$number * $value : (float)$number;
        $amount = round($amount, (int)$decimal_place);

        if (!$format) {
            return $amount;
        }

        $string = '';

        if ($symbol_left) {
            $string .= $symbol_left;
        }

        $string .= number_format($amount, (int)$decimal_place, $this->language->get('decimal_point'), $this->language->get('thousand_point'));

        if ($symbol_right) {
            $string .= $symbol_right;
        }

        return $string;
    }

    public function convert($value, $from, $to) {
        if (isset($this->currencies[$from]) && isset($this->currencies[$to])) {
            $from_value = $this->currencies[$from]['value'];
            $to_value = $this->currencies[$to]['value'];
            return $value * ($to_value / $from_value);
        } else {
            return 0; // Return 0 if currency is not found
        }
    }

    public function getId($currency) {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency]['id'] : 0;
    }

    public function getSymbolLeft($currency) {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency]['symbol_left'] : '';
    }

    public function getSymbolRight($currency) {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency]['symbol_right'] : '';
    }

    public function getDecimalPlace($currency) {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency]['decimal_place'] : 0;
    }

    public function getValue($currency) {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency]['value'] : 0;
    }

    public function has($currency) {
        return isset($this->currencies[$currency]);
    }
}
