<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Classroom extends Model
{
    use HasFactory;

    public static string $disk = 'public';

    protected $fillable = [
        'name', 'section', 'subject', 'code', 'room',
        'cover_image_path', 'theme', 'user_id', 'status'
    ];

    // this method found bootable in model class and this way as in route ('/classroom:code'); but possible make it in Classroom Model.
    public function getRouteKeyName()
    {
        return 'id';
    }

    public static function updateCoverImage($file)
    {
        $path = $file->store('cover_images', [
            'disk' => static::$disk
        ]);
        return $path;
    }

    public static function deleteCoverImage($path): bool
    {
        return Storage::disk(static::$disk)->delete($path);
    }
}
