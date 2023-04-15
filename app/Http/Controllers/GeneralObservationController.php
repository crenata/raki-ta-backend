<?php

namespace App\Http\Controllers;

use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Support\Facades\DB;

class GeneralObservationController extends Controller {
    protected $observationTable, $observationHistoryTable;

    public function __construct() {
        $this->observationTable = (new ObservationModel())->getTable();
        $this->observationHistoryTable = (new ObservationHistoryModel())->getTable();
    }

    public function get(array $conditions = []) {
        $detailIds = DB::table("$this->observationHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.observation_id")
            ->groupBy("detail_mx.observation_id")
            ->toSql();
        $detailData = DB::table("$this->observationHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        return ObservationModel::with("latestHistory")
            ->select("$this->observationTable.*")
            ->leftJoinSub(
                $detailIds,
                "detail_max",
                "$this->observationTable.id",
                "=",
                "detail_max.observation_id"
            )
            ->leftJoinSub(
                $detailData,
                "detail",
                "detail.id",
                "=",
                "detail_max.detail_id"
            )
            ->whereRaw(implode(" and ", $conditions))
            ->orderByDesc("$this->observationTable.id")
            ->paginate();
    }
}
