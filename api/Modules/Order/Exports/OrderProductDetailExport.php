<?php namespace Modules\Order\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderProductDetailExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents {
    protected $rows;

    public function __construct(array $rows) {
        $this->rows = $rows;
    }

    public function array(): array {
        return $this->rows;
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            'STT',
            'Mã đơn hàng',
            'Ngày đặt hàng',
            'Trạng thái đơn hàng',
            //'Lý do hủy',
            'Mã vận đơn',
            'Đơn vị vận chuyển',
            'Phương thức giao hàng',
            'Ngày gửi hàng',
            'SKU sản phẩm',
            'Tên sản phẩm',
            'Cân nặng sản phẩm',
            'Giá gốc',
            'Giá khuyến mãi',
            'Số lượng',
            'Tổng giá bán(sản phẩm)',
            'Tổng giá trị đơn hàng',
            'Mã giảm giá',
            'Coin',
            'Phí vận chuyển',
            'Phí vận chuyển mà người mua trả',
            'Ưu đãi phí vận chuyển',
            'Thời gian hoàn thành đơn hàng',
            'Thời gian đơn hàng được thanh toán',
            'Phương thức thanh toán',
            'Người mua',
            'Tên người nhận',
            'Số điện thoại',
            'Tỉnh/Thành phố',
            'TP/Quận/Huyện',
            'Quận/Phường',
            'Địa chỉ nhận hàng',
            'Ghi chú',
            'Yêu cầu hóa đơn',
            'Tên công ty',
            'Mã số thuế',
            'Địa chỉ',
            'Email',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Cố định hàng khi cuộn
                $event->sheet->getDelegate()->freezePane('A2');

                $styleArray = [
                    'font'    => [
                        'bold'  => true,
                        'size'  => 12,
                        //'name'  => 'Calibri',
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'fill'    => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF1E86CF',
                        ],
                    ],
                ];

                $event->sheet->getDelegate()->getStyle('A1:AK1')->applyFromArray($styleArray);
            },
        ];
    }
}
