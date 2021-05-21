<?php


namespace Webdevils\Blog\Exceptions;

use Exception;

class InvalidBlogPost extends Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Invalid Blog Post');

        $this->errors = $errors;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }
}
