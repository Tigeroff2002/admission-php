<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\AbiturientEmptyMarksList;

class DirectionEmptySnapshotContent extends Content
{
    public int $direction_id;

    public string $direction_caption;

    public AbiturientEmptyMarksList $abiturients;

    public function __construct($direction_id, $direction_caption, $abiturients)
    {
        $this->direction_id = $direction_id;
        $this->direction_caption = $direction_caption;
        $this->abiturients = $abiturients;
    }
}