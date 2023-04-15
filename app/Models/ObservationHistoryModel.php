<?php

namespace App\Models;

class ObservationHistoryModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "observation_histories";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "observation_id",
        "status",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
