<?php

namespace App\Http\Controllers\Api;

use App\Models\Moment;
use App\Http\Requests\Api\MomentStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MomentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type == 'friends') {
            $moments = '';
        } else {
            $moments = Moment::where('user_id', $request->user()->id)->paginate(20);
        }

        return app('jResponse')->success($moments);
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

    public function destroy($id)
    {
        Moment::destroy($id);
        return app('jResponse')->success();
    }
}
