<?php

namespace App\Contracts\Requests;

class GetDirectionEmptyResultsRequest
{
    public int $abiturient_id;

    public string $token;

    public int $direction_id;

    function __construct($abiturient_id, $token, $direction_id) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->direction_id = $direction_id;
      }
}