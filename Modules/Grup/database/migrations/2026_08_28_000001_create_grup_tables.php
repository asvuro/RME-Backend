<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grup_groups')) {
            Schema::create('grup_groups', function (Blueprint $table) {
                $table->id();
                $table->uuid('hub_group_id')->unique();
                $table->string('legal_name');
                $table->string('legal_identifier')->nullable();
                $table->string('status', 32)->default('active');
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('grup_branches')) {
            Schema::create('grup_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('grup_groups')->cascadeOnDelete();
                $table->uuid('hub_branch_id')->unique();
                $table->string('instance_id')->unique();
                $table->string('code', 64);
                $table->string('name');
                $table->string('status', 32)->default('active');
                $table->boolean('is_local')->default(false);
                $table->json('capabilities')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();
                $table->unique(['group_id', 'code']);
                $table->index(['group_id', 'status']);
            });
        }

        if (! Schema::hasTable('grup_referrals')) {
            Schema::create('grup_referrals', function (Blueprint $table) {
                $table->id();
                $table->uuid('hub_referral_id')->unique();
                $table->foreignId('group_id')->constrained('grup_groups')->cascadeOnDelete();
                $table->foreignId('source_branch_id')->constrained('grup_branches')->restrictOnDelete();
                $table->foreignId('destination_branch_id')->constrained('grup_branches')->restrictOnDelete();
                $table->foreignId('local_patient_id')->nullable()->constrained('patients')->nullOnDelete();
                $table->string('source_patient_id');
                $table->text('patient_snapshot');
                $table->text('reason');
                $table->text('clinical_summary')->nullable();
                $table->string('status', 32)->default('requested');
                $table->uuid('last_event_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('referred_at');
                $table->timestamps();
                $table->index(['group_id', 'destination_branch_id', 'status'], 'grup_referrals_destination_status_idx');
            });
        }

        if (! Schema::hasTable('grup_hub_nonces')) {
            Schema::create('grup_hub_nonces', function (Blueprint $table) {
                $table->id();
                $table->uuid('request_id')->unique();
                $table->timestamp('received_at');
            });
        }

        if (! Schema::hasTable('grup_realtime_events')) {
            Schema::create('grup_realtime_events', function (Blueprint $table) {
                $table->id();
                $table->uuid('event_id')->unique();
                $table->string('event_type', 80);
                $table->foreignId('branch_id')->nullable()->constrained('grup_branches')->nullOnDelete();
                $table->text('payload');
                $table->timestamp('received_at');
                $table->timestamp('processed_at')->nullable();
                $table->text('failure_reason')->nullable();
                $table->timestamps();
                $table->index(['processed_at', 'received_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grup_realtime_events');
        Schema::dropIfExists('grup_hub_nonces');
        Schema::dropIfExists('grup_referrals');
        Schema::dropIfExists('grup_branches');
        Schema::dropIfExists('grup_groups');
    }
};
