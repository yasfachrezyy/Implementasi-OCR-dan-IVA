<?php
namespace App\Exports;

use App\Models\Permohonan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PermohonanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Permohonan::with(['client', 'service']);
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return ["Nama Pemohon", "Email", "Jenis Layanan", "Tanggal Masuk", "Status"];
    }

    public function map($permohonan): array
    {
        return [
            $permohonan->client->name,
            $permohonan->client->email,
            $permohonan->service->name,
            $permohonan->created_at->format('d-m-Y'),
            $permohonan->status,
        ];
    }
}