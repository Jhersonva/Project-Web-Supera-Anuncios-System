<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;

class PublicController extends Controller
{
    public function contact($id)
    {
        $ad = Advertisement::with(['user', 'category', 'subcategory', 'images'])->findOrFail($id);
        return view('public.contact', compact('ad'));
    }
}
