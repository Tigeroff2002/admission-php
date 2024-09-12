<?php

namespace App\Contracts\Responses;

use App\Contracts\Content;

class DefaultResponse
{
    public int $abiturient_id;

    public string $token;

    public Content $content;

    public string $failure_message;

    public boolean $result;

    function __construct($abiturient_id, $token, $content, $failure_message, $result) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->content = $content;
        $this->failure_message = $failure_message;
        $this->result = $result;
      }
}