@extends('layouts.admin')

@section('title', 'Team & Founders')
@section('crumb', 'People pages')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.members.create') }}">+ New person</a>
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
