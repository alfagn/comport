<x-layouts.base :title="'Sign In — Empetix ERP'">
    <div class="min-h-screen flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-950">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md">E</div>
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">Empetix<span class="text-indigo-600">.</span></span>
                </a>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in to your account' }}
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    {{ app()->getLocale() === 'ar' ? 'أدخل بريدك الإلكتروني وكلمة المرور' : 'Access your enterprise dashboard' }}
                </p>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <x-ui.input
                    type="email"
                    name="email"
                    :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'"
                    placeholder="name@company.com"
                    :error="$errors->first('email')"
                    required
                />

                <x-ui.input
                    type="password"
                    name="password"
                    :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                    placeholder="••••••••"
                    :error="$errors->first('password')"
                    required
                />

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>{{ app()->getLocale() === 'ar' ? 'تذكرني' : 'Remember me' }}</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="font-bold text-indigo-600 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                    </a>
                </div>

                <x-ui.button type="submit" class="w-full py-2.5 font-bold shadow-md shadow-indigo-600/25">
                    {{ app()->getLocale() === 'ar' ? 'دخول' : 'Sign in' }} →
                </x-ui.button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب شركة؟' : 'Don\'t have an account?' }}
                <a href="{{ route('site.pricing') }}" class="font-bold text-indigo-600 hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'ابدأ تجربة مجانية' : 'Start free trial' }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.base>
