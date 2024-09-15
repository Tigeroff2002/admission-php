<?php

namespace App\Contracts;

use App\Contracts\DirectionLink;

final class DirectionLinksList
{
    public array $direction_links;

    public function __construct(array $direction_links) 
    {
        $this->direction_links = $direction_links;
    }
}