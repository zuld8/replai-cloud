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
        Schema::table('message_templates', function (Blueprint $table) {
            $table->enum('for_waba', ['yes', 'no'])->default('no')->after('type_content');
            $table->string('waba_status_template')->nullable()->after('for_waba');
            $table->uuid('meta_id')->nullable()->after('waba_status_template');
            $table->string('category')->nullable()->after('meta_id');
            $table->string('lang')->nullable()->after('category'); 
            $table->uuid('created_by')->nullable()->after('lang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
