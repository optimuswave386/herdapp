@extends('layouts.admin')

@section('title', 'Add product')
@section('heading', 'Add product')
@section('subheading', 'New products appear in the shop as soon as they are active.')

@section('admin')
    <div class="card max-w-3xl p-6">
        @include('products._form', ['action' => route('products.store'), 'method' => 'POST'])
    </div>
@endsection
