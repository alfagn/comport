<?php

declare(strict_types=1);

test('public website pages return successful status code', function () {
    $this->get(route('home'))->assertOk();
    $this->get(route('site.about'))->assertOk();
    $this->get(route('site.work'))->assertOk();
    $this->get(route('site.blog'))->assertOk();
    $this->get(route('site.careers'))->assertOk();
    $this->get(route('site.pricing'))->assertOk();
    $this->get(route('site.contact'))->assertOk();
});

test('landlord super admin panel views render without errors', function () {
    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.tenants'))->assertOk();
    $this->get(route('admin.subscriptions'))->assertOk();
    $this->get(route('admin.cms.posts'))->assertOk();
    $this->get(route('admin.cms.settings'))->assertOk();
    $this->get(route('admin.cms.team'))->assertOk();
    $this->get(route('admin.cms.portfolio'))->assertOk();
    $this->get(route('admin.cms.careers'))->assertOk();
    $this->get(route('admin.cms.applications'))->assertOk();
    $this->get(route('admin.cms.partners'))->assertOk();
    $this->get(route('admin.cms.inquiries'))->assertOk();
    $this->get(route('admin.cms.testimonials'))->assertOk();
});

test('tenant erp workspace modules render properly with multi-database tenancy', function () {
    $this->get(route('tenant.path.dashboard', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.hrm.employees', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.hrm.payroll', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.crm.leads', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.marketing.campaigns', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.projects.index', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.finance.invoices', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.subscription', ['tenant_slug' => 'acme']))->assertOk();
    $this->get(route('tenant.path.settings', ['tenant_slug' => 'acme']))->assertOk();
});
