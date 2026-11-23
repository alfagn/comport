@extends('layouts.admin')

@section('title', 'Navigation')
@section('crumb', 'Header menus & mega panels')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.navigation.create') }}">+ New link</a>
@endsection

@section('content')
<div class="card">
  {{-- Server-side Yajra DataTable: search, sort and pagination run in SQL. --}}
  <div class="table-wrap table-wrap--dt">
    {!! $datatable->table(['class' => 'table dt-table', 'style' => 'width:100%']) !!}
  </div>
</div>
@endsection

@push('datatables')
  {!! $datatable->scripts() !!}
@endpush
