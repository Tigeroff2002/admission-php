<?php

namespace App\Contracts\Requests;

class AddDiplomOriginalForUserRequest
{
    public int $abiturient_id;

    public int $target_abiturient_id;

    public string $token;

    public boolean $has_diplom_original;

    function __construct($abiturient_id, $target_abiturient_id, $token, $has_diplom_original) {
        $this->abiturient_id = $abiturient_id;
        $this->target_abiturient_id = $target_abiturient_id;
        $this->token = $token;
        $this->has_diplom_original = $has_diplom_original;
      }
}