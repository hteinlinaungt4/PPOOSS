<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    function list(){
        $product = Product::select('products.*', 'subcategories.name as subcategory_name')
            ->when(request('search'), function($p){
                $key = request('search');
                $p->orWhere('products.name', 'like', '%' . $key . '%')
                  ->orWhere('products.price', 'like', '%' . $key . '%');
            })
            ->leftJoin('subcategories', 'products.subcategory_id', 'subcategories.id')
            ->orderBy('products.created_at', 'desc')->paginate(2);
        return view('admin.pizza.list', compact('product'));
    }

    // Create page
    function createpage(){
        $subcats = Subcategory::select('name','id')->get();
        return view('admin.pizza.create', compact('subcats'));
    }

    // Create
    function create(Request $request){
        $this->validation($request, 'create');
        $pizza = $this->getData($request);
        if($request->hasFile('image')){
            $filename = uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/', $filename);
            $pizza['image'] = $filename;
        }
        Product::create($pizza);
        return redirect()->route('product#list');
    }

    // Detail
    function detail($id){
        $product = Product::where('products.id', $id)
            ->select('products.*', 'subcategories.name as subcategory_name')
            ->leftJoin('subcategories', 'products.subcategory_id', 'subcategories.id')
            ->first();
        return view('admin.pizza.detail', compact('product'));
    }

    // Edit
    function edit($id){
        $subcategory = Subcategory::all();
        $product = Product::find($id);
        return view('admin.pizza.edit', compact('product', 'subcategory'));
    }

    // Update
    function update(Request $request){
        $this->validation($request, 'update');
        $updatedata = $this->getData($request);
        $id = $request->id;
        if($request->file('image')){
            $oldimage = Product::select('image')->where('id', $id)->first()->toArray();
            $oldimage = $oldimage['image'];
            Storage::delete('public/' . $oldimage);
            $fileName = uniqid() . '__' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/', $fileName);
            $updatedata['image'] = $fileName;
        }
        Product::where('id', $id)->update($updatedata);
        return redirect()->route('product#list');
    }

    // Delete
    function delete($id){
        $oldimage = Product::select('image')->where('id', $id)->first()->toArray();
        $oldimage = $oldimage['image'];
        Storage::delete('public/' . $oldimage);
        Product::find($id)->delete();
        return redirect()->route('product#list')->with(['deleteMsg' => "You are deleted successfully!"]);
    }

    // Data
    private function getData($request){
        return [
            'subcategory_id' => $request->subcategory,
            'name' => $request->pizzaName,
            'description' => $request->description,
            'price' => $request->price,
        ];
    }

    // Validation
    private function validation($request, $actions){
        $validation = [
            'pizzaName' => 'required|unique:products,name,' . $request->id,
            'subcategory' => 'required',
            'description' => 'required',
            'price' => 'required',
        ];
        $validation['image'] = $actions == 'create' ? 'required|mimes:jpg,jpeg,png|file' : 'mimes:jpg,jpeg,png|file';
        Validator::make($request->all(), $validation)->validate();
    }
}
