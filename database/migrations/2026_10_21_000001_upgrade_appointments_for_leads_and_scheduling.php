<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->uuid('case_id')->nullable()->change();
            $table->foreignUuid('lead_id')->nullable()->after('case_id')->constrained('leads')->nullOnDelete();
            $table->foreignUuid('assigned_to_id')->nullable()->after('lead_id')->constrained('users')->nullOnDelete();
            $table->string('title')->nullable()->after('assigned_to_id');
            $table->string('meeting_link')->nullable()->after('location_name');
            $table->string('client_name')->nullable()->after('meeting_link');
            $table->string('client_phone')->nullable()->after('client_name');
            $table->string('client_email')->nullable()->after('client_phone');

            $table->index(['appointment_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['appointment_time', 'status']);
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['assigned_to_id']);
            $table->dropColumn([
                'lead_id',
                'assigned_to_id',
                'title',
                'meeting_link',
                'client_name',
                'client_phone',
                'client_email',
            ]);
        });
    }
};
