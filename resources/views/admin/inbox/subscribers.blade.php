@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')
@section('crumb', 'Inbox')

@section('actions')
  <a class="btn btn--ghost" href="{{ route('admin.inbox.index') }}">Back to briefs</a>
@endsection

@section('content')
<div class="card">
  {{-- Server-side Yajra DataTable (read-only listing, no row actions). --}}
  <div class="table-wrap table-wrap--dt">
    {!! $datatable->table(['class' => 'table dt-table', 'style' => 'width:100%']) !!}
  </div>
</div>
@endsection

@push('datatables')
  {!! $datatable->scripts() !!}
@endpush
