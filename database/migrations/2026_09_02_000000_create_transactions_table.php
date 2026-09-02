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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedBigInteger('login_id')->nullable()->index();
            $table->unsignedBigInteger('ride_id')->nullable()->index();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->string('type', 50);
            $table->string('reference', 250)->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedBigInteger('transaction_date')->nullable();
            $table->integer('is_reconcile')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};