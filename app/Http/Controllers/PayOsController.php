<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use PayOS\PayOS;

class PayOsController extends Controller
{
    private string $payOSClientId;
    private string $payOSApiKey;
    private string $payOSChecksumKey;

    public function __construct()
    {
        $this->payOSClientId = env("PAYOS_CLIENT_ID");
        $this->payOSApiKey = env("PAYOS_API_KEY");
        $this->payOSChecksumKey = env("PAYOS_CHECKSUM_KEY");
    }

    public function createPaymentLink(Request $request) {
        $order = Order::with(['details'])->where(['id' => session('order_id')])->first();

        $YOUR_DOMAIN = env("APP_URL");
        $data = [
            "orderCode" => $order['id'],
            "amount" => $order['order_amount'] * 25000,
            "description" => "payment",
            "returnUrl" => $YOUR_DOMAIN . "payos_return",
            "cancelUrl" => $YOUR_DOMAIN . "payos_return"
        ];
        error_log($data['orderCode']);
        $PAYOS_CLIENT_ID = env('PAYOS_CLIENT_ID');
        $PAYOS_API_KEY = env('PAYOS_API_KEY');
        $PAYOS_CHECKSUM_KEY = env('PAYOS_CHECKSUM_KEY');

        $payOS = new PayOS($PAYOS_CLIENT_ID, $PAYOS_API_KEY, $PAYOS_CHECKSUM_KEY);
        try {
            $response = $payOS->createPaymentLink($data);
            //dd($response['checkoutUrl']);
            //$response = $payOS->getPaymentLinkInfomation($data['orderCode']);
            //dd($response);
            return redirect($response['checkoutUrl']);
            // return $response;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getPaymentLinkInfoOfOrder(string $id)
    {
        $payOS = new PayOS($this->payOSClientId, $this->payOSApiKey, $this->payOSChecksumKey);
        try {
            $response = $payOS->getPaymentLinkInfomation($id);
            return response()->json([
                "error" => 0,
                "message" => "Success",
                "data" => $response["data"]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getCode(),
                "message" => $th->getMessage(),
                "data" => null
            ]);
        }
    }

    public function cancelPaymentLinkOfOrder(Request $request, string $id)
    {
        $body = json_decode($request->getContent(), true);
        $payOS = new PayOS($this->payOSClientId, $this->payOSApiKey, $this->payOSChecksumKey);
        try {
            $cancelBody = is_array($body) && $body["cancellationReason"] ? $body : null;
            $response = $payOS->cancelPaymentLink($id, $cancelBody);
            return response()->json([
                "error" => 0,
                "message" => "Success",
                "data" => $response["data"]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getCode(),
                "message" => $th->getMessage(),
                "data" => null
            ]);
        }
    }

    public function handlePayosReturn(Request $request)
    {   
        $id_order_vietqr = request()->input('id');
        $orderCode = request()->input('orderCode');
        $status = $request->input('status');      
    
        if ($status == 'PAID') {
            DB::table('orders')
                ->where('id', $orderCode)
                ->update([
                    'transaction_reference' => $id_order_vietqr,
                    'payment_status' => 'Paid',
                    'payment_method' => 'VietQR',
                    'order_status' => 'success',
                    //'failed' => now(),
                    'updated_at' => now()
                ]);
            return redirect()->route('payment-status', ['status' => 'success']);
        } else if($status == 'CANCELLED') {
            DB::table('orders')
                ->where('id', $orderCode)
                ->update([
                    'transaction_reference' => $id_order_vietqr,
                    'payment_status' => 'Cancelled',
                    'payment_method' => 'VietQR',
                    'order_status' => 'success',
                    //'failed' => now(),
                    'updated_at' => now()
                ]);
            return redirect()->route('payment-status', ['status' => 'cancel']);
        }else{
            return view('payment-result')->with('message', 'Invalid signature!');
        }
    }

    public function showPaymentResult(Request $request) {
        $status = $request->input('status');
    
        if ($status == 'success') {
            return view('payment-result')->with('message', 'Payment successfully!');
        } else if($status == 'cancel') {
            return view('payment-result')->with('message', 'Payment has been cancelled!');
        }else{
            return view('payment-result')->with('message', 'Invalid signature!');
        }
    }
}
