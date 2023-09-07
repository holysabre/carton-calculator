<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasDateTimeFormatter;

	public function tiered_prices()
	{
		return $this->hasMany(ProductTieredPrice::class);
	}
}
