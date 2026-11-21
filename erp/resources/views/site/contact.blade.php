<x-layouts.site :title="'Contact Us — Empetix'">
    <section class="py-20 lg:py-28 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Get in Touch' }}</span>
                    <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ app()->getLocale() === 'ar' ? 'دعنا نناقش مشروعك القادم ونبني حلولاً مبتكرة' : 'Let\'s Discuss Your Next Strategic Technology Project' }}
                    </h1>
                    <p class="mt-6 text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ app()->getLocale() === 'ar'
                            ? 'سواء كنت بحاجة إلى استشارة تقنية، أو بناء نظام برمجيات مخصص، أو إطلاق حملات تسويقية ذات عائد مرتفع، فريقنا مستعد لدعمك.'
                            : 'Whether you require technical consultation, custom software development, or enterprise SaaS operations, our engineering leads are ready.' }}
                    </p>

                    <div class="mt-10 space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">📍</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Location</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">New Cairo, Egypt</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">✉</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Email</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">contact@empetix.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">📞</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Phone</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">+20 100 000 0000</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Message Form -->
                <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-lg">
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <x-ui.input :label="app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name'" name="name" required />
                        <x-ui.input :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'" name="email" type="email" required />
                        <x-ui.input :label="app()->getLocale() === 'ar' ? 'الموضوع' : 'Subject'" name="subject" required />
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ app()->getLocale() === 'ar' ? 'الرسالة' : 'Your Message' }}</label>
                            <textarea rows="4" class="block w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white p-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <x-ui.button type="button" class="w-full font-bold py-3 text-sm">
                            {{ app()->getLocale() === 'ar' ? 'إرسال الرسالة' : 'Send Message' }} →
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
