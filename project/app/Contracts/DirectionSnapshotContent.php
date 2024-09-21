<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\PlaceSnapshotsList;

class DirectionSnapshotContent extends Content
{
    public int $direction_id;

    public string $direction_caption;

    public int $budget_places_number;

    public int $min_ball;

    public array $places;

    public function __construct($direction_id, $direction_caption, $budget_places_number, $min_ball, $places)
    {
        $this->direction_id = $direction_id;
        $this->direction_caption = $direction_caption;
        $this->budget_places_number = $budget_places_number;
        $this->min_ball = $min_ball;    
        $this->places = $places;
    }
}