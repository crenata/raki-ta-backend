<?php

use App\Constants\ObservationStatusConstant;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
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
        Schema::create($this->getTable(new ObservationHistoryModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("observation_id");
            $table->unsignedBigInteger("status")->default(ObservationStatusConstant::PENDING);
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("observation_id")->references("id")->on($this->getTable(new ObservationModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new ObservationHistoryModel()));
    }
};
