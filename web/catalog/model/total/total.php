<?php
class ModelTotalTotal extends Model {
    public function getTotal(&$totals, &$taxes, &$total) {
        $this->load->language('extension/total/total');
        $totals['total'] = [
            'code'       => 'total',
            'title'      => $this->language->get('text_total'),
            'value'      => max(0, $total),
        ];
    }
}
