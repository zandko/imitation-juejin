<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


use App\Models\Image;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*上传图片消息队列*/
        $imagePath = "public".substr($this->image->path,strripos($this->image->path,"uploads")-1);

        if($imagePath != 'public0')
        {
            $imageName = explode('/', $this->image->path);
            $disk = Storage::disk('qiniu');
            $disk->put($imageName[8], file_get_contents($imagePath));

            @unlink($imagePath);

            $path = Storage::disk('qiniu')->getDriver()->downloadUrl($imageName[8]);
            $pathInfo = explode('?', $path);

            Storage::delete($imagePath);

            $this->image->update([
                'path' => $pathInfo[0] . "?imageView2/1/w/362/interlace/0/q/100",
            ]);
        }

    }
}
