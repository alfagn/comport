@extends('layouts.admin')

@section('title', $item ? 'Edit Person' : 'New Person')
@section('crumb', 'Team & Founders')

@section('content')
<div class="card">
  <form method="post" enctype="multipart/form-data" action="{{ $item ? route('admin.members.update', $item->id) : route('admin.members.store') }}">
    @csrf
    @if($item) @method('PUT') @endif
    <div class="form-grid">
      <x-admin.input name="member_code" label="Unique Team ID" :value="$item?->member_code" placeholder="Auto-generated (e.g. EMP-00001)" hint="Leave empty to auto-generate a unique ID, or enter a custom unique ID."/>
      <x-admin.input name="email" label="Unique Email Address" type="email" :value="$item?->email" :required="true" placeholder="name@empetix.com" hint="Must be unique for each team member."/>
      <x-admin.input name="name" label="Full name" :value="$item?->name" :required="true"/>
      <x-admin.input name="slug" label="Slug" :value="$item?->slug" hint="Used in the founder page URL. Leave empty to generate."/>
      <x-admin.input name="role" label="Role" :value="$item?->role" placeholder="Co-founder & CEO"/>
      <x-admin.select name="kind" label="Kind" :options="['member' => 'Team member', 'founder' => 'Founder (gets a profile page)']" :value="$item?->kind ?? 'member'"/>
      <x-admin.select name="department" label="Department (team directory)" :options="['' => 'General team / not assigned', 'engineering' => 'Software engineering', 'design' => 'Product design', 'cloud' => 'Cloud, DevOps & AI']" :value="$item?->department ?? ''"/>
      <div class="full"><x-admin.input name="focus" label="Focus line" :value="$item?->focus" hint="One-line specialty shown on team cards."/></div>
      <div class="full"><x-admin.textarea name="lead" label="Lead paragraph" :value="$item?->lead" rows="2" hint="Founders only — shown under the role on the profile page."/></div>
      <div class="full"><x-admin.textarea name="bio" label="Bio paragraphs (one per line)" :value="$item ? implode(PHP_EOL, (array) $item->bio) : ''" rows="5" hint="Founders only — rendered on the profile page."/></div>
      <div class="full"><x-admin.textarea name="expertise" label="Expertise (Title | description, one per line)" :value="$item ? collect((array) $item->expertise)->map(fn($e) => ($e['title'] ?? '').' | '.($e['desc'] ?? ''))->implode(PHP_EOL) : ''" rows="4" hint="Founders only."/></div>
      <x-admin.input name="linkedin" label="LinkedIn URL" type="url" :value="$item?->linkedin"/>
      <x-admin.input name="twitter" label="Twitter / X URL" type="url" :value="$item?->twitter"/>
      <x-admin.input name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0"/>
      <div class="full"><x-admin.image name="photo" label="Photo" :current="$item?->photo"/></div>
      <x-admin.toggle name="is_active" label="Live on the site" :checked="$item?->is_active ?? true"/>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $item ? 'Save changes' : 'Create person' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.members.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection
