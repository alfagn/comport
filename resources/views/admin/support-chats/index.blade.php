@extends('layouts.admin')

@section('title', 'Support Conversations')
@section('crumb', 'Support Chats')

@section('actions')
  <a class="btn btn--primary btn--sm" href="{{ route('admin.support-chats.dispatch') }}" wire:navigate>
    <span>+ Send WhatsApp</span>
  </a>
@endsection

@section('content')
<div class="support-kpis">
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Total Conversations</div>
    <div class="support-kpi__val" style="color:#fff;">{{ $stats['total'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">WhatsApp Threads</div>
    <div class="support-kpi__val" style="color:#25D366;">{{ $stats['whatsapp'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Handed Off to Inbox</div>
    <div class="support-kpi__val" style="color:var(--accent);">{{ $stats['handed_off'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Started Today</div>
    <div class="support-kpi__val" style="color:#fff;">{{ $stats['today'] }}</div>
  </div>
</div>

<div class="card">
  <div class="table-wrap table-wrap--dt">
    {!! $datatable->table(['class' => 'table dt-table', 'style' => 'width:100%']) !!}
  </div>
</div>
@endsection

@push('datatables')
  {!! $datatable->scripts() !!}
@endpush
