<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductTieredPrice extends Model
{
    use HasDateTimeFormatter;

    protected $fillable = ['gt', 'elt', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
