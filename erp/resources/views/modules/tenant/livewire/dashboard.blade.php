<div>
    <!-- Operational KPI Matrix -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- HRM Stat -->
        <x-ui.stat
            :title="app()->getLocale() === 'ar' ? 'الموظفين والحضور اليوم' : 'Active Employees'"
            :value="$totalEmployees"
            :change="$presentToday . ' ' . (app()->getLocale() === 'ar' ? 'حاضر اليوم' : 'present today')"
            changeType="positive"
            icon="👥"
        />

        <!-- CRM Stat -->
        <x-ui.stat
            :title="app()->getLocale() === 'ar' ? 'العملاء المتوقعين (Leads)' : 'Sales Pipeline'"
            :value="number_format($pipelineValueMinor / 100) . ' EGP'"
            :change="$leadsCount . ' ' . (app()->getLocale() === 'ar' ? 'فرصة نشطة' : 'open leads')"
            changeType="positive"
            icon="🎯"
        />

        <!-- Marketing Stat -->
        <x-ui.stat
            :title="app()->getLocale() === 'ar' ? 'الإنفاق الإعلاني النشط' : 'Active Ad Spend'"
            :value="number_format($totalMarketingSpendMinor / 100) . ' EGP'"
            :change="$activeCampaigns . ' ' . (app()->getLocale() === 'ar' ? 'حملات جارية' : 'active campaigns')"
            changeType="neutral"
            icon="📢"
        />

        <!-- Finance Invoiced Stat -->
        <x-ui.stat
            :title="app()->getLocale() === 'ar' ? 'الفواتير المحصلة' : 'Revenue Collected'"
            :value="number_format($paidInvoicedMinor / 100) . ' EGP'"
            :change="number_format($totalInvoicedMinor / 100) . ' EGP ' . (app()->getLocale() === 'ar' ? 'إجمالي المفوتر' : 'total invoiced')"
            changeType="positive"
            icon="💵"
        />
    </div>

    <!-- Quick Navigation Modules -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- HRM Quick Access -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">👨‍💼</span>
                    <x-ui.badge variant="primary">HRM</x-ui.badge>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
                    {{ app()->getLocale() === 'ar' ? 'الموارد البشرية والمرتبات' : 'Employees & Payroll' }}
                </h3>
                <p class="text-xs text-slate-500 mb-6">
                    {{ app()->getLocale() === 'ar' ? 'سجلات الحضور، الإجازات، ومسيرات الرواتب الشهرية.' : 'Attendance logs, leave approvals, and monthly salary slips.' }}
                </p>
            </div>
            <a href="{{ route('tenant.hrm.employees') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                {{ app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Manage Employees' }} →
            </a>
        </div>

        <!-- Marketing Quick Access -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">🚀</span>
                    <x-ui.badge variant="success">Marketing</x-ui.badge>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
                    {{ app()->getLocale() === 'ar' ? 'الحملات والتسويق' : 'Marketing & Ads' }}
                </h3>
                <p class="text-xs text-slate-500 mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إدارة حملات العملاء وتتبع تكلفة النقرة CPL و ROI.' : 'Client campaigns, ad spend tracking, CPC, and CPL metrics.' }}
                </p>
            </div>
            <a href="{{ route('tenant.marketing.campaigns') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                {{ app()->getLocale() === 'ar' ? 'عرض الحملات' : 'View Campaigns' }} →
            </a>
        </div>

        <!-- Invoices Quick Access -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl">📑</span>
                    <x-ui.badge variant="info">Invoices</x-ui.badge>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
                    {{ app()->getLocale() === 'ar' ? 'الفواتير والتحصيل' : 'Invoices & Billing' }}
                </h3>
                <p class="text-xs text-slate-500 mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إصدار الفواتير، عروض الأسعار، وتسجيل المدفوعات.' : 'Commercial billing, line items, and client payments.' }}
                </p>
            </div>
            <a href="{{ route('tenant.finance.invoices') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                {{ app()->getLocale() === 'ar' ? 'إدارة الفواتير' : 'Open Invoices' }} →
            </a>
        </div>
    </div>
</div>
