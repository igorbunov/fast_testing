<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('creator_email', 50)->default('');
            $table->string('edit_link', 150)->unique();
            $table->integer('test_time_minutes')->default(30 * 60);
            $table->integer('is_active')->default(0);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
