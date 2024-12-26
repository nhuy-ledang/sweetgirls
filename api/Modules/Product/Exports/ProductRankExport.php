<?php namespace Modules\Product\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductRankExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents {
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
            'Mã sp',
            'Tên sản phẩm',
            'Danh mục',
            'Lượt truy cập',
            'Số lượng bán',
            'Tỷ lệ chuyển đổi',
            'Doanh thu',
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

                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray($styleArray);
            },
        ];
    }
}
