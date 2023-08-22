<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\User;
use App\Models\UserTrainer;
use App\Models\Intrest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashBoard()
    {

        $mostPopularTrainerId = UserTrainer::select('trainer_id')
            ->groupBy('trainer_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->get();


        $userDetails = User::whereIn('id', $mostPopularTrainerId)->with('role')
            ->get();

        $intrests = Intrest::take(4)->get();
        
        return response()->json([
            "success" => true,
            "data" => array(
                "trainers" => $userDetails,
                "intrests" => $intrests
            )
        ]);

    }

    
}
