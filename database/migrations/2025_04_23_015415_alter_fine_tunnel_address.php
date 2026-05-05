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
        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->string('zip_code')->nullable()->after('message_limit');
            $table->decimal('weight', 22, 4)->default(0)->after('zip_code'); 
            $table->string('address')->nullable()->after('weight');
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
