<?php

namespace App\Http\Controllers\User;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationImageModel;
use App\Models\ObservationModel;
use App\Models\ProvinceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ObservationController extends Controller {
    protected $observationTable, $provinceTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
        $this->provinceTable = (new ProvinceModel())->getTable();
    }

    public function get(Request $request) {
        $observations = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::APPROVED
        ]);

        return ResponseHelper::response($observations);
    }

    public function getProvince(Request $request) {
        $provinces = ProvinceModel::all();

        return ResponseHelper::response($provinces);
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
            "location" => "required|numeric|exists:$this->provinceTable,id",
            "description" => "required|string",
            "local_name" => "required|string",
            "found" => "required|string",
            "substrate" => "required|string",
            "images" => "required|array",
            "images.*" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $observation = ObservationModel::create([
                "user_id" => auth()->id(),
                "province_id" => $request->location,
                "name" => $request->name,
                "date" => $request->date,
                "latitude" => $request->latitude,
                "longitude" => $request->longitude,
                "description" => $request->description,
                "local_name" => $request->local_name,
                "found" => $request->found,
                "substrate" => $request->substrate
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
