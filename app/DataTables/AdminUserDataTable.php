<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class AdminUserDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private AdminUserRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        $query = $this->repository->listingQuery()->with(['roles', 'member']);
        $user = auth()->user();

        // Hide the currently authenticated user from the admins table so they don't see or delete themselves
        if ($user) {
            $query->where('id', '!=', $user->id);
        }

        return $query;
    }

    protected function routePrefix(): string
    {
        return 'admin.admins';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $currentUser = auth()->user();

        $table
            ->editColumn('name', function ($row) {
                $photo = $row->member?->photo;
                if (! $photo && $row->email) {
                    $photo = \App\Models\Member::where('email', $row->email)->value('photo');
                }

                if ($photo) {
                    $src = str_starts_with((string) $photo, 'uploads/') ? asset('storage/'.$photo) : asset($photo);
                    $avatar = '<img class="thumb thumb--avatar" src="'.e($src).'" alt="'.e($row->name).'"/>';
                } else {
                    $initial = e(mb_strtoupper(mb_substr((string) $row->name, 0, 1)));
                    $avatar = '<span class="dt-avatar-fallback">'.$initial.'</span>';
                }

                $codeBadge = $row->member?->member_code
                    ? ' <span style="font-family:var(--font-mono, monospace); font-size:0.72rem; padding:1px 6px; border-radius:6px; background:rgba(255,77,28,0.10); color:var(--accent, #FF4D1C); font-weight:700; margin-left:6px;">'.e($row->member->member_code).'</span>'
                    : '';

                return '<div class="dt-media-cell">'.$avatar.'<div>'
                    .'<div class="cell-title">'.e($row->name).$codeBadge.'</div>'
                    .'<div class="cell-sub">'.e($row->email).'</div></div></div>';
            })
            ->addColumn('roles', fn ($row) => $row->roles->isEmpty()
                ? '<span class="pill pill--off">No role</span>'
                : '<div class="dt-pills-wrap">'.$row->roles->map(fn ($role) => '<span class="pill pill--on">'.e($role->name).'</span>')->implode('').'</div>')
            ->editColumn('created_at', fn ($row) => e($row->created_at?->format('M j, Y')))
            ->addColumn('actions', function ($row) use ($currentUser) {
                // If the target user is a peer Owner, protect them from edit/delete
                if ($currentUser && $currentUser->hasRole('owner') && $row->hasRole('owner')) {
                    return '<span class="pill pill--accent" style="font-size:0.75rem; padding:3px 8px;">Peer Owner</span>';
                }

                return $this->rowActions($row->id, 'admin');
            })
            ->rawColumns(['name', 'roles', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Admin'),
            Column::computed('roles')->title('Roles'),
            Column::make('created_at')->title('Joined')->searchable(false)->width(130),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [0, 'asc'];
    }
}
