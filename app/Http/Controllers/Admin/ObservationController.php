<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\CommentModel;
use App\Models\NotificationModel;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ObservationController extends Controller {
    protected $observationTable, $commentTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
        $this->commentTable = (new CommentModel())->getTable();
    }

    public function get(Request $request) {
        $observations = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::PENDING
        ]);

        return ResponseHelper::response($observations);
    }

    public function getApproved(Request $request) {
        $observations = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::APPROVED
        ]);

        return ResponseHelper::response($observations);
    }

    public function getDetail(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->observationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $observation = ObservationModel::with("histories", "latestHistory", "user", "comments.user")->find($id);
        if (empty($observation->id)) return ResponseHelper::response(null, "Observation not found", 400);

        return ResponseHelper::response($observation);
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "date" => "required|string|date",
            "latitude" => ["required", "regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
            "longitude" => ["required", "regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            "location" => "required|string",
            "description" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $observation = ObservationModel::find($request->id);
            $observation->name = $request->name;
            $observation->date = $request->date;
            $observation->latitude = $request->latitude;
            $observation->longitude = $request->longitude;
            $observation->location = $request->location;
            $observation->description = $request->description;
            if ($request->hasFile("image")) {
                $image = Carbon::now()->format("Y-m-d-H-i") . "-observation-" . Str::random(12) . "." . $request->file("image")->getClientOriginalExtension();
                Storage::disk("public")->putFileAs("observation", $request->file("image"), $image);
                $observation->image = $image;
            }
            $observation->save();

            return ResponseHelper::response($observation);
        });
    }

    public function set($id, $status) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->observationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $isApproved = $status === ObservationStatusConstant::APPROVED;

        $observation = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::PENDING
        ], $id);
        if (empty($observation->id)) return ResponseHelper::response(
            null,
            "Already " . ($isApproved ? "approved" : "rejected"),
            400
        );

        return DB::transaction(function () use ($status, $isApproved, $observation) {
            ObservationHistoryModel::create([
                "observation_id" => $observation->id,
                "status" => $status
            ]);

            if ($isApproved) $description = "Congratulations.., Your observation has been approved by Administrator.";
            else $description = "Sorry, Your observation has been rejected by Administrator. Feel free to re-submit again.";
            NotificationModel::create([
                "observation_id" => $observation->id,
                "admin_id" => auth()->id(),
                "user_id" => $observation->user_id,
                "title" => "Your observation has been " . ($isApproved ? "approved" : "rejected"),
                "description" => $description
            ]);

            return ResponseHelper::response($observation);
        });
    }

    public function approve(Request $request, $id) {
        return $this->set($id, ObservationStatusConstant::APPROVED);
    }

    public function reject(Request $request, $id) {
        return $this->set($id, ObservationStatusConstant::REJECTED);
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->observationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $observation = ObservationModel::find($id)->delete();

        return ResponseHelper::response($observation);
    }

    public function deleteComment(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->commentTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $observation = CommentModel::find($id)->delete();

        return ResponseHelper::response($observation);
    }
}
