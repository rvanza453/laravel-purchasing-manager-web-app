<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSite extends Pivot
{
    protected $table = 'product_site';
}