<div
    x-data="{
        toasts: [],
        add(toast) {
            toast.id = Date.now();
            this.toasts.push(toast);
            setTimeout(() => {
                this.remove(toast.id);
            }, 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed bottom-5 end-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="pointer-events-auto p-4 rounded-xl shadow-lg border text-sm flex items-start gap-3 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800"
            :class="{
                'border-emerald-500/30 text-emerald-950 dark:text-emerald-100': toast.type === 'success',
                'border-rose-500/30 text-rose-950 dark:text-rose-100': toast.type === 'error',
                'border-amber-500/30 text-amber-950 dark:text-amber-100': toast.type === 'warning',
                'border-indigo-500/30 text-indigo-950 dark:text-indigo-100': toast.type === 'info'
            }"
        >
            <div class="mt-0.5">
                <template x-if="toast.type === 'success'">
                    <span class="text-emerald-500">✓</span>
                </template>
                <template x-if="toast.type === 'error'">
                    <span class="text-rose-500">✕</span>
                </template>
                <template x-if="toast.type === 'warning'">
                    <span class="text-amber-500">!</span>
                </template>
                <template x-if="toast.type === 'info'">
                    <span class="text-indigo-500">ℹ</span>
                </template>
            </div>
            <div class="flex-1">
                <p class="font-medium" x-text="toast.message"></p>
            </div>
            <button type="button" x-on:click="remove(toast.id)" class="text-slate-400 hover:text-slate-500">✕</button>
        </div>
    </template>
</div>
