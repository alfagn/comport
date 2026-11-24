@extends('layouts.admin')

@section('title', 'Services')
@section('crumb', 'What Empetix sells')

@section('actions')
  <a class="btn btn--primary" href="{{ route('admin.services.create') }}">+ New service</a>
@endsection

@section('content')
<p><a class="btn btn--ghost" href="{{ route('admin.service-pages.index') }}" wire:navigate>Bilingual service pages and drafts ↗</a></p>
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
