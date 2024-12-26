<?php namespace Modules\Order\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents {
    protected $rows;
    protected $max;

    public function __construct(array $rows, $max) {
        $this->rows = $rows;
        $this->max = $max;
    }

    public function array(): array {
        return $this->rows;
    }

    /**
     * @return array
     */
    public function headings(): array {
        $dynamicHeaders = [];
        for ($index = 1; $index <= $this->max; $index++) {
            $dynamicHeaders[] = "SKU sản phẩm $index";
            $dynamicHeaders[] = "Số lượng sản phẩm $index";
            $dynamicHeaders[] = "Sản phẩm $index";
        }

        return array_merge([
            'STT',
            '#',
            'Mã đơn hàng',
            'Tạm tính',
            'Giảm giá',
            'Phí vận chuyển',
            'Tổng tiền',
            'Thời gian',
            'Khách hàng',
            'Mã KH',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'Tình trạng thanh toán',
            'Tình trạng vận chuyển',
            'Tình trạng đơn hàng',
            'Ngày thanh toán',
            'Phương thức thanh toán',
        ], $dynamicHeaders);
    }

    /**
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
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

                $event->sheet->getDelegate()->getStyle('A1:R1')->applyFromArray($styleArray);
            },
        ];
    }
}
