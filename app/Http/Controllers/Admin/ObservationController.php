<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\NotificationModel;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ObservationController extends Controller {
    protected $observationTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
    }

    public function get(Request $request) {
        $observations = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::PENDING
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

        $observation = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::PENDING
        ], $id);
        if (empty($observation->id)) return ResponseHelper::response(null, "Observation not found", 400);

        return ResponseHelper::response($observation);
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
}
