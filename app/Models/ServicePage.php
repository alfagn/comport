<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A reviewed language edition of a service landing page. Drafts are never public. */
class ServicePage extends Model
{
    protected $fillable = ['slug', 'locale', 'title', 'summary', 'body', 'meta_title', 'meta_description', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function publicPath(): string
    {
        return ($this->locale === 'ar' ? '/ar' : '').'/services/'.$this->slug;
    }
}
