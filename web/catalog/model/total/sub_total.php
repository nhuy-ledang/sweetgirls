<?php
class ModelTotalSubTotal extends Model {
    public function getTotal(&$totals, &$taxes, &$total) {
        $this->load->language('extension/total/sub_total');

        $sub_total = $this->cart->getSubTotal();

        /*if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $sub_total += $voucher['amount'];
            }
        }*/

        $totals['sub_total'] = [
            'code'       => 'sub_total',
            'title'      => $this->language->get('text_sub_total'),
            'value'      => $sub_total,
        ];

        $total += $sub_total;
    }
}
