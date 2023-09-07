<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ProductTieredPrice;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductTieredPricesController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductTieredPrice(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('product_id');
            $grid->column('gt');
            $grid->column('elt');
            $grid->column('price');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new ProductTieredPrice(), function (Show $show) {
            $show->field('id');
            $show->field('product_id');
            $show->field('gt');
            $show->field('elt');
            $show->field('price');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ProductTieredPrice(), function (Form $form) {
            $form->display('id');
            $form->text('product_id');
            $form->text('gt');
            $form->text('elt');
            $form->text('price');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
