<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;
trait ImageUploadTrait
{
    protected string $path  = "assets/file-ticket";

    public function upload_file($file) {
        $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).rand(0,10).'.'.$file->getClientOriginalExtension();
        Storage::disk("public")->putFileAs($this->path, $file, $file_name);
        $file_path = "storage/assets/file-ticket/".$file_name;
        return $file_path;
    }
}
