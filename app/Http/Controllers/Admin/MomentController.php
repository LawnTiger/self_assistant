<?php

namespace App\Http\Controllers\Admin;

use App\Models\Moment;
use App\Http\Controllers\Controller;

class MomentController extends Controller
{
    public function index()
    {
        $moments = Moment::with('user')->paginate(15);
        return view('admin.moment', compact('moments'));
    }
}
