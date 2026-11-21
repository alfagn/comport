<?php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CrmContact
 *
 * Specific point of contact person within a client organization.
 */
class CrmContact extends Model
{
    protected $connection = 'tenant';

    protected $table = 'crm_contacts';

    protected $fillable = [
        'crm_client_id',
        'name',
        'email',
        'phone',
        'position',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }
}
