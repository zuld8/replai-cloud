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
        Schema::create('modul_fitures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('role_id')->nullable()->after('id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        // Pivot table untuk permission dan modul
        Schema::create('permission_modul_fiture', function (Blueprint $table) {
            $table->uuid('permission_id');
            $table->uuid('modul_fiture_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('modul_fiture_id')
                ->references('id')
                ->on('modul_fitures')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'modul_fiture_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_fitures');
    }
};
