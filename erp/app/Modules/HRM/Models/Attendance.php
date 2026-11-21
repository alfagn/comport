<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Attendance
 *
 * Daily attendance log for employees.
 *
 * @property int $id
 * @property int $employee_id
 * @property Carbon $date
 * @property string|null $clock_in
 * @property string|null $clock_out
 * @property string $status present|absent|late|half_day|on_leave
 * @property string|null $notes
 */
class Attendance extends Model
{
    protected $connection = 'tenant';

    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
