@extends('layouts.admin')

@section('title', 'Edit: '.$item->title)
@section('crumb', 'Service Pages')

@section('content')
  @include('admin.service-pages._form', ['item' => $item])
@endsection
