<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Response;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Product::select('*'))
                ->addColumn('action', 'product.product-action')
                ->addColumn('image', 'product.image')
                ->rawColumns(['action', 'image'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('product.home');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $productId ='';
        if($request->has('product_id')){
            $productId = $request->product_id;
        }
        

        $image = $request->hidden_image;

        if ($files = $request->file('image')) {

            //delete old file
            \File::delete('public/product/' . $request->hidden_image);

            //insert new file
            $destinationPath = 'public/product/'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $image = "$profileImage";
        }

        $product = Product::find($productId) ?? new Product();
        // Set the individual attributes
        $product->id = $productId;
        $product->title = $request->title;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->image = $image;

        // Save the product
        $product->save();

        return response()->json(['success' => true, 'message' => 'Successfully']);
    //    if($request->has('product_id')){
    //         return response()->json(['success' => true, 'message' => 'Product updated successfully']);
    //    }else{
    //         return response()->json(['success' => true, 'message' => 'Product created successfully']);
    //    }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $where = array('id' => $id);
        $product  = Product::where($where)->first();

        return response()->json($product);
        // return response()->json(['success' => true, 'message' => 'Product updated successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Product::where('id', $id)->first(['image']);
        \File::delete('public/product/' . $data->image);
        $product = Product::where('id', $id)->delete();

        return Response::json($product);
    }
}
