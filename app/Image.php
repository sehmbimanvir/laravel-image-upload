<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Image extends Model
{
    protected $fillable = [
        'title', 'path', 'auth_by', 'size', 'thumbnail'
    ];

    public $appends = ['url', 'uploaded_time', 'size_in_kb', 'thumbnail_url'];

    public function getUrlAttribute()
    {
        return Storage::disk('s3')->url($this->path);
    }

    public function getThumbnailUrlAttribute()
    {
        return Storage::disk('s3')->url($this->thumbnail);
    }

    public function getUploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'auth_by');
    }

    public function getSizeInKbAttribute()
    {
        return round($this->size / 1024, 2);
    }


    public static function boot()
    {
        parent::boot();
        static::creating(function ($image) {
            $image->auth_by = auth()->user()->id;
        });
    }
}
