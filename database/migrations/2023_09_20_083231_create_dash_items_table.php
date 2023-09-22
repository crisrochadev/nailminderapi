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
        Schema::create('dash_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->longText('element');
            $table->longText('classe');
            $table->longText('style');
            $table->longText('content');
            $table->unsignedBigInteger('layout_id');
            $table->foreign('layout_id')->references('id')->on('user_layout_pages');
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('dash_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dash_items');
    }
};
