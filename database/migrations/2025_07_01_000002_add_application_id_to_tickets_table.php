<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable()->after('urgensi');
                $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'application_id')) {
                $table->dropForeign(['application_id']);
                $table->dropColumn('application_id');
            }
        });
    }
}; 