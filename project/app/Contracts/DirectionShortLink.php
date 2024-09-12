<?php

namespace App\Contracts;

class DirectionShortLink
{
    public int $direction_id;

    public string $direction_caption;

    function __construct($direction_id, $direction_caption) {
        $this->direction_id = $direction_id;
        $this->direction_caption = $direction_caption;
      }
}