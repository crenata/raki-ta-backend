<?php

namespace App\Http\Controllers\User;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationImageModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ObservationController extends Controller {
    protected $observationTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
    }

    public function get(Request $request) {
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

        $observation = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::APPROVED
        ], $id);
        if (empty($observation->id)) return ResponseHelper::response(null, "Observation not found", 400);

        return ResponseHelper::response($observation);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "date" => "required|string|date",
            "latitude" => ["required", "regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
            "longitude" => ["required", "regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            "location" => "required|string",
            "description" => "required|string",
            "images" => "required|array",
            "images.*" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $observation = ObservationModel::create([
                "user_id" => auth()->id(),
                "name" => $request->name,
                "date" => $request->date,
                "latitude" => $request->latitude,
                "longitude" => $request->longitude,
                "location" => $request->location,
                "description" => $request->description
            ]);

            foreach ($request->file("images") as $file) {
                $image = Carbon::now()->format("Y-m-d-H-i") . "-observation-" . Str::random(12) . "." . $file->getClientOriginalExtension();
                Storage::disk("public")->putFileAs("observation", $file, $image);
                ObservationImageModel::create([
                    "observation_id" => $observation->id,
                    "image" => $image
                ]);
            }

            ObservationHistoryModel::create([
                "observation_id" => $observation->id,
                "status" => ObservationStatusConstant::PENDING
            ]);

            return ResponseHelper::response($observation);
        });
    }
}
