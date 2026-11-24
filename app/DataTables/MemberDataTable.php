<?php

namespace App\DataTables;

use App\Core\DataTables\AdminDataTable;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class MemberDataTable extends AdminDataTable
{
    public function __construct(DataTables $datatables, HtmlBuilder $html, private MemberRepositoryInterface $repository)
    {
        parent::__construct($datatables, $html);
    }

    protected function query(): Builder
    {
        $query = $this->repository->listingQuery();
        $user = auth()->user();

        // Hide the currently authenticated user from the team table so they don't see themselves or accidentally delete themselves
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', '!=', $user->id);
            });
            if ($user->email) {
                $query->where('email', '!=', $user->email);
            }
        }

        return $query;
    }

    protected function routePrefix(): string
    {
        return 'admin.members';
    }

    protected function decorate(EloquentDataTable $table): void
    {
        $table
            ->editColumn('name', function ($row) {
                if ($row->photo) {
                    $src = str_starts_with((string) $row->photo, 'uploads/') ? asset('storage/'.$row->photo) : asset($row->photo);
                    $avatar = '<img class="thumb thumb--avatar" src="'.e($src).'" alt=""/>';
                } else {
                    $initial = e(mb_strtoupper(mb_substr((string) $row->name, 0, 1)));
                    $avatar = '<span class="dt-avatar-fallback">'.$initial.'</span>';
                }

                return '<div class="dt-media-cell">'.$avatar.'<div>'
                    .'<div class="cell-title">'.e($row->name).'</div>'
                    .'<div class="cell-sub">'.e($row->role ?? '').'</div></div></div>';
            })
            ->editColumn('kind', fn ($row) => $row->kind === 'founder'
                ? '<span class="pill pill--accent">Founder</span>'
                : '<span class="pill">Team Member</span>')
            ->editColumn('sort_order', fn ($row) => $this->orderBadge($row->sort_order))
            ->editColumn('is_active', fn ($row) => $this->statusToggle('members', $row->id, (bool) $row->is_active, 'Live', 'Hidden'))
            ->addColumn('actions', fn ($row) => $this->rowActions($row->id, 'member'))
            ->rawColumns(['name', 'kind', 'sort_order', 'is_active', 'actions']);
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Person'),
            Column::make('kind')->title('Role Type')->width(130),
            Column::make('sort_order')->title('Order')->searchable(false)->width(90),
            Column::make('is_active')->title('Status')->searchable(false)->width(130),
            $this->actionsColumn(),
        ];
    }

    protected function defaultOrder(): array
    {
        return [2, 'asc'];
    }
}
