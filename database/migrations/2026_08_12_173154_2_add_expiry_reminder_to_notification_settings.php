<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notification_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('notification_settings', 'whatsapp_expiry_reminder')) {
                $table->enum('whatsapp_expiry_reminder', ['yes','no'])->default('no')->after('email_approval_payment_template');
                $table->uuid('whatsapp_expiry_reminder_template')->nullable()->after('whatsapp_expiry_reminder');
                $table->enum('whatsapp_expired_reminder', ['yes','no'])->default('no')->after('whatsapp_expiry_reminder_template');
                $table->uuid('whatsapp_expired_reminder_template')->nullable()->after('whatsapp_expired_reminder');
                $table->enum('email_expiry_reminder', ['yes','no'])->default('no')->after('whatsapp_expired_reminder_template');
                $table->uuid('email_expiry_reminder_template')->nullable()->after('email_expiry_reminder');
                $table->enum('email_expired_reminder', ['yes','no'])->default('no')->after('email_expiry_reminder_template');
                $table->uuid('email_expired_reminder_template')->nullable()->after('email_expired_reminder');
            }
        });
    }
    public function down(): void {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_expiry_reminder','whatsapp_expiry_reminder_template',
                'whatsapp_expired_reminder','whatsapp_expired_reminder_template',
                'email_expiry_reminder','email_expiry_reminder_template',
                'email_expired_reminder','email_expired_reminder_template',
            ]);
        });
    }
};
