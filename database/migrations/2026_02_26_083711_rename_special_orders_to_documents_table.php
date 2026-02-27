<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('special_orders')) {
            Schema::table('special_orders', function (Blueprint $table) {
                if (Schema::hasColumn('special_orders', 'special_order_no')) {
                    $table->renameColumn('special_order_no', 'document_no');
                }

                if (Schema::hasColumn('special_orders', 'special_order_year')) {
                    $table->renameColumn('special_order_year', 'document_year');
                }
            });

            Schema::rename('special_orders', 'documents');

            return;
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'special_order_no')) {
                    $table->renameColumn('special_order_no', 'document_no');
                }

                if (Schema::hasColumn('documents', 'special_order_year')) {
                    $table->renameColumn('special_order_year', 'document_year');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'document_no')) {
                    $table->renameColumn('document_no', 'special_order_no');
                }

                if (Schema::hasColumn('documents', 'document_year')) {
                    $table->renameColumn('document_year', 'special_order_year');
                }
            });

            Schema::rename('documents', 'special_orders');
        }
    }
};
