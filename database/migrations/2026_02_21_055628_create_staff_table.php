<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('position');               // Jabatan: Dekan, Wakil Dekan, Dosen, dll
            $table->string('title')->nullable();       // Gelar: S.Kep, M.Kep, Dr., Prof.
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('education')->nullable();   // Pendidikan terakhir
            $table->text('expertise')->nullable();     // Bidang keahlian
            $table->text('bio')->nullable();
            $table->foreignId('program_study_id')->nullable()->constrained('program_studies')->nullOnDelete();
            $table->string('staff_type')->default('dosen'); // dosen, tendik, pimpinan
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
