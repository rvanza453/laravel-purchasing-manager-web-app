<?php

namespace Modules\SystemSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SystemSupport\Database\Factories\FeatureRequestFactory;

class FeatureRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): FeatureRequestFactory
    // {
    //     // return FeatureRequestFactory::new();
    // }
}
