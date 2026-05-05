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
        Schema::table('chat_bots', function (Blueprint $table) {
            $table->string('reply_method')->default('text')->change();
        });

       // DB::statement("ALTER TABLE chat_bots MODIFY COLUMN reply_method ENUM('text', 'template', 'image')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
