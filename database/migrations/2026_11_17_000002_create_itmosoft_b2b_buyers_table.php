<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_b2b_buyers', function (Blueprint $table) {
            $table->id();
            $table->morphs('account');
            $table->morphs('buyer');
            $table->string('role', 24)->default('buyer');
            $table->string('status', 24)->default('active')->index();
            $table->unsignedBigInteger('spending_limit_minor')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['account_type', 'account_id', 'buyer_type', 'buyer_id'], 'itmosoft_b2b_buyer_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_b2b_buyers');
    }
};
