<?php

namespace App\Observers;

use App\Models\Image;
use App\Jobs\UploadImage;

class ImageObserver
{
    // creating, created, updating, updated, saving,
    // saved,  deleting, deleted, restoring, restored

//    /**
//     * @param Image $image
//     */
//    public function saved(Image $image)
//    {
//        dispatch(new UploadImage($image));
//    }
//
//    /**
//     * @param Image $image
//     */
//    public function updated(Image $image)
//    {
//        dispatch(new UploadImage($image));
//    }
}
