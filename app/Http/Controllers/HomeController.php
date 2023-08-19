<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function test(Request $request) {
        return $request;
        return "OK";
    }
}