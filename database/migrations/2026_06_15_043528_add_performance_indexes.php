<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gold_transactions', function (Blueprint $table) {
            $table->index('reference_id');
            $table->index('created_by');
            $table->index('deleted_at');
            $table->index(['member_id', 'type']);
            $table->index(['member_id', 'type', 'gram']);
            $table->index(['member_id', 'type', 'total_amount']);
        });

        Schema::table('family_members', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('name');
            $table->index('deleted_at');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('gold_transactions', function (Blueprint $table) {
            $table->dropIndex('gold_transactions_reference_id_index');
            $table->dropIndex('gold_transactions_created_by_index');
            $table->dropIndex('gold_transactions_deleted_at_index');
            $table->dropIndex('gold_transactions_member_id_type_index');
            $table->dropIndex('gold_transactions_member_id_type_gram_index');
            $table->dropIndex('gold_transactions_member_id_type_total_amount_index');
        });

        Schema::table('family_members', function (Blueprint $table) {
            $table->dropIndex('family_members_is_active_index');
            $table->dropIndex('family_members_name_index');
            $table->dropIndex('family_members_deleted_at_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_user_id_index');
            $table->dropIndex('activity_logs_created_at_index');
        });
    }
};
