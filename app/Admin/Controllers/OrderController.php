<?php
namespace App\Admin\Controllers;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'orders';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User ID'));
        $grid->column('order_amount', __('Order Amount'));
        $grid->column('payment_status', __('Payment Status'));
        $grid->column('payment_method', __('Payment Method'));
        $grid->column('order_status', __('Order Status'));
        $grid->column('pending', __('Pending Time'));
        $grid->column('order_note', __('Order Note'));
        $grid->column('confirmed', __('Confirmed'));
        $grid->column('accepted', __('Accepted'));
        $grid->column('processing', __('Processing'));
        $grid->column('handover', __('HandOver'));
        $grid->column('delivered', __('Delivered'));
        $grid->column('picked_up', __('Picked Up'));
        $grid->column('canceled', __('Canceled'));
        $grid->column('failed', __('Failed'));
        $grid->column('order_note', __('Order Note'));
        $grid->column('scheduled_at', __('Scheduled At'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('delivery_charge', __('Delivery Charge'));
        $grid->column('otp', __('OTP'));
        return $grid;
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('user_id', __('User ID'));
        $show->field('order_amount', __('Order Amount'));
        $show->field('payment_status', __('Payment Status'));
        $show->field('payment_method', __('Payment Method'));
        $show->field('order_status', __('Order Status'));
        $show->field('pending', __('Pending Time'));
        $show->field('order_note', __('Order Note'));
        $show->field('confirmed', __('Confirmed'));
        $show->field('accepted', __('Accepted'));
        $show->field('processing', __('Processing'));
        $show->field('handover', __('HandOver'));
        $show->field('delivered', __('Delivered'));
        $show->field('picked_up', __('Picked Up'));
        $show->field('canceled', __('Canceled'));
        $show->field('failed', __('Failed'));
        $show->field('order_note', __('Order Note'));
        $show->field('scheduled_at', __('Scheduled At'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('delivery_charge', __('Delivery Charge'));
        $show->field('otp', __('OTP'));
        return $show;
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());
         $form->integer('id', __('Id'));
        $form->integer('user_id', __('User ID'));
        $form->float('order_amount', __('Order Amount'));
        $form->text('payment_status', __('Payment Status'));
        $form->text('payment_method', __('Payment Method'));
        $form->text('order_status', __('Order Status'));
        $form->datetime('pending', __('Pending Time'))->default(date('Y-m-d H:i:s'));
        $form->text('order_note', __('Order Note'));
        $form->text('confirmed', __('Confirmed'));
        $form->text('accepted', __('Accepted'));
        $form->text('processing', __('Processing'));
        $form->text('handover', __('HandOver'));
        $form->text('delivered', __('Delivered'));
        $form->text('picked_up', __('Picked Up'));
        $form->text('canceled', __('Canceled'));
        $form->text('failed', __('Failed'));
        $form->text('order_note', __('Order Note'));
        $form->datetime('scheduled_at', __('Scheduled At'))->default(date('Y-m-d H:i:s'));
        $form->datetime('created_at', __('Created at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('updated_at', __('Updated at'))->default(date('Y-m-d H:i:s'));
        $form->float('delivery_charge', __('Delivery Charge'));
        $form->integer('otp', __('OTP'));
        // $form->text('name', __('Name'));
        // $form->email('email', __('Email'));
        // $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        // $form->password('password', __('Password'));
        // $form->text('remember_token', __('Remember token'));
        return $form;
    }
}