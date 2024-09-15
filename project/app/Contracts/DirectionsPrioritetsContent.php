<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\DirectionPrioritetLinksList;


class DirectionsPrioritetsContent extends Content
{
    public int $target_abiturient_id;

    public bool $has_diplom_original;

    public DirectionPrioritetLinksList $prioritets;

    public function __construct($target_abiturient_id, $has_diplom_original, $prioritets)
    {
        $this->target_abiturient_id = $target_abiturient_id;
        $this->has_diplom_original = $has_diplom_original;
        $this->prioritets = $prioritets;
    }
}