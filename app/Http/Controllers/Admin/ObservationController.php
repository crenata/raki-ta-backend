<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
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

    public function set($id, $status) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->observationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $observation = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::PENDING
        ], $id);
        if (empty($observation->id)) return ResponseHelper::response(
            null,
            "Already " . ($status === ObservationStatusConstant::APPROVED ? "approved" : "rejected"),
            400
        );

        ObservationHistoryModel::create([
            "observation_id" => $observation->id,
            "status" => $status
        ]);

        return ResponseHelper::response($observation);
    }

    public function approve(Request $request, $id) {
        return $this->set($id, ObservationStatusConstant::APPROVED);
    }

    public function reject(Request $request, $id) {
        return $this->set($id, ObservationStatusConstant::REJECTED);
    }
}
