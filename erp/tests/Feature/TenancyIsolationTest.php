<?php

declare(strict_types=1);

use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;
use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Finder\DomainTenantFinder;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Pipelines\TenantProvisioningPipeline;
use Illuminate\Http\Request;

it('provisions two separate tenants with physical database isolation', function () {
    $pipeline = app(TenantProvisioningPipeline::class);

    // 1. Provision Tenant Alpha
    $alpha = $pipeline->handle(new ProvisionTenantData(
        companyName: 'Company Alpha',
        slug: 'alpha-test-'.time(),
        ownerName: 'Alpha Owner',
        ownerEmail: 'owner@alpha.test',
        ownerPassword: 'secretpassword123',
        trialDays: 14
    ));

    // 2. Provision Tenant Beta
    $beta = $pipeline->handle(new ProvisionTenantData(
        companyName: 'Company Beta',
        slug: 'beta-test-'.time(),
        ownerName: 'Beta Owner',
        ownerEmail: 'owner@beta.test',
        ownerPassword: 'secretpassword123',
        trialDays: 14
    ));

    expect($alpha->getDatabaseName())->not->toBe($beta->getDatabaseName());

    // 3. Insert record in Tenant Alpha DB
    $alpha->makeCurrent();
    $deptAlpha = Department::on('tenant')->create(['name' => 'Alpha Secret Department', 'code' => 'ASD']);
    $empAlpha = Employee::on('tenant')->create([
        'employee_code' => 'ALPHA-001',
        'first_name' => 'Alice',
        'last_name' => 'Alpha',
        'email' => 'alice@alpha.test',
        'department_id' => $deptAlpha->id,
        'basic_salary_minor' => 2000000,
    ]);
    $alphaCount = Employee::on('tenant')->count();
    $alpha->forgetCurrent();

    expect($alphaCount)->toBeGreaterThanOrEqual(1);

    // 4. Switch to Tenant Beta DB and verify absolute isolation (Alice must NOT exist here)
    $beta->makeCurrent();
    $betaFound = Employee::on('tenant')->where('email', 'alice@alpha.test')->first();
    $beta->forgetCurrent();

    expect($betaFound)->toBeNull();
});

it('resolves central domains as null and tenant subdomains correctly', function () {
    $finder = new DomainTenantFinder;

    // Central domain check
    $centralReq = Request::create('http://empetix.test/');
    expect($finder->findForRequest($centralReq))->toBeNull();

    // Tenant subdomain check
    $tenant = Tenant::on('landlord')->first();
    if ($tenant !== null) {
        $subdomainReq = Request::create("http://{$tenant->slug}.empetix.test/");
        $found = $finder->findForRequest($subdomainReq);

        expect($found)->not->toBeNull();
        expect($found->id)->toBe($tenant->id);
    }
});
