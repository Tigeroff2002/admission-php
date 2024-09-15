<?php

namespace App\Contracts\Responses;

use App\Contracts\AllAbiturientsContent;

class GetAllAbiturientsResponse
{
    public int $abiturient_id;

    public string $token;

    public ?AllAbiturientsContent $content;

    public ?string $failure_message;

    public bool $result;

    function __construct($abiturient_id, $token, $content, $failure_message, $result) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->content = $content;
        $this->failure_message = $failure_message;
        $this->result = $result;
      }
}