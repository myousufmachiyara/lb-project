<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class SaleVoucherController extends Controller
{
    public function index()
    {
        return view('sale-voucher.index');
    }

    public function create()
    {
        $services = Service::all(); // Or Service::pluck('name', 'id');
        return view('sale-voucher.create', compact('services'));    
    }
}
