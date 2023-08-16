<?php

namespace App\Models;

class ProvinceModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "provinces";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
