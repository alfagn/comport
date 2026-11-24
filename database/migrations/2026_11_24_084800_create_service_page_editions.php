<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Separate editions leave homepage service records and custom content untouched. */
    public function up(): void
    {
        Schema::create('service_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('locale', 2);
            $table->string('title');
            $table->text('summary');
            $table->text('body');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
            $table->unique(['slug', 'locale']);
        });
        // Draft language is a proposal for owner review, not verified capability or customer evidence.
        $drafts = [
            ['saas-development', 'en', 'SaaS development',
                'Plan a SaaS product around your users, business model and the workflows that matter.',
                "Start with the product problem\nWho will use the product, what are they trying to do, and what makes the current process difficult? A useful project brief answers these questions before choosing a technology stack.\n\nDefine the first release\nDescribe the essential user journeys, subscription or billing needs, account roles, integrations and reporting. Separate the launch scope from later ideas.\n\nDiscuss architecture and delivery\nBring any requirements for tenant isolation, security, accessibility, Arabic and English interfaces, performance and existing systems. The proposed scope should make assumptions, dependencies and acceptance criteria clear.\n\nPrepare an enquiry\nShare your product stage, target users, desired launch window and any existing designs or code. Avoid sending passwords, customer records or other sensitive data through the contact form."],
            ['saas-development', 'ar', 'تطوير منصات SaaS',
                'ابدأ تخطيط منتجك البرمجي من احتياجات المستخدمين ونموذج العمل والمهام الأساسية.',
                "ابدأ بالمشكلة التي يحلها المنتج\nمن سيستخدم المنتج؟ وما المهمة التي يريد إنجازها؟ وما الصعوبة في الطريقة الحالية؟ توضيح هذه النقاط يسبق اختيار التقنيات.\n\nحدد نطاق الإصدار الأول\nاكتب رحلات الاستخدام الأساسية، ومتطلبات الاشتراكات والفوترة، وصلاحيات الحسابات، والتكاملات والتقارير. افصل احتياجات الإطلاق عن أفكار المراحل التالية.\n\nناقش البنية وخطة التنفيذ\nوضح متطلبات فصل بيانات العملاء، والأمان، وسهولة الاستخدام، والواجهات العربية والإنجليزية، والأداء والأنظمة الحالية. يجب أن يوضح النطاق المقترح الافتراضات والاعتماديات ومعايير القبول.\n\nجهز طلب المشروع\nشارك مرحلة المنتج، والجمهور المستهدف، وموعد الإطلاق المطلوب، وأي تصميمات أو كود موجود. لا ترسل كلمات مرور أو بيانات عملاء حساسة من نموذج التواصل."],
            ['erp-systems', 'en', 'ERP systems and integrations',
                'Map the workflows, data and integrations your business needs before defining an ERP project.',
                "Document the workflow first\nList the steps your teams follow today and where information is entered more than once. Start with the operational problem, not a list of software modules.\n\nIdentify the systems and data\nDescribe existing finance, inventory, sales, purchasing or HR tools relevant to the project. Note data owners, migration requirements and integration constraints.\n\nAgree on controls and rollout\nDiscuss user permissions, approval steps, audit history, reporting and the order in which teams would adopt the system. Define how a pilot would be accepted before a wider rollout.\n\nPrepare an enquiry\nShare the processes you want to improve, approximate user groups and the systems involved. Do not include financial exports, employee records or login credentials in an initial enquiry."],
            ['erp-systems', 'ar', 'أنظمة ERP وتكامل الأعمال',
                'حدد إجراءات العمل والبيانات والتكاملات المطلوبة قبل تحديد نطاق مشروع ERP.',
                "وثق إجراءات العمل أولًا\nاكتب الخطوات التي تتبعها الفرق حاليًا، وأماكن تكرار إدخال البيانات. ابدأ بالمشكلة التشغيلية بدلًا من قائمة وحدات برمجية.\n\nحدد الأنظمة والبيانات\nوضح أدوات الحسابات أو المخزون أو المبيعات أو المشتريات أو الموارد البشرية المرتبطة بالمشروع. حدد المسؤول عن البيانات ومتطلبات نقلها وقيود التكامل.\n\nاتفق على الصلاحيات وخطة الإطلاق\nناقش صلاحيات المستخدمين، وخطوات الموافقة، وسجل التغييرات، والتقارير، وترتيب تطبيق النظام على الفرق. حدد معايير قبول النسخة التجريبية قبل التوسع.\n\nجهز طلب المشروع\nشارك الإجراءات التي تريد تحسينها، وفئات المستخدمين المتوقعة والأنظمة الحالية. لا ترفق سجلات مالية أو بيانات موظفين أو كلمات مرور في الطلب الأول."],
            ['performance-marketing', 'en', 'Performance marketing',
                'Connect campaign planning with a clear offer, useful landing pages and measurable business goals.',
                "Define the commercial goal\nClarify whether the priority is qualified enquiries, product sales, subscriptions or another measurable outcome. Agree on the audience and the offer before selecting channels.\n\nReview the customer journey\nDescribe the steps between an advert and a completed enquiry or purchase. Identify the landing pages, creative material and follow-up process that need attention.\n\nPlan measurement responsibly\nList the conversion events and reporting needed to evaluate a campaign. Tracking choices should account for consent, privacy requirements and the data your business is allowed to use.\n\nPrepare an enquiry\nShare your offer, intended markets, existing channels and a budget range you are comfortable discussing. Advertising spend, scope and expected reporting should be agreed separately. No campaign can promise a fixed return."],
            ['performance-marketing', 'ar', 'التسويق بالأداء',
                'اربط تخطيط الحملات بعرض واضح وصفحات مناسبة وأهداف تجارية قابلة للقياس.',
                "حدد الهدف التجاري\nهل الأولوية لطلبات تواصل جادة، أم مبيعات، أم اشتراكات، أم نتيجة أخرى قابلة للقياس؟ حدد الجمهور والعرض قبل اختيار القنوات.\n\nراجع رحلة العميل\nوضح الخطوات من الإعلان حتى إرسال طلب أو إتمام شراء. حدد صفحات الهبوط والمواد الإعلانية وخطوات المتابعة التي تحتاج إلى مراجعة.\n\nخطط للقياس بمسؤولية\nاكتب أحداث التحويل والتقارير اللازمة لتقييم الحملة. يجب أن تراعي أدوات التتبع الموافقة والخصوصية والبيانات المسموح لنشاطك باستخدامها.\n\nجهز طلب المشروع\nشارك العرض والأسواق المستهدفة والقنوات الحالية ونطاق الميزانية المناسب للمناقشة. يتحدد الإنفاق الإعلاني ونطاق العمل والتقارير باتفاق منفصل. لا توجد حملة تضمن عائدًا ثابتًا."],
        ];
        foreach ($drafts as [$slug, $locale, $title, $summary, $body]) {
            DB::table('service_pages')->insert([
                'slug' => $slug, 'locale' => $locale, 'title' => $title,
                'summary' => $summary, 'body' => $body,
                'meta_title' => $title.' | Empetix', 'meta_description' => $summary,
                'is_published' => false, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    /** Editorial content must not be silently removed during rollback. */
    public function down(): void {}
};
