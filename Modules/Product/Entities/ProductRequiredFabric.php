<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductRequiredFabric extends Model
{
    protected $table = 'product_required_fabrics';

    protected $fillable = ['product_id', 'title', 'qty'];
}
