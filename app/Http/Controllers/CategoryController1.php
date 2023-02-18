<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\category;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->page?:1;
        $products = category::all();
        return response()->json(['status'=>true,'message'=>trans('Category List '),'data'=>$products], 200);
    }
}
