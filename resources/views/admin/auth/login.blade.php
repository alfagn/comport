<!DOCTYPE html>
{{-- Empetix Command sign-in screen. --}}
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow"/>
  <title>Sign in — Empetix Command</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon.svg') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/base/typography.css') }}"/>
  <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}"/>
</head>
<body class="admin">
<div class="login-wrap">
  <div class="login-brand">
    <img src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="Empetix"/>
    <h2>Run the whole site<br/>from <em>one command room.</em></h2>
    <p>Every word, image, project, job opening and brief across the Empetix website — editable from here.</p>
  </div>
  <div class="login-form-side">
    <div class="login-card">
      <h1>Welcome back</h1>
      <p class="sub">Sign in to Empetix Command.</p>

      @if($errors->any())
        <div class="flash" style="background:rgba(248,113,113,.1);border-color:rgba(248,113,113,.35);color:var(--danger)">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <x-admin.input name="email" label="Email" type="email" :required="true" placeholder="you@empetix.com"/>
        <x-admin.input name="password" label="Password" type="password" :required="true" placeholder="••••••••••"/>
        <x-admin.toggle name="remember" label="Keep me signed in"/>
        <div style="margin-top:24px">
          <button class="btn btn--primary" type="submit" style="width:100%;justify-content:center;padding:12px">Sign in</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
