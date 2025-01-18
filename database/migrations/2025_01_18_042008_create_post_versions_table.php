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
        /**
         * Stores
         */
        Schema::create('post_versions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('keywords');
            $table->string('edited_by');
            $table->integer('start_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_versions');
    }
};
