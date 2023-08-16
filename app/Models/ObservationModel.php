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
        "province_id",
        "name",
        "date",
        "latitude",
        "longitude",
        "description",
        "local_name",
        "found",
        "substrate",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function province() {
        return $this->belongsTo(ProvinceModel::class, "province_id");
    }

    public function histories() {
        return $this->hasMany(ObservationHistoryModel::class, "observation_id");
    }

    public function images() {
        return $this->hasMany(ObservationImageModel::class, "observation_id");
    }

    public function comments() {
        return $this->hasMany(CommentModel::class, "observation_id");
    }

    public function latestHistory() {
        return $this->hasOne(ObservationHistoryModel::class, "observation_id")->orderByDesc("id");
    }
}
