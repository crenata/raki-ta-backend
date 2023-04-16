<?php

namespace App\Models;

class NotificationModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "notifications";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "observation_id",
        "admin_id",
        "user_id",
        "title",
        "description",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function observation() {
        return $this->belongsTo(ObservationModel::class, "observation_id");
    }

    public function admin() {
        return $this->belongsTo(AdminModel::class, "admin_id");
    }

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }
}
