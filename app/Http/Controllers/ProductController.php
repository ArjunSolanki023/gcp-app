<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Validator, Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Http\Controllers\MasterTrait;
use App\Models\Address;

class ProductController extends Controller
{
    use MasterTrait;
    public function productList(Request $request)
    {
        // dd($request->all());
        /* $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
         if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        } */
        $getProduct = Product::select('id','name','mrp','description','image','category_id');
        $id = $request->input('category_id');      

         if($id > 0){
            $getProduct = $getProduct->where('category_id',$request->input('category_id'));
         }
         if(@$request->search_text){
            $search_text = $request->search_text;
               $getProduct = $getProduct->where(function($query) use ($search_text){
               $query->Where("name",'LIKE','%'.$search_text.'%')
               ->orWhere("description",'LIKE','%'.$search_text.'%');
            });
       }
         $getProduct = $getProduct->OrderBy('id','DESC')->paginate(10);
        return response()->json(['status'=>true,'message'=>trans('Product List '),'data'=>$getProduct], 200);
    }
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }
        $input = $request->all();
        $getProduct = Product::where('id', $request->product_id)->where('is_del',0)->first();
        if(!empty($getProduct)){
            $alredyExist = Cart::where('user_id',Auth::user()->id)->where('product_id',$input['product_id'])->where('is_confirm','0')->first();
            if(!empty($alredyExist)){
                return response()->json(['status'=>false,'message'=>trans('Product already added to cart.')], 200);
            }else{
                $cart = [];
                $cart['product_id'] = $request->product_id;
                $cart['user_id'] = Auth::user()->id;
                $cart_id = $this->lastInsertedData("App\Models\Cart", $cart); 
                if($cart_id){
                    return response()->json(['status'=>true,'message'=>trans('Product added to cart successfully.')], 200);
                }else{
                    return response()->json(['status'=>false,'message'=>trans('Something went wrong, please try agan later.')], 200);
                }
            }
        }else{
            return response()->json(['status'=>false,'message'=>trans('Product not found.')], 200);
        } 
    }
    public function itemRemovefromCart(Request $request){
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required'
            
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }
        $getcartitem = Cart::where('user_id',Auth::user()->id)->where('id',$request->cart_id)->where('is_confirm','0')->where('is_del',0)->first();
        if(!empty($getcartitem)){
            Cart::find($getcartitem['id'])->delete();
            return $this->sendResponse(NULL,'Item removed from cart.');
        }else{
            return $this->sendBadRequest('Item not found in your cart.');
        }
    }
    public function cartList(Request $request)
    {
        // $cart = [];
        $cart['cart'] = Cart::select('carts.id as cart_id','carts.product_id','products.*','products.name as product_name','products.mrp as product_price')
        ->leftjoin('products','products.id','=','carts.product_id')
        ->where('carts.user_id',Auth::user()->id)->where('carts.is_confirm','0')->where('carts.is_del','0')->get();
        //dd(count($cart['cart']));
        if(count($cart['cart']) > 0){
            $sum = 0;
            foreach ($cart['cart'] as $values) {
                $sum += $values['product_price'];
            }
            $cart['total_price'] = (string) $sum;
           // $cart['price'] = (string) $sum;
           // return response()->json(['status'=>true,'message'=>trans('Product added to cart successfully.')], 200);
            return $this->sendResponse($cart, 'Cartlist');
        }else{
            return $this->sendBadRequest('Cart empty');
        }
    }
    public function addOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
            
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }
        $getcartitem = Cart::where('user_id',Auth::user()->id)->where('is_confirm',0)->first();
        if(!empty($getcartitem)) {
            $getItem = Cart::select('carts.*','carts.id as cart_id','products.*')
                ->leftjoin('products','products.id','=','carts.product_id')
                ->where('carts.user_id',Auth::user()->id)->where('carts.is_confirm','0')->where('carts.is_del','0')->get()->toArray();
            
            /* $getPrice = Cart::select('carts.*','products.*')
                ->leftjoin('products','products.id','=','carts.product_id')
                ->where('carts.user_id',Auth::user()->id)->where('carts.is_confirm','0')->get()->toArray(); */

            //dd($getPrice);
            if(!empty($getItem)) {
                $total_price = 0;
                if(!empty($getItem)){
                    foreach ($getItem as $price) {
                        $total_price += $price['mrp']; 
                    }
                    // dd($total_price);
                }
                $order['user_id'] = Auth::user()->id;
                $order['payment_amt'] = isset($total_price) ? $total_price : '';
                $order['payment_type'] = 0;
                $order['payment_status'] = '0';
                $order['order_date'] = date('Y-m-d');
                $order['address_id'] = 0;
                $order['status'] = 'pending';
                $order['commission'] = 0;
                
                $order_id = $this->lastInsertedData("App\Models\Order", $order);
                if($order_id){
                   // $address = [];
                    if(@$request->address){
                       /*  $address['user_id'] = Auth::user()->id;
                        $address['address'] = $request->address ? $request->address : null;
                        $address['landmark'] = $request->landmark ? $request->landmark : null;;
                        $address['city'] = $request->city ? $request->city : null;;
                        $address['state'] = $request->state ? $request->state : null;;
                        
                        $address_id = $this->lastInsertedData("App\Models\Address", $address); */
                        if($request->address_id){
                            $this->updateTable('App\Models\Order', ['id'=> $order_id] , ['address_id'=> $request->address_id]);    
                         }
                    }
                        // 'email'=>Auth::user()->email
                       // unset($address['user_id']);
                       // $data = array('order' => $getItem,'total' => $total_price ,'address' => $address ,'user' => auth()->user());
                                                 
                       /* Mail::send('emails.order', $data, function ($message) use ($data) {
                            $message->subject('Order placed.');
                            $message->from(env("MAIL_USERNAME"), env("APP_NAME"));
                            $message->to(Auth::user()->email); 
                        }); */
                    foreach ($getItem as $value) {
                        $this->updateTable('App\Models\Cart', ['id'=>$value['cart_id']] , ['order_id'=>$order_id,'is_confirm'=>'1']);
                    }
                    return $this->sendResponse(NULL, 'Order placed.');
                }else{
                    return $this->sendBadRequest("Something went wrong, please try again later.");
                }

            }else{
                return $this->sendBadRequest("Item not found in your cart.");    
            }
        }else{
            return $this->sendBadRequest("Item not found in your cart.");
        }
    }
    public function addAddress(Request $request)
    {
        $user = Auth::User();
        
         $validator = Validator::make($request->all(), [
                'address' => 'required',
                'landmark' => 'required',
                'city' => 'required',
                'state' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['status'=>false, 'message' => $validator->errors()->first()], 203);
        }
        $address = [
            'address' => $request->get('address'),
            'landmark' => $request->get('landmark'),
            'city' => $request->get('city'),
            'state' => $request->get('state'),
        ];
        $ifExistAddress = Address::where('user_id', $user->id)->first();
        if(!empty($ifExistAddress)){
            $address_id = Address::where('id', $ifExistAddress['id'])
                    ->update($address);
        }else{
            $address['user_id'] = Auth::user()->id;
            $address_id = $this->lastInsertedData("App\Models\Address", $address);
        }
        if ($address_id)
        {
            $data = User::with('address')->where('id', $user->id)->get();
            return response()->json(['status'=>true, 'data' => $data,'message'=>trans('Address saved.')], 200);
        }
        else
        {
            return response()->json(['status'=>false,"message" => trans('profile not found')], 203);
        }
    }
}
