<?php

namespace App\Contracts\Requests;

class GetUserLkContentRequest
{
    public int $abiturient_id;

    public string $token;

    function __construct($abiturient_id, $token) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
      }
}