<?php namespace Modules\Order\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderByDateExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents {
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
            'Ngày',
            'Tổng doanh số',
            'Tổng số đơn hàng',
            'Tổng số lượt xem sản phẩm',
            'Số lượt truy cập',
            'Tỉ lệ chuyển đổi',
            'Đơn đã hủy',
            'Doanh số đơn hủy',
            'Đơn đã hoàn trả / hoàn tiền',
            'Doanh số trả hàng / hoàn tiền',
            'Số người mua',
            'Số người mua mới',
            'Số người mua hiện tại',
        ];
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

                $event->sheet->getDelegate()->getStyle('A1:M1')->applyFromArray($styleArray);
            },
        ];
    }
}
