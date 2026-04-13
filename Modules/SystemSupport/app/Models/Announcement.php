<?php

namespace Modules\SystemSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SystemSupport\Database\Factories\AnnouncementFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'is_active',
    ];

    // protected static function newFactory(): AnnouncementFactory
    // {
    //     // return AnnouncementFactory::new();
    // }
}
