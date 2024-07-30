@extends('client.layouts.main')

@section('container')
    @include('partials.navbar')
    @include('partials.hero')
    @include('partials.reason')
    @include('partials.subscribe')
    @include('partials.how')
    @include('partials.join')
    @include('partials.commitment')
    @include('partials.testimonial')
    {{-- @include('partials.features') --}}
    @include('partials.newsletter')
    @include('partials.footer')
@endsection
