<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A footer newsletter subscription (email only).
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = ['email'];
}
