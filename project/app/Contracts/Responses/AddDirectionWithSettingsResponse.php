<?php

namespace App\Contracts\Responses;

use App\Contracts\Content;

class AddDirectionWithSettingsResponse
{
    public int $abiturient_id;

    public string $token;

    public int $direction_id;

    public Content $content;

    public string $failure_message;

    public boolean $result;

    function __construct($abiturient_id, $token, $direction_id, $content, $failure_message, $result) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->direction_id = $direction_id;
        $this->content = $content;
        $this->failure_message = $failure_message;
        $this->result = $result;
      }
}