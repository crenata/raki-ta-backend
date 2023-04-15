<?php

namespace App\Http\Controllers\User;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ObservationController extends Controller {
    public function get(Request $request) {
        $observations = (new GeneralObservationController())->get([
            "detail.status = " . ObservationStatusConstant::APPROVED
        ]);

        return ResponseHelper::response($observations);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "date" => "required|string|date",
            "latitude" => ["required", "regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
            "longitude" => ["required", "regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            "location" => "required|string",
            "description" => "required|string",
            "image" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $image = Carbon::now()->format("Y-m-d-H-i") . "-observation-" . Str::random(12) . "." . $request->file("image")->getClientOriginalExtension();
            Storage::disk("public")->putFileAs("observation", $request->file("image"), $image);

            $observation = ObservationModel::create([
                "user_id" => auth()->id(),
                "name" => $request->name,
                "date" => $request->date,
                "latitude" => $request->latitude,
                "longitude" => $request->longitude,
                "location" => $request->location,
                "description" => $request->description,
                "image" => $image
            ]);

            ObservationHistoryModel::create([
                "observation_id" => $observation->id,
                "status" => ObservationStatusConstant::PENDING
            ]);

            return ResponseHelper::response($observation);
        });
    }
}
