<?php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use App\Modules\Finance\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CrmClient
 *
 * Client account entity in the tenant database.
 */
class CrmClient extends Model
{
    protected $connection = 'tenant';

    protected $table = 'crm_clients';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'website',
        'address',
        'currency',
        'notes',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'crm_client_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'crm_client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }
}
