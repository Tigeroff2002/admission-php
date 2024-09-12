<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\PlaceSnapshotsList;

class DirectionSnapshotContent extends Content
{
    public int $direction_id;

    public string $direction_caption;

    public PlaceSnapshotsList $places;

    public function __construct($direction_id, $direction_caption, $places)
    {
        $this->direction_id = $direction_id;
        $this->direction_caption = $direction_caption;
        $this->places = $places;
    }
}