<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\TeamMember;
use Illuminate\Support\Str;

/**
 * Class CmsTeamIndex
 *
 * Livewire component managing Founders and Team Members for the public website.
 */
class CmsTeamIndex extends BaseComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $role_title = '';

    public string $bio = '';

    public string $avatar = '';

    public bool $is_founder = false;

    public string $linkedin = '';

    public string $github = '';

    public string $twitter = '';

    public int $sort_order = 0;

    public string $filter = 'all'; // all, founders, team

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'role_title', 'bio', 'avatar', 'is_founder', 'linkedin', 'github', 'twitter', 'sort_order']);
        $this->sort_order = TeamMember::on('landlord')->count() + 1;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $member = TeamMember::on('landlord')->findOrFail($id);
        $this->editingId = $member->id;
        $this->name = $member->name;
        $this->slug = $member->slug;
        $this->role_title = $member->role_title;
        $this->bio = (string) $member->bio;
        $this->avatar = (string) $member->avatar;
        $this->is_founder = (bool) $member->is_founder;
        $links = $member->social_links ?? [];
        $this->linkedin = (string) ($links['linkedin'] ?? '');
        $this->github = (string) ($links['github'] ?? '');
        $this->twitter = (string) ($links['twitter'] ?? '');
        $this->sort_order = (int) $member->sort_order;
        $this->showModal = true;
    }

    public function updatedName(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'role_title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->slug ?: $this->name),
            'role_title' => $this->role_title,
            'bio' => $this->bio,
            'avatar' => $this->avatar ?: 'assets/img/team/mohanad-shehata.jpg',
            'is_founder' => $this->is_founder,
            'social_links' => array_filter([
                'linkedin' => $this->linkedin,
                'github' => $this->github,
                'twitter' => $this->twitter,
            ]),
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId) {
            TeamMember::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث بيانات العضو بنجاح!' : 'Team member updated successfully!');
        } else {
            TeamMember::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة عضو الفريق بنجاح!' : 'Team member added successfully!');
        }

        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        TeamMember::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم حذف العضو' : 'Member deleted successfully');
    }

    public function render()
    {
        $query = TeamMember::on('landlord')->orderBy('sort_order')->orderBy('id');

        if ($this->filter === 'founders') {
            $query->where('is_founder', true);
        } elseif ($this->filter === 'team') {
            $query->where('is_founder', false);
        }

        $members = $query->get();

        return view('admin::livewire.cms-team-index', [
            'members' => $members,
            'foundersCount' => TeamMember::on('landlord')->where('is_founder', true)->count(),
            'totalCount' => TeamMember::on('landlord')->count(),
        ])->layout('layouts.admin', [
            'title' => 'Founders & Leadership Team — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'إدارة المؤسسين وفريق العمل' : 'Founders & Leadership Team Management',
        ]);
    }
}
