@extends('admin.layout.master')
@section('title','Category Create')
@section('content')
<!-- MAIN CONTENT-->
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 offset-8">
                    <a href="{{ route('category#list')}}"><button class="btn bg-dark text-white my-3">List</button></a>
                </div>
            </div>
            <div class="col-lg-6 offset-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h3 class="text-center title-2">Category Form</h3>
                        </div>
                        <hr>
                        <form action="{{ route('subCategory#create') }}" method="post" novalidate="novalidate">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="cc-payment" class="control-label mb-1">Category</label>
                                <select name="category_id" id="" class="form-control @error('category_id') is-invalid @enderror" >
                                    <option value="">Choose Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="cc-payment" class="control-label mb-1">Name</label>
                                <input id="cc-pament" value="{{ old('subcategoryName') }}" name="subcategoryName" type="text" class="form-control @error('subcategoryName') is-invalid @enderror" aria-required="true" aria-invalid="false" placeholder="SubCategory...">
                                @error('subcategoryName')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div>
                                <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block w-100 my-3">
                                    <span id="payment-button-amount">Create</span>
                                    <span id="payment-button-sending" style="display:none;">Sending…</span>
                                    <i class="fa-solid fa-circle-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MAIN CONTENT-->
@endsection
