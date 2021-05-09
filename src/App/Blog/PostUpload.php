<?php

declare(strict_types=1);

namespace App\Blog;

use Sorani\SimpleFramework\Http\Upload;

class PostUpload extends Upload
{
    protected $path = 'public/uploads/posts';
}
