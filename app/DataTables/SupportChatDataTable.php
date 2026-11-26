<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Models\SupportChatSession;
use App\Services\CustomerSupportChatService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class SupportChatDataTable extends AdminDataTable
{
    protected function query(): Builder
    {
        return SupportChatSession::query()->select('support_chat_sessions.*');
    }

    protected function routePrefix(): string
    {
        return 'admin.support-chats';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('visitor_name', function (SupportChatSession $row): string {
                $channelBadge = $row->isWhatsapp()
                    ? ' <span class="pill" style="background:rgba(37,211,102,0.16);color:#25D366;border:1px solid rgba(37,211,102,0.35);">WhatsApp</span>'
                    : ' <span class="pill">Web</span>';

                $handoffBadge = $row->status === SupportChatSession::STATUS_HANDED_OFF
                    ? ' <span class="pill pill--accent">Handed off</span>'
                    : '';

                $presence = app(CustomerSupportChatService::class)->presenceFor($row);
                $liveBadge = '';
                if ($presence['customer_typing']) {
                    $liveBadge = ' <span class="pill" style="background:rgba(16,185,129,0.16);color:#10B981;border:1px solid rgba(16,185,129,0.38);">● Typing now…</span>';
                } elseif ($presence['ai_typing']) {
                    $liveBadge = ' <span class="pill" style="background:rgba(255,77,28,0.16);color:#FF4D1C;border:1px solid rgba(255,77,28,0.38);">● AI replying…</span>';
                }

                $sub = $row->visitor_phone
                    ? ($row->visitor_email ? "{$row->visitor_phone} · {$row->visitor_email}" : $row->visitor_phone)
                    : ($row->visitor_email ?: ($row->landing_page ?: 'Public website'));

                return '<div class="cell-title">'.e($row->visitorLabel()).$channelBadge.$handoffBadge.$liveBadge.'</div>'
                    .'<div class="cell-sub">'.e($sub).'</div>';
            })
            ->editColumn('handoff_summary', function (SupportChatSession $row): string {
                return $row->handoff_summary
                    ? Str::limit($row->handoff_summary, 90)
                    : '—';
            })
            ->editColumn('status', function (SupportChatSession $row): string {
                $class = match ($row->status) {
                    SupportChatSession::STATUS_HANDED_OFF => 'pill pill--accent',
                    SupportChatSession::STATUS_CLOSED => 'pill',
                    default => 'pill pill--live',
                };

                $pausedNote = ($row->isWhatsapp() && $row->ai_paused)
                    ? '<div class="cell-sub" style="margin-top:3px;">Human takeover</div>'
                    : '';

                return '<span class="'.$class.'">'.e($row->statusLabel()).'</span>'.$pausedNote;
            })
            ->editColumn('last_message_at', fn (SupportChatSession $row): string => $row->last_message_at?->diffForHumans() ?? $row->created_at?->diffForHumans() ?? '—')
            ->addColumn('actions', function (SupportChatSession $row): string {
                $open = '<a class="btn btn--ghost btn--sm dt-btn" href="'.e(route('admin.support-chats.show', $row->id)).'">Transcript</a>';

                return '<div class="dt-actions">'.$open
                    .'<form method="post" action="'.e(route('admin.support-chats.destroy', $row->id)).'" data-confirm="Delete this support chat and its transcript?" data-confirm-title="Delete Support Chat" class="dt-inline-form">'
                    .csrf_field().method_field('DELETE')
                    .'<button class="btn btn--danger btn--sm dt-btn" type="submit">Delete</button></form></div>';
            })
            ->rawColumns(['visitor_name', 'status', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('visitor_name')->title('Customer / Channel'),
            Column::make('handoff_summary')->title('Handoff Summary')->orderable(false),
            Column::make('message_count')->title('Turns')->searchable(false)->width(85),
            Column::make('status')->title('Status')->width(135),
            Column::make('last_message_at')->title('Last Active')->searchable(false)->width(140),
            $this->actionsColumn(185),
        ];
    }

    protected function defaultOrder(): array
    {
        return [4, 'desc'];
    }
}
