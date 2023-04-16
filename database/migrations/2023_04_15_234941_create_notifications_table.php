<?php

use App\Models\AdminModel;
use App\Models\NotificationModel;
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
        Schema::create($this->getTable(new NotificationModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("observation_id");
            $table->unsignedBigInteger("admin_id");
            $table->unsignedBigInteger("user_id");
            $table->string("title");
            $table->longText("description");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("observation_id")->references("id")->on($this->getTable(new ObservationModel()))->onDelete("cascade");
            $table->foreign("admin_id")->references("id")->on($this->getTable(new AdminModel()))->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on($this->getTable(new UserModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new NotificationModel()));
    }
};
