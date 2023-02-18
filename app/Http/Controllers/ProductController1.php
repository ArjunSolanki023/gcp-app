<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }

        /* $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        } */
        
        
        $id = $request->input('category_id');
        $getProduct = Product::all();

       /*  if($id!='0'){
            $getProduct = $getProduct->where('category_id',$id);
        } */
        $page = $request->page?:1;
       // $products = Product::paginate(5,['*'], 'page', $page);

        //$getProduct = $getProduct->groupBy('id')->paginate(10)->toArray();
        /* if(count($getProduct['data']) > 0)
            return $this->sendResponse($getProduct,__('api_messages.BOOK_DATA'));
        else
            return $this->sendBadRequest(__('api_messages.BOOKS_NOT_FOUND'));  */ 
            
        return response()->json(['status'=>true,'message'=>trans('Product List '),'data'=>$getProduct], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imageName=null;
        //try{
            if ($request->File('image')) {
           // dd($request->File('image'));
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            //dd($imageName);
            $request->image->move(public_path('images'), $imageName);
           }
       // }catch(\Exception $e){
            //return $e->getMessage();
      //  }
        
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'mrp' => 'required|numeric|min:0|not_in:0',
            'selling' => 'required|numeric|min:0|not_in:0',
            'description' => 'required',
        ]);
     
        if ($validator->fails()) {
            return response(['status'=>false, 'message' => $validator->errors()->first()], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'mrp' => $request->mrp,
            'selling' => $request->selling,
            'description'=>$request->description,
            'image'=>$imageName,
        ]);
        return response()->json(['status'=>200,'message'=>trans(' Product created successfully '),'data'=>$product], 200);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
         if ($product) {
           return response()->json(['status'=>true,'message'=>trans('Product retrieved successfully '),'data'=>$product], 200);
          
        }
        else
        {
         return response(['status'=>false, 'message'=>'Product not found '],203);
        }  
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $imageName=null;
        if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mrp' => 'required|numeric|min:0|not_in:0',
                'selling' => 'required|numeric|min:0|not_in:0',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['status'=>false, 'message' => $validator->errors()->first()], 422);
            }  
        $product = Product::find($id);
        if($product)
        {
        $product->name = $request->get('name');
        $product->mrp = $request->get('mrp');
        $product->selling = $request->get('selling');
        $product->description = $request->get('description');
        $product->image = $imageName;
        $product->save();
        return response()->json(['status'=>true,'message'=>trans('Product updated successfully'),'data'=>$product], 200);
        }
       
        else
        {
          return response(['status'=>false, 'message'=>'Product not found '],203);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
         if ($product) {
           return response()->json(['status'=>true,'message'=>trans('Product deleted successfully ')], 200);
        }
        else
        {
         return response(['status'=>false, 'message'=>'Product not found '],203);
        }  
     
    }
}
