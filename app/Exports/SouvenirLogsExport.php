<?php

namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Carbon;

class SouvenirLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    use Exportable;

    protected $index = 0;
    protected $type;
    protected $table;

    public function type($type = ""){
        $this->type = $type;
        return $this;
    }
    
    public function table($table = ""){
        $this->table = $table;
        return $this;
    }

    public function collection()
    {
        $where = [];
        $this->type != "" ? $where['type_invitation'] = $this->type : "";
        $this->table != "" ? $where['table_number_invitation'] = $this->table : "";

        $invt = Invitation::where('souvenir_claimed', true)
            ->where($where)
            ->orderBy('souvenir_claimed_at', "DESC")
            ->get();

        return $invt;
    }

    public function headings(): array
    {
        return [
            'No',
            'Qr Code',
            'Nama',
            'Keterangan',
            'Jenis Tamu',
            'No Meja',
            'Waktu Pengambilan Souvenir',
            'Status'
        ];
    }
    
    public function map($invt): array
    {
        return [
            ++$this->index,
            $invt->qrcode_invitation,
            $invt->name_guest,
            $invt->information_invitation,
            ucwords($invt->type_invitation),
            $invt->table_number_invitation,
            Carbon::parse($invt->souvenir_claimed_at)->format('d M Y H:i:s'),
            $invt->souvenir_claimed ? 'Sudah Diambil' : 'Belum Diambil'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->collection()->count();
         
        $sheet->getStyle('A1:H1')->applyFromArray([
            "fill" => [
                'fillType' => 'solid',
                'rotation' => 0,
                'color' =>
                [
                    'rgb' => 'FFFF00'
                ],
            ],
            "font" => [
                "bold" => true
            ]
        ]);

        $sheet->getStyle('A1:H'.($totalRows+1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                "horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]
        ]);
    }
} 