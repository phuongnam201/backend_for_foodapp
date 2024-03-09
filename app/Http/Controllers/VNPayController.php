<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VNPayController extends Controller
{
    public static function payWithVnpay(Request $request){
    $order = Order::with(['details'])->where(['id' => session('order_id')])->first();

    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = route('vnpay_return');
    $vnp_TmnCode = "MP8ZPI13";//Mã website tại VNPAY 
    $vnp_HashSecret = "XLETAFXEBDUVXRAPNLQNMWNBMEIHSZUQ"; //Chuỗi bí mật

    $vnp_TxnRef = $order['id'] + rand(1,10000); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
    $vnp_OrderInfo = "test";
    $vnp_OrderType = "test";
    $vnp_Amount = $order['order_amount'] * 1000000;
    $vnp_Locale = "VN";
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
        
       
    );

    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        $inputData['vnp_BankCode'] = $vnp_BankCode;
    }
    if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
        $inputData['vnp_Bill_State'] = $vnp_Bill_State;
    }

    //var_dump($inputData);
    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
        //dd($hashdata);
    }

    $vnp_Url = $vnp_Url . "?" . $query;
    if (isset($vnp_HashSecret)) {
        $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);  
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }
   // echo($vnp_Url);
   // dd($vnpSecureHash);

    $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'data' => $vnp_Url);
        //dd($vnp_Url);
    return redirect()->away($returnData['data']);
    
    }

    public function handleVnpayReturn(Request $request) {
        $vnp_HashSecret = "XLETAFXEBDUVXRAPNLQNMWNBMEIHSZUQ";
        $order = Order::with(['details'])->where(['id' => session('order_id')])->first();
    
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    
       // echo($secureHash);
        $vnp_TransactionNo = $_GET['vnp_TransactionNo'];
        //$vnp_TxnRef = $_GET['vnp_TxnRef'];
        if ($secureHash == $vnp_SecureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                DB::table('orders')
                ->where('id', $order['id'])
                ->update([
                    'transaction_reference' => $vnp_TransactionNo,
                    'payment_status' => 'Paid',
                    'payment_method' => 'vnpay',
                    'order_status' => 'success',
                    //'failed' => now(),
                    'updated_at' => now()
                ]);
                return redirect()->route('payment-status', ['status' => 'success']);
                
               // return view('payment-result')->with('message', 'Payment successfully!');
            } else {
                return redirect()->route('payment-status', ['status' => 'cancel']);
            }
        } else {
            return redirect()->route('payment-status', ['status' => 'fail']);
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
