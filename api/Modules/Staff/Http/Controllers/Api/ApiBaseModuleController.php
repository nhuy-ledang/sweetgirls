<?php namespace Modules\Staff\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 *
 * @package Modules\Staff\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 11:03 PM
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "order";

    /**
     * Get config emails
     * @return array
     */
    protected function getConfigEmails() {
        $emails = [];
        $alert_email = $this->setting_repository->findByKey('config_mail_alert_email');
        if ($alert_email) {
            $alert_emails = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($alert_email)));
            $temps = [];
            foreach ($alert_emails as $alert_email) {
                $e2 = explode(',', (string)$alert_email);
                foreach ($e2 as $email) if (trim($email)) $temps[] = trim($email);
            }
            $emails = array_unique($temps);
        }

        return $emails;
    }

    /**
     * Save Invoice To File
     *
     * @param $model
     * @param $order
     * @param $contract
     * @param $user
     * @param $products
     * @return array
     * @throws \Throwable
     */
    protected function saveInvoiceToFile($model, $order, $contract, $user, $products) {
        $status = '';
        if ($model->status == 'paid') {
            $status = '<div style="font-weight: bold;color: #0acf97">ĐÃ THANH TOÁN</div>';
        } else if ($model->status == 'in_process') {
            $status = '<div style="font-weight: bold;color: #fd7e14">ĐÃ THANH TOÁN MỘT PHẦN</div>';
        } else if ($model->status == 'draft' || $model->status == 'new' || $model->status == 'approved') {
            if ($model->end_date && strtotime($model->end_date) < time()) {
                $status = '<div style="font-weight: bold;color: #dc3545">QUÁ HẠN</div>';
            } else {
                $status = '<div style="font-weight: bold;color: #dc3545">CHƯA THANH TOÁN</div>';
            }
        }
        if (!$model->is_bill) {
            $notify_footer = "<p><strong>Lưu ý/ <em>Notices</em>:&nbsp;</strong>&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br />
<strong>Khoản thanh toán có VAT, Quý khách vui lòng thanh toán theo thông tin dưới đây:<br />
<em>The payment include VAT, please pay as the information below:</em></strong><br />
- Số tài khoản/ <em>Account number</em>: <strong>6574457</strong><br />
- Đơn vị thụ hưởng/ <em>Beneficiary</em>: CÔNG TY CỔ PHẦN ENGINER<br />
- <strong>Ngân hàng TMCP Á Châu</strong> (ACB) - Chi nhánh Thành Phố Hồ Chí Minh<br />
<em>&nbsp; Asia Commercial Joint Stock Bank (ACB) - Ho Chi Minh Branch</em><br />
<br />
- Để nhận hóa đơn điện tử đề nghị Quý khách truy cập địa chỉ email khách hàng đã đăng ký<br />
<em>&nbsp; For receipt of our electronic invoices, please log in to your registered email</em><br />
<br />
<strong>Khoản thanh toán không có VAT, Quý khách vui lòng thanh toán theo thông tin dưới đây:<br />
<em>The payment does not include VAT, please pay as the information below:</em></strong><br />
- Số tài khoản/ <em>Account number</em>: <strong>931823888</strong><br />
- Người thụ hưởng/ <em>Beneficiary</em>: NGUYỄN TRÍ NHÂN<br />
- <strong>Ngân hàng TMCP Á Châu</strong> (ACB) - Chi nhánh Thành Phố Hồ Chí Minh<br />
<em>&nbsp; Asia Commercial Joint Stock Bank (ACB) - Ho Chi Minh Branch</em></p>";
            $notify_footer = $this->setting_repository->findByKey('config_invoice_notify_footer', $notify_footer);
        } else {
            $notify_footer = "<p><strong>Lưu ý/ <em>Notices</em>:</strong><br />
- Quý khách vui lòng thanh toán hết số tiền đến hạn để đảm bảo dịch vụ sử dụng thông suốt.<br />
<em>&nbsp; Users are requested to pay, in full and on time, all debts to avoid any interruption of service</em><br />
<br />
<strong>Khoản thanh toán có VAT, Quý khách vui lòng thanh toán theo thông tin dưới đây:<br />
<em>The payment include VAT, please pay as the information below:</em></strong><br />
- Số tài khoản/ <strong>Account number</strong>: <strong>6574457</strong><br />
- Đơn vị thụ hưởng/ <em>Beneficiary</em>: CÔNG TY CỔ PHẦN ENGINER<br />
- <strong>Ngân hàng TMCP Á Châu</strong> (ACB) - Chi nhánh Thành Phố Hồ Chí Minh<br />
&nbsp;<em> Asia Commercial Joint Stock Bank (ACB) - Ho Chi Minh Branch</em><br />
<br />
- Để nhận hóa đơn điện tử đề nghị Quý khách hàng truy cập địa chỉ email khách hàng đã đăng ký<br />
<em>&nbsp; For receipt of our electronic invoices, please log in to your registered email</em><br />
<br />
<strong>Khoản thanh toán không có VAT, Quý khách vui lòng thanh toán theo thông tin dưới đây:<br />
<em>The payment does not include VAT, please pay as the information below:</em></strong><br />
- Số tài khoản/ <em>Account number</em>: <strong>931823888</strong><br />
- Người thụ hưởng/ <em>Beneficiary</em>: NGUYỄN TRÍ NHÂN<br />
- <strong>Ngân hàng TMCP Á Châu</strong> (ACB) - Chi nhánh Thành Phố Hồ Chí Minh<br />
<em>&nbsp; Asia Commercial Joint Stock Bank (ACB) - Ho Chi Minh Branch</em><br />
<br />
- Motila xin chân thành cảm ơn sự hỗ trợ của Quý khách trong thời gian qua và rất mong được tiếp tục phục vụ Quý khách.<br />
<em>&nbsp; Motila thanks for your support during the past time and we are looking forward to continue serve you.</em></p>";
            $notify_footer = $this->setting_repository->findByKey('config_bill_notify_footer', $notify_footer);
        }
        $data = compact('model', 'order', 'contract', 'user', 'products', 'status', 'notify_footer');
        /*// Save to html
        $filepath = (!$model->is_bill ? 'invoices' : 'bill') . '/' . date('Y-m') . '/' . $model->id . '.html';
        $html = (view('order::print_invoice', $data))->render();
        \Storage::disk('files')->getDriver()->put($filepath, $html, ['visibility' => 'public']);*/
        // Save to pdf
        $html = (view('order::print_invoice_pdf', $data))->render();
        $pdf = \PDF::loadHTML($html);
        $pdf->setOptions(['dpi' => 150]);
        $filepath = (!$model->is_bill ? 'invoices' : 'bill') . '/' . date('Y-m') . '/' . $model->id . '.pdf';
        \Storage::disk('files')->getDriver()->put($filepath, $pdf->output(), ['visibility' => 'public']);
        $attach_file = storage_path('app/files/' . $filepath);

        return [$pdf, $attach_file, $data];
    }

    /**
     * Send email alert
     *
     * @param $model
     * @param array $emails
     * @param bool $attached
     * @param string $content
     * @param string $sender
     * @param string $subject_override
     * @throws \Throwable
     */
    protected function sendEmailInvoice($model, $emails = [], $attached = true, $content = '', $sender = '', $subject_override = '') {
        $order = $model->order_id ? $model->order : null;
        if ($order) {
            $contract = $order->contract;
            $user = $contract->user;
        } else {
            $contract = null;
            $user = $model->user;
        }
        $products = $model->products;
        if (!$emails) {
            $emails = [[$user->email], $this->getConfigEmails()];
        }
        // Save Invoice To File
        list($pdf, $attach_file, $data) = $this->saveInvoiceToFile($model, $order, $contract, $user, $products);
        // Send email
        $contact_name = $user->company ? $user->company : $user->first_name;
        $invoice_number = $model->no;
        $data = compact('contact_name', 'invoice_number');
        $data['clientnote'] = $model->clientnote;
        $data['terms'] = $model->terms;
        $data['is_bill'] = $model->is_bill;
        $subject = 'Thông báo thanh toán';
        if ($model->status == 'paid') {
            $subject = 'Thông báo đã thanh toán';
        } else if ($model->status == 'in_process') {
            $subject = 'Thông báo đã thanh toán một phần';
        } else if ($model->status == 'draft' || $model->status == 'new' || $model->status == 'approved') {
            if ($model->end_date && strtotime($model->end_date) < time()) {
                $subject = 'Thông báo đã quá han thanh toán';
            } else {
                $subject = 'Thông báo chưa thanh toán';
            }
        }
        $data['subject'] = $subject_override ? $subject_override : $subject;
        $data['content'] = $content;
        $data['sender'] = $sender;

        dispatch(new \Modules\Staff\Jobs\InvoiceJob($this->email, compact('emails', 'attached', 'attach_file', 'data')));
    }
}
