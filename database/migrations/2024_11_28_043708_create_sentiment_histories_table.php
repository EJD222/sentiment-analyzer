<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentimentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentiment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('text');
            $table->json('analysis_result'); // Store the analysis result as a JSON field
            $table->json('emotion_scores'); // Store emotion scores as JSON
            $table->text('highlighted_text');
            $table->timestamps();
            $table->softDeletes(); // Add the softDeletes column (deleted_at)

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sentiment_histories');
    }
}
