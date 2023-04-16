<?php

namespace App\Models;

class CommentModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "comments";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "observation_id",
        "user_id",
        "comment",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function observation() {
        return $this->belongsTo(ObservationModel::class, "observation_id");
    }

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }
}
