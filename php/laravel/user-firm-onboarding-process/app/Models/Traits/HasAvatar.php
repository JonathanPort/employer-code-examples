<?php

namespace App\Models\Traits;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use App\Services\FileManagementService;
use App\Models\Misc\FileUpload;

trait HasAvatar
{

    public function generateDefaultAvatar($model)
    {

        $name = isset($model->attributes['full_name']) ? $model->attributes['full_name'] : false;

        if (! $name) {

            $firstName = isset($model->attributes['first_name']) ? $model->attributes['first_name'] : false;
            $lastName = isset($model->attributes['last_name']) ? $model->attributes['last_name'] : false;

            if ($firstName && $lastName) $name = $firstName . ' ' . $lastName;

        }

        if (! $name) $name = isset($model->attributes['name']) ? $model->attributes['name'] : false;

        $img = (new InitialAvatar())
            ->name($name)
            ->length(2)
            ->size(256)
            ->fontName('sans-serif')
            ->fontSize(0.5)
            ->background('30317A')
            ->color('FFFFFF')
            ->rounded()
            ->smooth()
            ->gd()
            ->generate();

        $img = $img->encode('png');

        $fileManagementService = new FileManagementService();

        if ($model->attributes['avatar']) {

            $file = $fileManagementService->getFile($model->attributes['avatar']);

            if ($file) $file->delete();

        }

        $uploadKey = class_basename($model) . '_avatar';

        $upload = $fileManagementService->uploadBase64(
            $uploadKey,
            $img->__toString(),
            'png',
            $uploadKey
        );

        $model->update(['avatar' => $upload->id]);

        return $upload;

    }


    public function getAvatarAttribute()
    {

        $avatar = isset($this->attributes['avatar']) ? $this->attributes['avatar'] : false;

        if (! $avatar) {

            $avatar = $this->generateDefaultAvatar($this);

        } else {

            $avatar = FileUpload::find($avatar);

        }

        if (! $avatar) $avatar = $this->generateDefaultAvatar($this);

        $avatar->url = route('file-management.view-file', [
            'fileId' => $avatar->id,
        ]);

        return $avatar;

    }

}
