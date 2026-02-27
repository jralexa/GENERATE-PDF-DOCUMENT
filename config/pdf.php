<?php

/*
|--------------------------------------------------------------------------
| PDF Generation
|--------------------------------------------------------------------------
|
| Install PDFTK before using this feature:
| - Linux: install `pdftk` or `pdftk-java` (package manager).
| - Windows: install PDFtk Server, then set the full executable path.
|
*/

return [
    'pdftk_binary' => env('PDFTK_BINARY'),
    'document_template_path' => 'pdf/templates/document_template.pdf',
];
