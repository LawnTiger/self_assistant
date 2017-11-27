<?php

namespace App\Http\Controllers\Api;

use App\Models\Moment;
use App\Http\Requests\Api\MomentStoreRequest;
use App\Http\Controllers\Controller;

class MomentController extends Controller
{
    public function index()
    {

    }

    public function store(MomentStoreRequest $request)
    {
        $mom = new Moment;
        $mom->user_id = $request->user()->id;
        $mom->content = $request->contents;
        if ($request->pictures) {
            $mom->pictures = $request->pictures;
        }
        $mom->save();

        return app('jResponse')->success($mom);
    }

    public function destroy()
    {

    }
}
