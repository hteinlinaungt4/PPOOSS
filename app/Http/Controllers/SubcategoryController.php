<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{
    function list(){
        $subcategories = Subcategory::select('subcategories.*', 'categories.name as category_name')
            ->leftJoin('categories', 'subcategories.category_id', '=', 'categories.id')
            ->when(request('search'), function ($query) {
                $key = request('search');
                $query->where('subcategories.name', 'like', '%' . $key . '%');
            })
            ->orderBy('subcategories.created_at', 'desc')
            ->paginate(5);

        return view('admin.subcategory.list', compact('subcategories'));
    }

    // cat create page
    function subcategory(){
        $categories = Category::all();
        return view('admin.subcategory.create',compact('categories'));
    }

    // create
    function create(Request $request){
        $this->Validation($request);
        $subcategoryData=$this->getData($request);
        Subcategory::create($subcategoryData);
        return redirect()->route('subCategory#list');

    }
    // Delete
    function delete($id){
        Subcategory::where('id',$id)->delete();
        return back();
    }

     // edit page
    function edit($id){
        $categories = Category::all();
        $subcategory=Subcategory::where('id',$id)->first();
        return view('admin.subcategory.edit',compact('subcategory','categories'));
    }

    // update
    function update(Request $request){
        $id=$request->id;
        $this->Validation($request);
        $updatedata=$this->getData($request);
        Subcategory::where('id',$id)->update($updatedata);
        return redirect()->route('subCategory#list');

    }


    // Data
    private function getData($request) {
        $data=[
            'id'=>$request->id,
            'name' => $request->subcategoryName,
            'category_id' => $request->category_id,
        ];
        return $data;
    }



    // Validate
    private function Validation($request){
        $validation=[
            'category_id' => 'required',
            'subcategoryName' => 'required|unique:subcategories,name,'.$request->id,
        ];
        Validator::make($request->all(),$validation)->validate();
    }


}
