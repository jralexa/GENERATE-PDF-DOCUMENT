<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Services\DocumentPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(private DocumentPdfService $documentPdfService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('documents.index', [
            'documents' => Document::query()->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('documents.create', [
            'document' => new Document(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentRequest $request): RedirectResponse|BinaryFileResponse|Response
    {
        $document = Document::query()->create($request->validated());

        if ($this->shouldDownloadPdf($request->input('action'))) {
            return $this->downloadPdf($document);
        }

        return Redirect::route('documents.index')->with('status', 'Document saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): View
    {
        return view('documents.show', [
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document): View
    {
        return view('documents.edit', [
            'document' => $document,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentRequest $request, Document $document): RedirectResponse|BinaryFileResponse|Response
    {
        $document->update($request->validated());

        if ($this->shouldDownloadPdf($request->input('action'))) {
            return $this->downloadPdf($document->refresh());
        }

        return Redirect::route('documents.edit', $document)->with('status', 'Document updated successfully.');
    }

    /**
     * Download the generated PDF.
     */
    public function pdf(Document $document): BinaryFileResponse|Response
    {
        return $this->downloadPdf($document);
    }

    private function shouldDownloadPdf(?string $action): bool
    {
        return $action === 'save_pdf';
    }

    private function downloadPdf(Document $document): BinaryFileResponse|Response
    {
        try {
            return response()->download(
                $this->documentPdfService->generate($document),
                $document->pdfDownloadFilename(),
            );
        } catch (RuntimeException $exception) {
            return response($exception->getMessage(), 500);
        } catch (Throwable $exception) {
            Log::error('Unexpected document PDF error.', [
                'document_id' => $document->id,
                'error' => $exception->getMessage(),
            ]);

            return response('Unable to generate the PDF right now.', 500);
        }
    }
}
