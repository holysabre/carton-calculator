<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Product;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductsController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('price_per_square');
            $grid->column('weight_per_square');
            $grid->column('remark');
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
        return Show::make($id, new Product(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('price_per_square');
            $show->field('weight_per_square');
            $show->field('remark');
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
        return Form::make(Product::with([]), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('price_per_square');
            $form->text('weight_per_square');
            $form->text('thickness');
            $form->text('per_bundle_qty');
            $form->text('jettison_ratio');
            $form->text('remark');

            // $form->hasMany('tiered_prices', function (Form\NestedForm $form) {
            //     $form->text('gt', '大于');
            //     $form->text('elt', '小于等于');
            //     $form->text('price', '每平方单价');
            // });

            $skuParams = [
                // 扩展第一列
                [
                    'name'    => '阶梯价 0-500',
                    'field'   => 'tieredPrice_0_500',
                    'default' => 0,
                ],
                [
                    'name'    => '阶梯价 500-1000',
                    'field'   => 'tieredPrice_500_1000',
                    'default' => 0,
                ],
                [
                    'name'    => '阶梯价 1000-5000',
                    'field'   => 'tieredPrice_1000_5000',
                    'default' => 0,
                ],
                [
                    'name'    => '阶梯价 5000-999999',
                    'field'   => 'tieredPrice_5000_999999',
                    'default' => 0,
                ],
            ];

            $skuData = $form->model()->prices;
            $skuString = json_encode($skuData);
            $form->sku('sku', 'Sku')->addColumn($skuParams)->value($skuString);

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
