<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_orders', function (Blueprint $table) {
            // Jumlah berapa kali direvisi
            $table->unsignedInteger('revision_count')->default(0)->after('status');
            // Timestamp terakhir kali direvisi
            $table->timestamp('last_revised_at')->nullable()->after('revision_count');
        });
    }

    public function down(): void
    {
        Schema::table('request_orders', function (Blueprint $table) {
            $table->dropColumn(['revision_count', 'last_revised_at']);
        });
    }
};