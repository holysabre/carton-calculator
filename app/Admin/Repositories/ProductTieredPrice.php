<?php

namespace App\Admin\Repositories;

use App\Models\ProductTieredPrice as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ProductTieredPrice extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
