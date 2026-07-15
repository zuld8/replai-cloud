<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menu Otomatis — 4 tabel engine:
 * chat_flows, chat_flow_nodes, chat_flow_options, chat_flow_sessions
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── chat_flows ─────────────────────────────────────────
        if (!Schema::hasTable('chat_flows')) {
            Schema::create('chat_flows', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('business_id')->index();
                $table->uuid('merchant_id')->nullable();
                $table->string('name');
                $table->enum('trigger_type', ['keyword', 'welcome', 'default'])->default('keyword');
                $table->json('trigger_keywords')->nullable();   // ["halo","menu"]
                $table->json('channels')->nullable();            // device id array, empty=all
                $table->uuid('start_node_id')->nullable();
                $table->enum('fallback_action', ['ai_agent', 'manual_reply', 'repeat_menu'])->default('ai_agent');
                $table->unsignedInteger('session_timeout_min')->default(30);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->index(['business_id', 'status']);
            });
        }

        // ── chat_flow_nodes ────────────────────────────────────
        if (!Schema::hasTable('chat_flow_nodes')) {
            Schema::create('chat_flow_nodes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('flow_id')->index();
                $table->enum('type', ['message', 'buttons', 'list', 'handoff']);
                $table->text('body_text')->nullable();
                $table->string('header', 60)->nullable();
                $table->string('footer', 60)->nullable();
                $table->string('list_button_label', 20)->nullable();
                $table->uuid('handoff_assign_to')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }

        // ── chat_flow_options ──────────────────────────────────
        if (!Schema::hasTable('chat_flow_options')) {
            Schema::create('chat_flow_options', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('node_id')->index();
                $table->enum('kind', ['button', 'list_row']);
                $table->string('label', 24);
                $table->string('description', 72)->nullable();
                $table->string('section')->nullable();
                $table->integer('order')->default(0);
                $table->enum('target_action', ['goto_node', 'handoff', 'back_to_start', 'end'])->default('goto_node');
                $table->uuid('target_node_id')->nullable();
                $table->string('reply_id')->index();   // unique id dikirim ke WA → pakai routing balasan
                $table->timestamps();
            });
        }

        // ── chat_flow_sessions ─────────────────────────────────
        if (!Schema::hasTable('chat_flow_sessions')) {
            Schema::create('chat_flow_sessions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('business_id')->index();
                $table->uuid('history_chat_id')->index();
                $table->uuid('flow_id');
                $table->uuid('current_node_id')->nullable();
                $table->enum('status', ['active', 'handoff', 'ended'])->default('active');
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                $table->index(['history_chat_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_flow_sessions');
        Schema::dropIfExists('chat_flow_options');
        Schema::dropIfExists('chat_flow_nodes');
        Schema::dropIfExists('chat_flows');
    }
};
