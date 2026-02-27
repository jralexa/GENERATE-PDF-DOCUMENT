# Special Order Save & Generate PDF Documentation

## 1. Overview

This module implements a CRUD-lite flow for Special Orders and generates PDFs by filling an existing AcroForm template (not HTML-to-PDF).

Core goals:

1. Save Special Order records in the database.
2. Generate a flattened PDF from a fillable template.
3. Store generated PDFs for audit/reprint.
4. Reuse existing PDFs when data has not changed.
5. Regenerate PDFs when record data changes.

---

## 2. Feature Scope

Implemented features:

1. List Special Orders.
2. Create Special Order.
3. View Special Order.
4. Edit Special Order.
5. Save only.
6. Save and download PDF.
7. Download PDF from index/show/action route.

Routes:

1. `GET /special-orders`
2. `GET /special-orders/create`
3. `POST /special-orders`
4. `GET /special-orders/{special_order}`
5. `GET /special-orders/{special_order}/edit`
6. `PUT /special-orders/{special_order}`
7. `GET /special-orders/{special_order}/pdf`

---

## 3. Files Added / Updated

Core backend:

1. `database/migrations/2026_02_25_131924_create_special_orders_table.php`
2. `app/Models/SpecialOrder.php`
3. `app/Http/Requests/SpecialOrderRequest.php`
4. `app/Services/SpecialOrderPdfService.php`
5. `app/Http/Controllers/SpecialOrderController.php`
6. `routes/web.php`
7. `config/pdf.php`
8. `.env.example`

Views:

1. `resources/views/special-orders/index.blade.php`
2. `resources/views/special-orders/create.blade.php`
3. `resources/views/special-orders/edit.blade.php`
4. `resources/views/special-orders/show.blade.php`
5. `resources/views/special-orders/partials/form.blade.php`
6. `resources/views/layouts/navigation.blade.php` (menu link)

Test coverage:

1. `tests/Feature/SpecialOrderTest.php`
2. `database/factories/SpecialOrderFactory.php`
3. `database/seeders/SpecialOrderSeeder.php`

---

## 4. Data Model

Table: `special_orders`

| Column | Type | Notes |
|---|---|---|
| id | bigint | Primary key |
| document_date | date | Required |
| special_order_no | string(20) | Required |
| special_order_year | string(4) | Required |
| employee_name | string(150) | Required |
| position | string(150) | Required |
| assignment_station | string(200) | Required |
| conforme_name | string(150) | Required |
| pdf_path | string nullable | Relative storage path |
| created_at | timestamp | Managed by Laravel |
| updated_at | timestamp | Managed by Laravel |

`pdf_path` stores relative paths like:

`pdf/generated/special-order-15.pdf`

---

## 5. Validation Rules

Validation is centralized in `SpecialOrderRequest`:

1. `special_order_no`: required, string, max:20
2. `special_order_year`: required, digits:4
3. `document_date`: required, date
4. `employee_name`: required, string, max:150
5. `position`: required, string, max:150
6. `assignment_station`: required, string, max:200
7. `conforme_name`: required, string, max:150

Custom messages are defined for all rules.

---

## 6. PDF Template and Field Mapping

Template path:

`storage/app/pdf/templates/special_order_template.pdf`

AcroForm field mapping:

| PDF Field Name | Source |
|---|---|
| document_date | `special_orders.document_date` (formatted `Y-m-d`) |
| special_order_no | `special_orders.special_order_no` |
| special_order_year | `special_orders.special_order_year` |
| employee_name | `special_orders.employee_name` |
| position | `special_orders.position` |
| assignment_station | `special_orders.assignment_station` |
| conforme_name | `special_orders.conforme_name` |

Generation call chain:

`fillForm($data)->flatten()->saveAs($outputPath)`

Flattening makes output non-editable.

---

## 7. Configuration

Config file:

`config/pdf.php`

Keys:

1. `pdf.pdftk_binary`: optional absolute path to executable from `.env` (`PDFTK_BINARY`)
2. `pdf.special_order_template_path`: relative path (`pdf/templates/special_order_template.pdf`)

`.env` example:

```env
PDFTK_BINARY='C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe'
```

After changing `.env`, run:

```bash
php artisan config:clear
```

---

## 8. Storage Strategy and Path Portability

Generated output path format:

`storage/app/pdf/generated/special-order-{id}.pdf`

Stored in DB as relative:

`pdf/generated/special-order-{id}.pdf`

Important Laravel 12 detail:

The default `local` disk root is `storage/app/private`.  
This feature intentionally builds a filesystem adapter rooted at `storage/app` so generated files match required paths.

---

## 9. Runtime Flow: Save Only

Trigger:

`POST /special-orders` or `PUT /special-orders/{id}` with `action=save`

Flow:

