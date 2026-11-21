<div class="max-w-2xl mx-auto">
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'بيانات الشركة والضرائب' : 'Company Profile Settings'">
        <form wire:submit.prevent="save" class="space-y-6">
            <x-ui.input
                wire:model="companyName"
                :label="app()->getLocale() === 'ar' ? 'الاسم التجاري للشركة' : 'Legal Company Name'"
                required
            />

            <x-ui.input
                wire:model="taxNumber"
                :label="app()->getLocale() === 'ar' ? 'الرقم الضريبي / السجل التجاري' : 'Tax ID / Registration Number'"
                placeholder="EG-9482019"
            />

            <x-ui.select wire:model="currency" :label="app()->getLocale() === 'ar' ? 'العملة الافتراضية للفواتير' : 'Base Invoicing Currency'">
                <option value="EGP">EGP — Egyptian Pound (ج.م)</option>
                <option value="USD">USD — US Dollar ($)</option>
                <option value="SAR">SAR — Saudi Riyal (ر.س)</option>
                <option value="EUR">EUR — Euro (€)</option>
            </x-ui.select>

            <div class="pt-4">
                <x-ui.button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات الشركة' : 'Save Settings' }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ app()->getLocale() === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}
                    </span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
