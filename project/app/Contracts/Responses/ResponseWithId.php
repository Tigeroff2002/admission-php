<?php

namespace App\Contracts\Responses;

use App\Contracts\Content;

class ResponseWithId
{
    public int $abiturient_id;

    public string $token;

    public bool $is_admin;

    public ?Content $content;

    public ?string $failure_message;

    public bool $result;

    function __construct($abiturient_id, $token, $is_admin, $content, $failure_message, $result) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token; 
        $this->is_admin = $is_admin; 
        $this->content = $content;
        $this->failure_message = $failure_message;
        $this->result = $result;
      }
}