<?php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use App\Modules\TenantCore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CrmActivity
 *
 * Sales follow-ups, calls, and meetings scheduled with clients or leads.
 */
class CrmActivity extends Model
{
    protected $connection = 'tenant';

    protected $table = 'crm_activities';

    protected $fillable = [
        'subject',
        'type', // call, meeting, email, note, task
        'due_date',
        'status', // pending, completed
        'crm_client_id',
        'crm_lead_id',
        'user_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }
}
