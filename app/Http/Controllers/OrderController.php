<?php

namespace App\Http\Controllers;

use DB,Session,Excel,PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\MasterTrait;
use Yajra\Datatables\Datatables;
use App\Order;
use App\Notification;
use App\PushTokens;
use App\push_test;
use App\Models\Address;
use App\Cart;
use App\Exports\OrdersExport;

class OrderController extends Controller
{
    use MasterTrait;
	public function __construct()
    {
        $this->middleware('auth');
       // dd(App::getLocale());
    }
    public function index(Datatables $datatables)
    {   
        if ($datatables->getRequest()->ajax()) {
            $orders = Order::select('orders.*','users.name','users.email','users.phone_no')
            ->leftjoin('users','users.id','=','orders.user_id')
            ->orderBy('orders.id', 'DESC')->get();
            //dd($orders);
			return Datatables::of($orders)->make(true);
		}
        return view('orders.order');
    }
    public function viewOrder(Request $request)
    {

          $orders = Order::select('orders.*','users.name','users.email','users.phone_no')
            ->leftjoin('users','users.id','=','orders.user_id')
            ->where('orders.id',$request->id)
            ->orderBy('orders.id', 'DESC')->get()->first();
            if(@$orders->address_id){
                $address = Address::select('*')->where('id',$orders->address_id)->first();
               // dd($address);
            }
            $getCart = Cart::select('carts.*','books.*',DB::raw("IFNULL(avg(reviews.rating),0)AS rates"),DB::raw("count(reviews.book_id) AS rate_count"))
                ->leftjoin('books','books.id','=','carts.book_id')
                ->leftjoin('reviews','books.id','=','reviews.book_id')
                ->where('carts.is_confirm','1')->where('carts.order_id',$request->id)->groupBy('books.id')->get()->toArray();
                
         //dd($getCart);
        return view('orders.add')->with([
            'order' => $orders,
            'address' => $address,
            'books' => $getCart
		]);
    }
    public function statusSetAsDelivered(Request $request){
        if(@$request->order_id){
            $update_record = $this->updateTable('App\Order', ['id'=>$request->order_id],['status'=>'delivered']);
            $userId = Order::where('id',$request->order_id)->first();
            if(@$userId){
                 $notification = [];
                $notification['user_id'] = $userId->user_id;
                $notification['notification_type'] = 3;
                $notification['order_id'] = $userId->id;
                $notification['notification'] = NOTIFICATION_THIRD;
                $notification['notification_text_en'] = 'Order id #'.$userId['id'].' status changed to delivered.';
                $notification['notification_text_ar'] = 'Order id #'.$userId['id'].' status changed to delivered.';
                Notification::create($notification)->id;
                $register_token = PushTokens::select('*')->where('user_id',$userId['user_id'])->get()->toArray();
                if (!empty($register_token)) {
                    $android_device_arr = array();
                    $ios_device_arr = array();
                    foreach ($register_token as $token) {
                        if($token['device_type'] == 0)
                        {
                            array_push($android_device_arr, $token['push_token']);
                        }
                        else
                        {
                            array_push($ios_device_arr, $token['push_token']);
                        }
                    }
                    $message_array['custom_data']['message'] = 'Order id #'.$userId['id'].' status changed to delivered.';
                    $message_array['custom_data']['notification_type'] = 3;
                    $message_array['custom_data']['order_id'] = $userId['id'];
                    $message_array['custom_data']['notification'] = NOTIFICATION_THIRD;

                    if(!empty($android_device_arr)){
                        $push_data['device_type'] = 0;
                        $push_data['register_id'] = $android_device_arr;
                        $push_data['multiple'] = 1;
                        
                        $data1 = $this->send_fcm_push($push_data, $message_array);
                        $push_test = [];
                        $push_test['json_data'] = json_encode($data1);
                        $push_test['device_type'] = 0;
                        push_test::create($push_test)->id;
                        // $this->db->insert('tbl_test',Array('json_data' => json_encode($data1),'device_type' => '0'));
                    }
                    if(!empty($ios_device_arr)){
                        $push_data['device_type'] = 1;
                        $push_data['register_id'] = $ios_device_arr;
                        $push_data['multiple'] = 1;
                        
                        $data1 = $this->send_fcm_push($push_data, $message_array);
                        $push_test = [];
                        $push_test['json_data'] = json_encode($data1);
                        $push_test['device_type'] = 0;
                        push_test::create($push_test)->id;
                        // $this->db->insert('tbl_test',Array('json_data' => json_encode($data1),'device_type' => '1'));
                    }
                    
                }
            }
        }
    }
    public function statusSetAsCancel(Request $request){
        if(@$request->order_id){
            $update_record = $this->updateTable('App\Order', ['id'=>$request->order_id],['status'=>'cancel']);
            $userId = Order::where('id',$request->order_id)->first();
            if(@$userId){
                 $notification = [];
                $notification['user_id'] = $userId->user_id;
                $notification['notification_type'] = 3;
                $notification['order_id'] = $userId->id;
                $notification['notification'] = NOTIFICATION_THIRD;
                $notification['notification_text_en'] = 'Order id #'.$userId['id'].' status changed to cancel.';
                $notification['notification_text_ar'] = 'Order id #'.$userId['id'].' status changed to cancel.';
                Notification::create($notification)->id;
                $register_token = PushTokens::select('*')->where('user_id',$userId['user_id'])->get()->toArray();
                if (!empty($register_token)) {
                    $android_device_arr = array();
                    $ios_device_arr = array();
                    foreach ($register_token as $token) {
                        if($token['device_type'] == 0)
                        {
                            array_push($android_device_arr, $token['push_token']);
                        }
                        else
                        {
                            array_push($ios_device_arr, $token['push_token']);
                        }
                    }
                    $message_array['custom_data']['message'] = 'Order id #'.$userId['id'].' status changed to cancel.';
                    $message_array['custom_data']['notification_type'] = 3;
                    $message_array['custom_data']['order_id'] = $userId['id'];
                    $message_array['custom_data']['notification'] = NOTIFICATION_THIRD;

                    if(!empty($android_device_arr)){
                        $push_data['device_type'] = 0;
                        $push_data['register_id'] = $android_device_arr;
                        $push_data['multiple'] = 1;
                        
                        $data1 = $this->send_fcm_push($push_data, $message_array);
                        $push_test = [];
                        $push_test['json_data'] = json_encode($data1);
                        $push_test['device_type'] = 0;
                        push_test::create($push_test)->id;
                        // $this->db->insert('tbl_test',Array('json_data' => json_encode($data1),'device_type' => '0'));
                    }
                    if(!empty($ios_device_arr)){
                        $push_data['device_type'] = 1;
                        $push_data['register_id'] = $ios_device_arr;
                        $push_data['multiple'] = 1;
                        
                        $data1 = $this->send_fcm_push($push_data, $message_array);
                        $push_test = [];
                        $push_test['json_data'] = json_encode($data1);
                        $push_test['device_type'] = 0;
                        push_test::create($push_test)->id;
                        // $this->db->insert('tbl_test',Array('json_data' => json_encode($data1),'device_type' => '1'));
                    }
                    
                }
            }
        }
    }
    function send_fcm_push($data,$message_data)
	{
        //dd($message_data);
		$device_type = @$data['device_type'];
		$register_id = $data['register_id'];
		/*$token       = @$data['device_token'];
		$badge       = 0;*/
        //	print_r($message_data);die;
		if($register_id != ""){

			/*$fields['notification'] = array
			(
			'body'=> $message_data['message'],
			'title'=> $message_data['title'],
			'icon' => 'myicon',
			'sound'=> 'mySound'
			);*/
			if($device_type != 0)
			{
				$fields['notification'] = array
				(
					'title' => PROJECT_NAME,		
					'body'=> $message_data['custom_data']['message'],
					'sound' => 'mySound',
					//'badge' => $message_data['custom_data']['count']
				);
			}
			
			$message_data['custom_data']['message'] = $message_data['custom_data']['message'];
				//print_r($message_data['custom_data']['message']);die;
			if(!empty($message_data['custom_data'])){

				$fields['data'] = $message_data['custom_data'];
			}
			//$fields['data'] = $message_data;

			if($data['multiple'] == 1){
				$fields['registration_ids'] = $register_id;
			}
			else
			{
				$fields['to'] = $register_id;
			}
			//$fields['count'] = $message_data['custom_data']['count']; 
			//$fields['priority'] = $priority;
    	//	print_r($fields);die;
			$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY_FOR_FIREBASE_PUSH,
				'Content-Type: application/json'
			);
			//print_r($fields);die;
			#Send Reponse To FireBase Server
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			return $result;

		}
    }
    public function orderExport(Request $request)
    {
        $orders = Order::select('orders.*','users.name','users.email','users.phone_no')
            ->leftjoin('users','users.id','=','orders.user_id')
            ->orderBy('orders.id', 'DESC')->get();
        $response = [];
        if($request->format == 1){
            $fileName = "Order report " .strtotime(now()). ".pdf";
            $pdf = PDF::loadView('orderexport', compact('orders'));
            $pdf->setPaper('A4', 'landscape');  
            return $pdf->download($fileName);
        }else if($request->format == 2){
            
            return Excel::download(new OrdersExport,'Order.xlsx');
            $cnt = 1;
           /*  foreach ($orders as $key => $value) {
                $response[$key]['No'] = $cnt;
                $response[$key]['buyer name'] = $value['name'];
               
                $response[$key]['Phone no'] = $value['phone_no'];
                $response[$key]['Email'] = $value['email'];

                $response[$key]['Payment amount'] = $value['payment_amt'];
                $response[$key]['Payment type'] = $value['p_type']; 
                $response[$key]['Order status'] = $value['status'];
                
                $cnt ++;
            } 
            $fileName = "Order report " . strtotime(now());
            Excel::store($fileName, function($excel) use($response) {
                $excel->sheet('sheet', function($sheet) use($response) {
                    $sheet->freezeFirstRow();
                    $sheet->getStyle('A1:G1')->applyFromArray(['font' => ['bold' => true]]);
                    $sheet->fromArray($response);
                });
            })->export('xlsx'); */
        }
    }

}
