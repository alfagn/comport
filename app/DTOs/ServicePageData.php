<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/** Only editorial fields pass from validated input to persistence. */
class ServicePageData extends BaseData
{
    protected static array $fields = ['slug', 'title', 'summary', 'body', 'meta_title', 'meta_description', 'is_published'];

    public mixed $slug = null;

    public mixed $title = null;

    public mixed $summary = null;

    public mixed $body = null;

    public mixed $meta_title = null;

    public mixed $meta_description = null;

    public mixed $is_published = null;
}
