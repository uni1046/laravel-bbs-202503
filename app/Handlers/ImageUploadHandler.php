<?php

namespace App\Handlers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class ImageUploadHandler
{
    /**
     * 允许的上传图片后缀名
     *
     * @var array|string[]
     */
    protected array $allowedExt = ['png', 'jpg', 'gif', 'jpeg'];

    /**
     * 保存上传的图片
     *
     * @param UploadedFile $file
     * @param string $folder 存储的文件夹名称
     * @param string $filePrefix 文件名前缀，通常是模型 ID
     * @param ?int $maxWidth 图片最大宽度，默认为 416px
     * @return array|false 返回图片存储路径或 false
     *
     */

    public function save(UploadedFile $file, string $folder, string $filePrefix = '', int $maxWidth = null): array|false
    {

        $folderName = "uploads/images/$folder/" . date("Ym/d", time());
        $uploadPath = public_path() . '/' . $folderName;
        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');

        // 拼接文件名
        $filename = $filePrefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        // 判断后缀名
        if (!in_array($extension, $this->allowedExt)) {
            return false;
        }

        // 移动到目标存储路径中
        $file->move($uploadPath, $filename);

        //裁切
        if ($maxWidth && $extension !=='gif'){
            $this->reduceSize($uploadPath . '/' . $filename, $maxWidth);
        }

        return [
            'path' => config('app.url') . "/$folderName/$filename"
        ];
    }

    public function reduceSize(string $filePath, int $maxWidth): void
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // open an image file
        $image = $manager->read($filePath);

        // resize image instance
        // $image->resize(height: $maxWidth);

        // scale image instance to a maximum width
        $image->scale($maxWidth);

        // insert a watermark
        // $image->place('images/watermark.png');

        // encode edited image
        // $encoded = $image->toJpg();

        // save encoded image
        $image->save();

    }
}

