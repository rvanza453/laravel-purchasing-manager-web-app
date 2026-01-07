<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'unit', 'min_stock', 'category'];

    public function stocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }
}
