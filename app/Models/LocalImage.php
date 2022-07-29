<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalImage extends Model
{
    protected $table = 'local_images';
    
    protected $fillable = [
        'local_image_id', 'local_image_file',
    ];

    public static function get_random_string($field_code='local_image_id')
	{
        $random_unique  =  sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

        $localImage = LocalImage::where('local_image_id', '=', $random_unique)->first();
        if ($localImage != null) {
            $this->get_random_string();
        }
        return $random_unique;
    }
}
