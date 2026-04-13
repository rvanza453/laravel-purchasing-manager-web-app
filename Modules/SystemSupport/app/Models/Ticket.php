<?php

namespace Modules\SystemSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SystemSupport\Database\Factories\TicketFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'module',
        'user_id',
        'priority',
        'status',
        'admin_response'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // protected static function newFactory(): TicketFactory
    // {
    //     // return TicketFactory::new();
    // }
}
