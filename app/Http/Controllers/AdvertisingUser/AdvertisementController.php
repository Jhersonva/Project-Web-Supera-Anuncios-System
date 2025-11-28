<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdCategory;
use App\Models\Advertisement;
use App\Models\ValueFieldAd;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $ads = $user->advertisements()
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('advertising_user.my_ads.index', compact('ads'));
    }
}
