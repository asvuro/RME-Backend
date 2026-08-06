<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imaging_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imaging_order_id')->constrained('imaging_orders');
            // Placeholder untuk Study Instance UID dari PACS nyata di masa depan.
            // Unique bila terisi karena UID memang identitas global studi di dunia DICOM.
            $table->string('study_instance_uid')->nullable()->unique();
            $table->dateTime('performed_at');
            $table->text('findings_summary')->nullable();
            $table->string('report_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_studies');
    }
};
