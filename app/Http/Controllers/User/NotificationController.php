<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\NotificationModel;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function get(Request $request) {
        $notifications = NotificationModel::where("user_id", auth()->id())
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($notifications);
    }
}
