<?php

namespace App\Contracts\Requests;

class AddDirectionWithSettingsRequest
{
    public int $abiturient_id;

    public string $token;

    public string $direction_caption;

    public int $budget_places_number;

    public int $min_ball;

    function __construct($abiturient_id, $token, $direction_caption, $budget_places_number, $min_ball) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->direction_caption = $direction_caption;
        $this->budget_places_number = $budget_places_number;
        $this->min_ball = $min_ball;
      }
}