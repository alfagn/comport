{{--
  Shared founders strip, rendered from the members table (kind = founder).
  Used by the home, about and team pages.
--}}
<!-- PARTIAL: founders -->
<section id="founders" class="founders" aria-labelledby="founders-heading">
  <div class="founders__inner">
    <header class="founders__header">
      <h2 id="founders-heading" class="founders__title">The founders</h2>
      <div class="founders__intro">
        <p class="founders__lead">
          Every engagement at Empetix Solution is led directly by our three co-founders across executive strategy, operations, and software engineering.
        </p>
        <a href="{{ url('/team') }}" class="founders__story-link">Meet the full team</a>
      </div>
    </header>

    <ul class="founders__list">
      @foreach($founders as $founder)
@php
  // Uploaded photos live on the public disk; seeded photos point at /assets.
  $photo = $founder->photo
      ? (str_starts_with($founder->photo, 'uploads/') ? asset('storage/'.$founder->photo) : asset($founder->photo))
      : asset('assets/img/team/placeholder.jpg');
@endphp

      <li class="founders__item">
        <a href="{{ route('founders.show', $founder->slug) }}" class="founders__link">
          <div class="founders__media">
            <img
              class="founders__photo"
              src="{{ $photo }}"
              alt="Portrait of {{ $founder->name }}, {{ $founder->role }}"
              width="800"
              height="1000"
              loading="lazy"
            />
          </div>
          <h3 class="founders__name">{{ $founder->name }}</h3>
          <p class="founders__role">{{ $founder->role }}</p>
        </a>
      </li>
      
@endforeach
    </ul>
  </div>
</section>
<!-- END PARTIAL: founders -->
