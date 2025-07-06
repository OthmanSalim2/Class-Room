<?php

namespace App\Models;

use App\Models\Scopes\UserClassroomScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    public static string $disk = 'public';

    protected $fillable = [
        'name', 'section', 'subject', 'code', 'room',
        'cover_image_path', 'theme', 'user_id', 'status'
    ];

    protected static function bootes()
    {
        static::addGlobalScope(UserClassroomScope::class);

        static::creating(function (Classroom $classroom) {
            $classroom->code = Str::random(8);
            $classroom->user_id = Auth::id();
        });
    }

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

    public function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    // $classroom->cover_image_url
    public function getImageUrlAttribute()
    {
        if($this->cover_image_path)
        {
            return Storage::disk('public')->url($this->cover_image_path);
        }
        return "https://placehold.co/800x300";
    }

    public function getUrlAttribute()
    {
        return route('classrooms.show', $this->id);
    }

    // this's the old way.
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    // this way a modern way.
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
            set: fn($value) => strtolower($value)
        );
    }

    public function join($user_id, $role = 'student')
    {
        return DB::table('classroom_user')
            ->insert([
                'classroom_id' => $this->id,
                'user_id' => Auth::id(),
                'role' => $role,
                // 'created_at' => now(),
            ]);
    }

    // Events of Model.
    // Creating, Created, Updating, Updated, Deleting, Deleted, Saving, Saved, Restoring, Restored
    // ForceDeleting, ForceDeleted.
    // Retrieved
}
