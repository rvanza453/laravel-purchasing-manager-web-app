<?php

namespace Modules\AgriFlow\Http\Controllers;

use App\Http\Controllers\Controller;

class AgriFlowController extends Controller
{
    public function index()
    {
        return view('agriflow::index');
    }

    public function panen()
    {
        return view('agriflow::panen');
    }

    public function pengiriman()
    {
        return view('agriflow::pengiriman');
    }

    public function monitoringRestan()
    {
        return view('agriflow::monitoring-restan');
    }
}
