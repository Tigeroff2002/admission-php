<?php

namespace App\Contracts\Responses;

use App\Contracts\Content;

class DefaultResponse
{
    public ?Content $content;

    public ?string $failure_message;

    public bool $result;

    function __construct($content, $failure_message, $result) {
        $this->content = $content;
        $this->failure_message = $failure_message;
        $this->result = $result;
      }
}