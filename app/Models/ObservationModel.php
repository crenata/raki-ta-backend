<?php

namespace App\Models;

class ObservationModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "observations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "name",
        "date",
        "latitude",
        "longitude",
        "location",
        "description",
        "image",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/observation/" . $this->attributes["image"];
    }

    public function histories() {
        return $this->hasMany(ObservationHistoryModel::class, "observation_id");
    }

    public function latestHistory() {
        return $this->hasOne(ObservationHistoryModel::class, "observation_id")->orderByDesc("id");
    }
}
