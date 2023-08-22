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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('resource')->nullable();
            $table->enum('experience',['Nothing','Some stuff','Lots'])->default('Nothing');
            $table->enum('shift',['Morning','Afternoon','Evening']);
            $table->time('scheduled_time');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_subscribed')->default(false);
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
