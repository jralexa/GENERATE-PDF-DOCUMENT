<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'document_date',
        'document_no',
        'document_year',
        'employee_name',
        'position',
        'assignment_station',
        'conforme_name',
        'pdf_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_date' => 'date',
        ];
    }

    public function generatedPdfRelativePath(): string
    {
        return 'pdf/generated/document-'.$this->id.'.pdf';
    }

    public function generatedPdfAbsolutePath(): string
    {
        return storage_path('app/'.$this->generatedPdfRelativePath());
    }

    public function pdfDownloadFilename(): string
    {
        return 'document-'.$this->id.'.pdf';
    }

    public function shouldRegeneratePdf(): bool
    {
        if ($this->pdf_path === null) {
            return true;
        }

        $disk = $this->appStorageDisk();

        if (! $disk->exists($this->pdf_path)) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $disk->lastModified($this->pdf_path) < $this->updated_at->timestamp;
    }

    private function appStorageDisk(): FilesystemAdapter
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app'),
        ]);
    }
}
