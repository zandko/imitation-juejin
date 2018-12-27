<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ImageRequest;
use App\Transformers\ImageTransformer;
use App\Handlers\ImageUploadHandler;
use App\Models\Image;
use Auth;
use App\Jobs\UploadImage;

class ImagesController extends Controller
{
    /**
     * 上传图片
     * @param ImageRequest $request
     * @param ImageUploadHandler $imageUploadHandler
     * @param Image $image
     * @return \Dingo\Api\Http\Response
     */
    public function store(ImageRequest $request, ImageUploadHandler $imageUploadHandler, Image $image)
    {
        $user = Auth::guard('api')->user();

        $size = $request->type == 'avatar' ? 362 : 1024;
        $result = $imageUploadHandler->save($request->file('image'), str_plural($request->type), $user->id, $size);

        if ($image = $image->query()->where('user_id', $user->id)->first()) {
            $image->update([
                'path' => $result['path'],
            ]);

            dispatch(new UploadImage($image));
        } else {
            $image->type = $request->type;
            $image->user_id = $user->id;
            $image->path = $result['path'];
            $image->save();

            dispatch(new UploadImage($image));
        }

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