1. Request enters controller.
2. `SpecialOrderRequest` validates input.
3. Record is created/updated.
4. Controller redirects:
   - Create: to index with success message.
   - Update: to edit with success message.
5. No PDF generation is attempted.

---

## 10. Runtime Flow: Save & Download PDF

Trigger:

`action=save_pdf` from create/edit form button.

Flow:

1. Request is validated.
2. Record is saved.
3. Controller calls `SpecialOrderPdfService::generate($specialOrder)`.
4. Service checks whether regeneration is needed.
5. If reuse is possible, existing file path is returned.
6. If regeneration is needed:
   - Validate template exists.
   - Ensure `pdf/generated` directory exists.
   - Create `Pdf` instance with optional `command` from `PDFTK_BINARY`.
   - Fill mapped fields.
   - Flatten.
   - Save output file.
   - Persist `pdf_path` in DB.
7. Controller returns `response()->download(...)` with filename:
   `special-order-{id}.pdf`

---

## 11. Runtime Flow: Download PDF Endpoint

Trigger:

`GET /special-orders/{special_order}/pdf`

Flow:

1. Controller calls `downloadPdf()`.
2. Service decides reuse vs regenerate.
3. File is returned as attachment download.

This endpoint supports:

1. Reprint existing generated PDF.
2. Automatic regeneration when record has been updated after file generation.

---

## 12. Regeneration Decision Logic

Regenerate if any of these are true:

1. `pdf_path` is `null`.
2. File at `pdf_path` does not exist.
3. File modified timestamp is older than `updated_at` timestamp.

Reuse if:

1. `pdf_path` exists in DB.
2. File exists on disk.
3. File modified timestamp is newer than or equal to record `updated_at`.

This preserves audit/reprint behavior while keeping PDF content aligned with latest record changes.

---

## 13. Error Handling

### 13.1 Missing Template

Condition:

Template file does not exist.

Response:

1. HTTP 500
2. Clear message:
   `Special order PDF template not found at ...`

### 13.2 Missing/Invalid PDFTK Binary

Condition:

1. `PDFTK_BINARY` path configured but file missing.
2. PDFTK execution fails with command-not-found/not-executable patterns.

Response:

1. HTTP 500
2. Message explains to install pdftk and/or set `PDFTK_BINARY`.

### 13.3 Fill/Save Failure

Condition:

`fillForm()->flatten()->saveAs(...)` returns false.

Behavior:

1. Detailed context logged:
   - special order id
   - template path
   - output path
   - configured binary
   - pdftk error text
2. User gets a friendly 500 message.

---

## 14. UI Behavior

Create/Edit form actions:

1. **Save** (`action=save`)
2. **Save & Download PDF** (`action=save_pdf`)

Index actions per row:

1. **View**
2. **Edit**
3. **Download PDF**

---

## 15. Testing Coverage

Feature tests validate:

1. Authenticated index access.
2. Save flow.
3. Save & download flow.
4. Update flow.
5. Download endpoint reuse behavior.
6. Template-missing failure behavior.
7. Validation enforcement.
8. Regeneration staleness logic.

Run:

```bash
php artisan test --compact tests/Feature/SpecialOrderTest.php
```

---

## 16. Operational Checklist

### First-time setup

1. Install package:
   `composer require mikehaertl/php-pdftk --no-interaction`
2. Install OS executable (`pdftk` / `pdftk.exe`).
3. Set `.env` `PDFTK_BINARY` if not on PATH.
4. Run migrations:
   `php artisan migrate --no-interaction`
5. Clear config:
   `php artisan config:clear`

### Quick health checks

1. `php artisan config:show pdf`
2. Confirm template exists:
   `storage/app/pdf/templates/special_order_template.pdf`
3. Use UI button **Save & Download PDF** on a test record.

---

## 17. Troubleshooting Guide

### Error: `Configured PDFTK_BINARY path [...] was not found`

Cause:

Path in `.env` is wrong.

Fix:

1. Find real executable path.
2. Update `.env`.
3. `php artisan config:clear`.

### Error: `The environment file is invalid!`

Cause:

Unquoted Windows path with spaces.

Fix:

Use quotes:

```env
PDFTK_BINARY='C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe'
```

### Error: PDF fields are blank

Possible causes:

1. Field names in template changed.
2. Template is not AcroForm fillable.
3. Mismatched field map keys in service.

Fix:

1. Confirm template field names.
2. Update `formData()` mapping in `SpecialOrderPdfService`.

---

## 18. Maintenance Notes

When template changes:

1. Keep field names stable whenever possible.
2. If field names change, update mapping in `SpecialOrderPdfService::formData()`.
3. Re-test using Save & Download flow.

When adding new DB fields:

1. Add migration column.
2. Update model fillable/casts.
3. Update request validation.
4. Update form UI.
5. Update PDF field mapping if needed.
6. Add/adjust tests.

