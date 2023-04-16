<?php

namespace App\Http\Controllers\User;

use App\Constants\ObservationStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralObservationController;
use App\Models\NotificationModel;
use App\Models\ObservationHistoryModel;
use App\Models\ObservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationController extends Controller {
    public function get(Request $request) {
        $notifications = NotificationModel::where("user_id", auth()->id())
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($notifications);
    }
}
