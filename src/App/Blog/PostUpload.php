<?php

declare(strict_types=1);

namespace App\Blog;

use Sorani\SimpleFramework\Http\Upload;

/**
 * Upload am image in the Post Crud
 */
class PostUpload extends Upload
{
    /**
     * @var string
     * {@inheritdoc}
     */
    protected $path = 'public/uploads/posts';

    /**
     * {@inheritdoc}
     */
    protected $formats = [
        'thumb' => [320, 180],
    ];
}
