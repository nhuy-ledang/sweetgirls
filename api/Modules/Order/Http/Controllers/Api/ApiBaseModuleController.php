<?php namespace Modules\Order\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 *
 * @package Modules\Order\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 11:03 PM
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "order";

    public function getReportDates() {
        $start_date = null;
        $end_date = null;
        $data = $this->getRequestData();
        $report_year = !empty($data->{'report_year'}) ? (int)$data->{'report_year'} : false;
        if ($report_year) return ["$report_year-01-01", "$report_year-12-31"];
        $report_months = !empty($data->{'report_months'}) ? $data->{'report_months'} : false;
        $report_from = !empty($data->{'report_from'}) ? $data->{'report_from'} : false;
        $report_to = !empty($data->{'report_to'}) ? $data->{'report_to'} : false;
        if ($report_months == 'this_month') { // Tháng này
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } else if ($report_months == 'this_year') { // Năm nay
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
        } else if ($report_months == 'last_year') { // Năm trước
            $start_date = date('Y-01-01', strtotime('-1 year', time()));
            $end_date = date('Y-12-31', strtotime('-1 year', time()));
        } else if ($report_months == '1') { // Tháng trước
            $start_date = date('Y-m-01', strtotime('-1 month', time()));
            $end_date = date('Y-m-t', strtotime('-1 month', time()));
        } else if (intval($report_months) > 1) { // Ba tháng qua
            $report_months = (int)$report_months;
            $start_date = date('Y-m-d', strtotime("-$report_months month", time()));
        } else { // custom
            if ($report_from) $start_date = $report_from;
            if ($report_to) $end_date = $report_to;
        }

        return [$start_date, $end_date];
    }
}
