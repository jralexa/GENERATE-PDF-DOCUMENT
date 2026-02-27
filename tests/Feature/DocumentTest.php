<?php

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentPdfService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

function appStorageDisk(): \Illuminate\Filesystem\FilesystemAdapter
{
    return Storage::build([
        'driver' => 'local',
        'root' => storage_path('app'),
    ]);
}

function documentPayload(array $overrides = []): array
{
    return array_merge([
        'document_date' => '2026-02-25',
        'document_no' => 'DOC-1001',
        'document_year' => '2026',
        'employee_name' => 'Jane Employee',
        'position' => 'Operations Officer',
        'assignment_station' => 'Manila Station',
        'conforme_name' => 'John Conforme',
    ], $overrides);
}

beforeEach(function (): void {
    appStorageDisk()->deleteDirectory('pdf/generated');
});

test('documents page is displayed for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('documents.index'));

    $response->assertOk();
});

test('dashboard is displayed without metrics data', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSeeText('Dashboard')
        ->assertDontSeeText('Operations Overview')
        ->assertDontSeeText('Total Documents')
        ->assertDontSeeText('Pending PDF');
});

test('document can be saved', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('documents.store'), documentPayload([
            'action' => 'save',
        ]));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('documents.index'));

    $this->assertDatabaseHas('documents', [
        'document_date' => '2026-02-25 00:00:00',
        'document_no' => 'DOC-1001',
        'document_year' => '2026',
        'employee_name' => 'Jane Employee',
        'position' => 'Operations Officer',
        'assignment_station' => 'Manila Station',
        'conforme_name' => 'John Conforme',
    ]);
});

test('document can be saved and downloaded as pdf', function () {
    $user = User::factory()->create();

    $this->mock(DocumentPdfService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generate')
            ->once()
            ->andReturnUsing(function (Document $document): string {
                appStorageDisk()->makeDirectory('pdf/generated');
                appStorageDisk()->put($document->generatedPdfRelativePath(), 'pdf');

                return $document->generatedPdfAbsolutePath();
            });
    });

    $response = $this
        ->actingAs($user)
        ->post(route('documents.store'), documentPayload([
            'action' => 'save_pdf',
        ]));

    $document = Document::query()->firstOrFail();

    $response->assertDownload($document->pdfDownloadFilename());
});

test('document can be updated', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put(route('documents.update', $document), documentPayload([
            'employee_name' => 'Updated Employee',
            'document_no' => 'DOC-2002',
            'action' => 'save',
        ]));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('documents.edit', $document));

    $document->refresh();

    expect($document->employee_name)->toBe('Updated Employee');
    expect($document->document_no)->toBe('DOC-2002');
});

test('download endpoint reuses existing generated pdf when record has not changed', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $relativePath = $document->generatedPdfRelativePath();

    $document->forceFill(['pdf_path' => $relativePath])->save();

    appStorageDisk()->makeDirectory('pdf/generated');
    appStorageDisk()->put($relativePath, 'existing pdf');

    $response = $this
        ->actingAs($user)
        ->get(route('documents.pdf', $document));

    $response->assertDownload($document->pdfDownloadFilename());
});

test('download endpoint returns clear error when template is missing', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Config::set('pdf.document_template_path', 'pdf/templates/missing-document-template.pdf');

    $response = $this
        ->actingAs($user)
        ->get(route('documents.pdf', $document));

    $response->assertInternalServerError();
    $response->assertSeeText('Document PDF template not found');
});

test('pdf service throws clear template missing message', function () {
    $document = Document::factory()->create();

    Config::set('pdf.document_template_path', 'pdf/templates/missing-document-template.pdf');

    expect(fn () => app(DocumentPdfService::class)->generate($document))
        ->toThrow(\RuntimeException::class, 'Document PDF template not found');
});

test('pdf service maps every document field into pdf form data', function () {
    $document = Document::factory()->make([
        'document_date' => '2026-02-25',
        'document_no' => 'DOC-5123',
        'document_year' => '2026',
        'employee_name' => 'Mary Employee',
        'position' => 'Senior Analyst',
        'assignment_station' => 'Cebu Station',
        'conforme_name' => 'Mark Conforme',
    ]);

    $method = new ReflectionMethod(DocumentPdfService::class, 'formData');
    $method->setAccessible(true);
    $formData = $method->invoke(app(DocumentPdfService::class), $document);

    expect($formData)->toBe([
        'document_date' => '2026-02-25',
        'document_no' => 'DOC-5123',
        'document_year' => '2026',
        'special_order_no' => 'DOC-5123',
        'special_order_year' => '2026',
        'employee_name' => 'Mary Employee',
        'position' => 'Senior Analyst',
        'assignment_station' => 'Cebu Station',
        'conforme_name' => 'Mark Conforme',
    ]);
});

test('document validation rules are enforced', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('documents.store'), [
            'action' => 'save',
        ]);

    $response
        ->assertSessionHasErrors([
            'document_no',
            'document_year',
            'document_date',
            'employee_name',
            'position',
            'assignment_station',
            'conforme_name',
        ]);
});

test('document marks pdf as stale when updated after generation time', function () {
    appStorageDisk()->makeDirectory('pdf/generated');

    $document = Document::factory()->create();

    expect($document->shouldRegeneratePdf())->toBeTrue();

    $relativePath = $document->generatedPdfRelativePath();
    $document->forceFill(['pdf_path' => $relativePath])->save();
    appStorageDisk()->put($relativePath, 'generated pdf');

    expect($document->fresh()->shouldRegeneratePdf())->toBeFalse();

    Document::withoutTimestamps(function () use ($document): void {
        $document->forceFill(['updated_at' => now()->addMinute()])->save();
    });

    expect($document->fresh()->shouldRegeneratePdf())->toBeTrue();
});

test('document uses neutral default pdf naming', function () {
    $document = Document::factory()->create();

    expect($document->generatedPdfRelativePath())->toBe('pdf/generated/document-'.$document->id.'.pdf');
    expect($document->pdfDownloadFilename())->toBe('document-'.$document->id.'.pdf');
});
