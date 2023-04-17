<?php

use App\Models\ObservationModel;
use App\Models\UserModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create($this->getTable(new ObservationModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->string("name");
            $table->date("date");
            $table->unsignedDouble("latitude");
            $table->unsignedDouble("longitude");
            $table->longText("location");
            $table->longText("description");
            $table->string("image");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("user_id")->references("id")->on($this->getTable(new UserModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new ObservationModel()));
    }
};