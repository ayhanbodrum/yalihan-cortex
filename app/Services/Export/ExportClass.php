<?php

namespace App\Services\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

/**
 * Context7: Excel Export Class
 * 
 * Handles Excel formatting and data mapping
 */
class ExportClass implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $data;
    protected array $headers;
    protected string $type;

    public function __construct(Collection $data, array $headers, string $type = '')
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->type = $type;
    }

    /**
     * Get collection data
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Get headings
     */
    public function headings(): array
    {
        return $this->headers;
    }

    /**
     * Map data rows
     */
    public function map($row): array
    {
        // Auto-detect type from data if not set
        if (!$this->type) {
            $this->type = $this->detectType($row);
        }

        return match ($this->type) {
            'ilan' => $this->mapIlan($row),
            'kisi' => $this->mapKisi($row),
            'talep' => $this->mapTalep($row),
            default => $this->mapGeneric($row),
        };
    }

    /**
     * Detect type from row
     * 
     * @param mixed $row
     * @return string
     */
    protected function detectType(mixed $row): string
    {
        if (isset($row->baslik) && isset($row->fiyat)) {
            return 'ilan';
        }
        if (isset($row->ad) && isset($row->soyad)) {
            if (isset($row->tip)) {
                return 'talep';
            }
            return 'kisi';
        }
        return '';
    }

    /**
     * Map generic row
     * 
     * @param mixed $row
     * @return array
     */
    protected function mapGeneric(mixed $row): array
    {
        return array_values((array) $row);
    }

    /**
     * Map Ilan row
     * 
     * @param mixed $ilan
     * @return array
     */
    protected function mapIlan(mixed $ilan): array
    {
        return [
            $ilan->id,
            $ilan->baslik,
            number_format($ilan->fiyat ?? 0, 2, ',', '.'),
            $ilan->para_birimi ?? 'TRY',
            $ilan->status ?? 'Bilinmiyor',
            $ilan->ilanSahibi ? ($ilan->ilanSahibi->ad . ' ' . $ilan->ilanSahibi->soyad) : '-',
            $ilan->il->il_adi ?? '-',
            $ilan->ilce->ilce_adi ?? '-',
            $ilan->anaKategori->name ?? '-',
            $ilan->altKategori->name ?? '-',
            $ilan->created_at ? $ilan->created_at->format('d.m.Y H:i') : '-',
        ];
    }

    /**
     * Map Kisi row
     * 
     * @param mixed $kisi
     * @return array
     */
    protected function mapKisi(mixed $kisi): array
    {
        return [
            $kisi->id,
            trim(($kisi->ad ?? '') . ' ' . ($kisi->soyad ?? '')),
            $kisi->telefon ?? '-',
            $kisi->email ?? '-',
            $kisi->kisi_tipi ?? $kisi->musteri_tipi ?? '-',
            $kisi->status ? 'Aktif' : 'Pasif',
            $kisi->danisman->name ?? '-',
            $kisi->il->il_adi ?? '-',
            $kisi->ilce->ilce_adi ?? '-',
            $kisi->created_at ? $kisi->created_at->format('d.m.Y H:i') : '-',
        ];
    }

    /**
     * Map Talep row
     * 
     * @param mixed $talep
     * @return array
     */
    protected function mapTalep(mixed $talep): array
    {
        return [
            $talep->id,
            $talep->baslik ?? '-',
            $talep->tip ?? '-',
            $talep->status ?? '-',
            $talep->kisi ? ($talep->kisi->ad . ' ' . $talep->kisi->soyad) : '-',
            $talep->kisi ? ($talep->kisi->telefon ?? '-') : '-',
            $talep->il->il_adi ?? '-',
            $talep->ilce->ilce_adi ?? '-',
            $talep->created_at ? $talep->created_at->format('d.m.Y H:i') : '-',
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Get sheet title
     */
    public function title(): string
    {
        return match ($this->type) {
            'ilan' => 'İlanlar',
            'kisi' => 'Kişiler',
            'talep' => 'Talepler',
            default => 'Rapor',
        };
    }
}

