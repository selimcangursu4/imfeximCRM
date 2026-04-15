<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyApiSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

}
