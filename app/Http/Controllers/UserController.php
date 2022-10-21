<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommisionCollection;
use App\Http\Resources\UserResource;
use App\Models\Commission;
use App\Models\User;

class UserController extends Controller
{
    public function show() {
        return new UserResource(User::findOrFail(auth()->id));
    }


    public function commision_report() {
        $commision_data = Commission::where('user_id', auth()->id)
        ->where('status', Commission::STATUS_PENDING)
        ->get();

        return new CommisionCollection($commision_data);
    }
}
