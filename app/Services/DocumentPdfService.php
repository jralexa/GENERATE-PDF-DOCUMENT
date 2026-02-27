<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use mikehaertl\pdftk\Pdf;

class DocumentPdfService
{
    public function generate(Document $document): string
    {
        $relativeOutputPath = $document->generatedPdfRelativePath();
        $disk = $this->appStorageDisk();

        if (! $document->shouldRegeneratePdf() && $disk->exists($relativeOutputPath)) {
            return storage_path('app/'.$relativeOutputPath);
        }

        $templatePath = storage_path('app/'.config('pdf.document_template_path'));

        if (! file_exists($templatePath)) {
            throw new RuntimeException('Document PDF template not found at '.$templatePath.'.');
        }

        $disk->makeDirectory('pdf/generated');

        $pdf = $this->newPdfInstance($templatePath);
        $outputPath = storage_path('app/'.$relativeOutputPath);

        $saved = $pdf
            ->fillForm($this->formData($document))
            ->flatten()
            ->saveAs($outputPath);

        if (! $saved) {
            $pdftkError = method_exists($pdf, 'getError') ? (string) $pdf->getError() : 'Unknown PDFTK error.';

            Log::error('Document PDF generation failed.', [
                'document_id' => $document->id,
                'template_path' => $templatePath,
                'output_path' => $outputPath,
                'pdftk_binary' => config('pdf.pdftk_binary'),
                'error' => $pdftkError,
            ]);

            throw new RuntimeException($this->humanReadablePdftkMessage($pdftkError));
        }

        if ($document->pdf_path !== $relativeOutputPath) {
            $document->forceFill(['pdf_path' => $relativeOutputPath])->save();
        }

        return $outputPath;
    }

    private function newPdfInstance(string $templatePath): Pdf
    {
        if (! class_exists(Pdf::class)) {
            throw new RuntimeException('The mikehaertl/php-pdftk package is not installed. Install it and optionally set PDFTK_BINARY in .env.');
        }

        $options = [];
        $configuredBinary = config('pdf.pdftk_binary');

        if (is_string($configuredBinary) && trim($configuredBinary) !== '') {
            if (! file_exists($configuredBinary)) {
                throw new RuntimeException('Configured PDFTK_BINARY path ['.$configuredBinary.'] was not found. Install pdftk and/or fix PDFTK_BINARY in .env.');
            }

            $options['command'] = $configuredBinary;
        }

        return new Pdf($templatePath, $options);
    }

    /**
     * @return array<string, string>
     */
    private function formData(Document $document): array
    {
        return [
            'document_date' => $document->document_date?->format('Y-m-d') ?? '',
            'document_no' => $document->document_no,
            'document_year' => $document->document_year,
            'special_order_no' => $document->document_no,
            'special_order_year' => $document->document_year,
            'employee_name' => $document->employee_name,
            'position' => $document->position,
            'assignment_station' => $document->assignment_station,
            'conforme_name' => $document->conforme_name,
        ];
    }

    private function humanReadablePdftkMessage(string $pdftkError): string
    {
        $normalizedError = strtolower($pdftkError);

        if (
            str_contains($normalizedError, 'not found')
            || str_contains($normalizedError, 'not recognized')
            || str_contains($normalizedError, 'executable')
            || str_contains($normalizedError, 'unable to execute')
        ) {
            return 'PDFTK is not installed or not executable. Install pdftk and set PDFTK_BINARY in .env when the binary is not on PATH.';
        }

        return 'Unable to generate the PDF right now. Please try again or contact support.';
    }

    private function appStorageDisk(): FilesystemAdapter
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app'),
        ]);
    }
}
