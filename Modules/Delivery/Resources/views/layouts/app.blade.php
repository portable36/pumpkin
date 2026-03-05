@extends('delivery::layouts.master')

{{-- All the contents will be placed here --}}
@section('parent-content')
    @yield('content')
@endsection

{{-- All the styles will be injected here --}}
@section('parent-css')
    @yield('css')
    @stack('styles')
@endsection

{{-- All the scripts will be injected here --}}
@section('parent-js')
    @yield('js')
    @stack('scripts')
@endsection
