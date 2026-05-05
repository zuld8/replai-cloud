<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->enum('master_type', ['yes', 'no'])->default('yes')->after('button_or_list');
            $table->enum('media_type', ['text', 'audio', 'video', 'image', 'document'])->default('text')->after('type_content');
        });

        Schema::table('message_templates', function (Blueprint $table) {
            $table->string('type_content')->nullable('description')->nullable()->change();
        });

        // DB::statement("ALTER TABLE message_templates MODIFY COLUMN type_content ENUM('description', 'button', 'list','vote','location')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
