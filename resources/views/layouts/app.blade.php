{{-- <x-app-layout> pages render inside the master layout so there is one shell to maintain.
     $header and $slot are forwarded to master automatically. --}}
@extends('layouts.master')

@section('content')
    {{ $slot }}
@endsection
