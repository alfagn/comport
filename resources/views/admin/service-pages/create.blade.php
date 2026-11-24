@extends('layouts.admin')

@section('title', 'Add Service Page')
@section('crumb', 'Service Pages')

@section('content')
  @include('admin.service-pages._form', ['item' => $item])
@endsection
