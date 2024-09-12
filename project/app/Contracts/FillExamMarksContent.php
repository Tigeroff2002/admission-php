<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\AbiturientEmptyMarksList;

class FillExamMarksContent extends Content
{
    public int $direction_id;

    public AbiturientEmptyMarksList $abiturients;

    public function __construct($direction_id, $abiturients)
    {
        $this->direction_id = $direction_id;
        $this->abiturients = $abiturients;
    }
}