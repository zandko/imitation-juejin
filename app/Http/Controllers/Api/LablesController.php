<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\LableTransformer;
use App\Models\Lable;
use Auth;

class LablesController extends Controller
{
    /**
     * 标签
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection(Lable::all(), new LableTransformer());
    }

    /**
     * 关注标签 取消关注标签
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function followLable(Request $request)
    {
        $lable = Auth::guard('api')->user()->userThisLable($request->lable_id);

        if (count($lable['attached']) > 0) {
            $lables = Lable::query()->where('id', $lable['attached'])->first();
            $lables->increment('follow_count', 1);

            return $this->response->created();
        } else {
            $lables = Lable::query()->where('id', $lable['detached'])->first();

            if ($lables->follow_count > 1) {
                $lables->decrement('follow_count', 1);
            }

            return $this->response->noContent();
        }
    }
}
