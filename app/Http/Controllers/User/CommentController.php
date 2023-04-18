<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CommentModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller {
    protected $observationTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
    }

    public function get(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->observationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $comments = CommentModel::with("user")
            ->where("observation_id", $id)
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($comments);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->observationTable,id",
            "comment" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $comment = CommentModel::create([
            "observation_id" => $request->id,
            "user_id" => auth()->id(),
            "comment" => $request->comment
        ]);

        return ResponseHelper::response($comment);
    }
}
