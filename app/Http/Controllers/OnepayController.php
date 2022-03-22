<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Utility\OnepayUtility;
use App\Models\Order;
use App\CentralLogics\Helpers;

use App\CentralLogics\OrderLogic;

class OnepayController {
    /*
     * Onepay Callback
     * @body - json
     * @response -  {'transaction_id': 'E2D51187B9A137DB7E866', 'pl_ref_no': '', 'status': 1, 'status_message': 'SUCCESS'}
     * @link https://merchant-v2.onepay.lk/#/pages/ipg-developer/app Callback URL
     */

    public function callback(Request $request) {
        
        $api_input = file_get_contents('php://input');
        
        // Log::info('callback PHP - json ' . $api_input);
        
        $data = json_decode($api_input, true);
        
        // $cache_key = 'onepay_callback';
        
        // if (!is_null($data)) {
        //     $statusCode = $data["status"];
        //     Cache::put($cache_key, $data, 1000);
        //     if ($statusCode == 1) {
        //     //return true;
        //     $order = Order::find(session('order_id'));
        //     $order->order_status='confirmed';
        //     $order->payment_method='onepay';
        //     $order->transaction_reference=session('transaction_ref');
        //     $order->payment_status='paid';
        //     $order->confirmed=now();
        //     $order->save();
        //     try {
        //         Helpers::send_order_notification($order);
        //     } catch (\Exception $e) {
        //     }
        //     if ($order->callback != null) {
        //         return redirect($order->callback . '&status=success');
        //     }
        //     return \redirect()->route('payment-success');
        //     }
        //     //
        //     DB::table('orders')
        //     ->where('id', session('order_id'))
        //     ->update(['order_status' => 'failed',  'payment_status' => 'unpaid', 'failed'=>now()]);
        //     $order = Order::find(session('order_id'));
        //     if ($order->callback != null) {
        //         return redirect($order->callback . '&status=fail');
        //     }
        //     return \redirect()->route('payment-fail');
        // }
        // Cache::forget($cache_key);
        // return false;
        
        
        
        $order = Order::find(session('order_id'));
        $order->order_status='confirmed';
        $order->payment_method='onepay';
        $order->transaction_reference=session('transaction_ref');
        $order->payment_status='paid';
        $order->confirmed=now();
        $order->save();
        
        try {
            Helpers::send_order_notification($order);
        } catch (\Exception $e) {

        }
        
        
        

        //     if ($order->callback != null) {
        //         return redirect($order->callback . '&status=success');
        //     }

        //     return \redirect()->route('payment-success');
        //     }
        
        // printf($order);
        //   dd(session());
    }

    public function checkout(Request $request) {
        
        return OnepayUtility::create_checkout_form();
    }

    public function notify(Request $request) {
        
         return OnepayUtility::create_notify_form();

        
    }

    public function checkoutRequest(Request $request) {
        $first_name = $request->firstname;
        $last_name = $request->lastname;
        $email = $request->email;
        $phone = $request->tele;
        $studentId = $request->stuid;
        $amount = $request->pay;
        $reference = 'onepay-' . uniqid();
        return OnepayUtility::checkout_request($reference, $amount, $first_name, $last_name, $phone, $email);
    }

}
