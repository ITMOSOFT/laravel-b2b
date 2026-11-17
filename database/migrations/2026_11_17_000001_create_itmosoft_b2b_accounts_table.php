<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_b2b_accounts', function (Blueprint $table) {
            $table->id();
            $table->morphs('accountable');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('status', 24)->default('pending')->index();
            $table->char('currency', 3)->default('USD');
            $table->unsignedBigInteger('minimum_order_minor')->default(0);
            $table->unsignedBigInteger('maximum_order_minor')->nullable();
            $table->boolean('requires_purchase_order')->default(false);
            $table->boolean('requires_quote')->default(false);
            $table->unsignedSmallInteger('payment_terms_days')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['accountable_type', 'accountable_id', 'status'], 'itmosoft_b2b_owner_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_b2b_accounts');
    }
};
