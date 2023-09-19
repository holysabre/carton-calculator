<?php

namespace App\Admin\Repositories;

use App\Models\Spec as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Spec extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
