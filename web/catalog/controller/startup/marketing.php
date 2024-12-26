<?php
class ControllerStartupMarketing extends Controller {
	public function index() {
		// Tracking Code
		if (isset($this->request->get['tap_a'])) {
		    $dayNum = (int)$this->config->get('config_tracking_day');
            if (!$dayNum || ($dayNum && $dayNum < 0)) $dayNum = 1;
			setcookie('tracking', $this->request->get['tap_a'], time() + 3600 * 24 * $dayNum, '/');

			/*$this->load->model('marketing/marketing');

			$marketing_info = $this->model_marketing_marketing->getMarketingByCode($this->request->get['tap_a']);

			if ($marketing_info) {
				$this->model_marketing_marketing->addMarketingReport($marketing_info['marketing_id'], $this->request->server['REMOTE_ADDR']);
			}*/

            /*$this->load->model('account/affiliate');

            $affiliate_info = $this->model_account_affiliate->getAffiliateByTracking($this->request->get['tap_a']);

            if ($affiliate_info) {
                $this->model_account_affiliate->addAffiliateReport($affiliate_info['customer_id'], $this->request->server['REMOTE_ADDR']);
            }*/
		}
	}
}
