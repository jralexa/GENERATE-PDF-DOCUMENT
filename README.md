# AppTest

AppTest is a Laravel 12 web app for managing employee documents and generating downloadable PDFs from a fillable PDF template.

## What You Can Do

- Register/login users (Laravel Breeze auth).
- Create, view, edit, and list documents.
- Save document records to MySQL.
- Generate and download PDFs from document data.

## Stack

- PHP 8.3
- Laravel 12
- MySQL
- Vite + Tailwind CSS
- PDFTK (required for PDF generation)

## Quick Start (Local)

### 1) Prerequisites

Install:

- PHP 8.3+
- Composer
- Node.js + npm
- MySQL
- PDFTK (optional, but required for PDF downloads)

### 2) Clone and install

```bash
git clone <your-repo-url>
cd AppTest
composer install
npm install
```

### 3) Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials, then create that database in MySQL.

Default `.env.example` values:

- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=apptest`
- `DB_USERNAME=root`
- `DB_PASSWORD=`

### 4) Run migrations and seed

```bash
php artisan migrate --seed
```

This seeds a default user:

- Email: `test@example.com`
- Password: `password`

Optional sample documents:

```bash
php artisan db:seed --class=DocumentSeeder
```

### 5) Start the app

Use one terminal command for app server + queue + Vite:

```bash
composer run dev
```

Then open:

- `http://localhost:8000`

## Access Guide For New Users

After the app is running:

1. Open `http://localhost:8000/login`.
2. Login with seeded credentials, or register at `http://localhost:8000/register`.
3. Go to `http://localhost:8000/documents`.
4. Create a document and use:
   - `Save` (store record only), or
   - `Save & Download PDF` (store + generate PDF).

## PDF Setup

PDF download depends on:

- Template file at `storage/app/pdf/templates/document_template.pdf`
- PDFTK installed and executable

If PDFTK is not on PATH, set this in `.env` (Windows example):

```env
PDFTK_BINARY='C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe'
```

After changing `.env`:

```bash
php artisan config:clear
```

## Useful Commands

```bash
# Run all tests
php artisan test --compact

# Run document feature tests only
php artisan test --compact tests/Feature/DocumentTest.php

# Format code
vendor/bin/pint --format agent
```

## Troubleshooting

- `Vite manifest` errors: run `npm run build` or keep `composer run dev` running.
- `Document PDF template not found`: ensure template exists at `storage/app/pdf/templates/document_template.pdf`.
- `PDFTK is not installed or not executable`: install PDFTK and/or set `PDFTK_BINARY` correctly.
