<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

    Route::get('/', function () {
        return view('welcome');
    });

    Route::group(['prefix' => 'payment-mobile'], function () {
        Route::get('/', 'PaymentController@payment')->name('payment-mobile');
        Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
    });
    Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
    Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');
    Route::get('payment-success', 'PaymentController@success')->name('payment-success');
    Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

    /**vnpay */
    Route::post('vnpay', 'VNPayController@payWithVnpay')->name('vnpay');
    Route::get('vnpay_return', 'VNPayController@handleVnpayReturn')->name('vnpay_return');
    Route::get('payment-status', 'VNPayController@showPaymentResult')->name('payment-status');
    Route::get('result-payment', 'VNPayController@resultPayment')->name('result-payment');

    /** payos real but costly :v */
    Route::post('payos', 'PayOsController@createPaymentLink')->name('payos');
    Route::get('payos_return', 'PayOsController@handlePayosReturn')->name('payos_return');
