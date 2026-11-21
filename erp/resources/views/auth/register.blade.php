<x-layouts.base :title="'Create Account — Empetix ERP'">
    <div class="min-h-screen flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-950">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md">E</div>
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">Empetix<span class="text-indigo-600">.</span></span>
                </a>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create an Account' }}
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    {{ app()->getLocale() === 'ar' ? 'انضم لمنصة إدارة الأعمال المتكاملة' : 'Join our modern all-in-one ERP platform' }}
                </p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <x-ui.input
                    name="name"
                    :label="app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name'"
                    placeholder="Ahmed Hassan"
                    :error="$errors->first('name')"
                    required
                />

                <x-ui.input
                    type="email"
                    name="email"
                    :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'"
                    placeholder="ahmed@company.com"
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

                <x-ui.input
                    type="password"
                    name="password_confirmation"
                    :label="app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password'"
                    placeholder="••••••••"
                    required
                />

                <x-ui.button type="submit" class="w-full py-2.5 font-bold shadow-md shadow-indigo-600/25 mt-4">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل حساب' : 'Create Account' }} →
                </x-ui.button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.base>
