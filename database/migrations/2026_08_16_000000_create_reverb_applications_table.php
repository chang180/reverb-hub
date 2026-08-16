<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reverb_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('app_id')->unique();
            $table->string('key')->unique();
            $table->text('secret');
            $table->json('allowed_origins');
            $table->unsignedInteger('max_connections')->nullable();
            $table->unsignedInteger('ping_interval')->default(60);
            $table->unsignedInteger('activity_timeout')->default(30);
            $table->unsignedInteger('max_message_size')->default(10_000);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reverb_applications');
    }
};
