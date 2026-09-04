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
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('car_id')->after('group_id');
            $table->string('car_name', 250)->nullable()->after('car_id');
            $table->decimal('commission', 10, 2)->default(0)->after('car_name');

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
            $table->dropColumn(['car_id', 'car_name', 'commission']);
        });
    }
};