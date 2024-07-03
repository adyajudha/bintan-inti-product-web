<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Response;
use DataTables;

class ProductController extends Controller
{
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

    public function store(Request $request)
    {
        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $productId = $request->product_id;

        $image = $request->hidden_image;

        if($request->has('image')){
            if ($files = $request->file('image')) {

                //delete old file
                \File::delete('public/product/' . $request->hidden_image);

                //insert new file
                $destinationPath = 'public/product/'; // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $image = "$profileImage";
            }
        }elseif($request->has('hidden_image')){
            $image = $request->hidden_image;
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

        return Response::json($product);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $product  = Product::where($where)->first();

        return Response::json($product);
    }

    public function destroy($id)
    {
        $data = Product::where('id', $id)->first(['image']);
        \File::delete('public/product/' . $data->image);
        $product = Product::where('id', $id)->delete();

        return Response::json($product);
    }
}
