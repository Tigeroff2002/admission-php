<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\DirectionShortLinksList;

class DirectionsShortContent extends Content
{
    public DirectionShortLinksList $directions;

    public function __construct($directions)
    {
        $this->directions = $directions;
    }
}