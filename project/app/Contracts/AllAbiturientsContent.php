<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\AbiturientLinksList;

class AllAbiturientsContent extends Content
{
    public AbiturientLinksList $abiturients;

    public function __construct($abiturients)
    {
        $this->abiturients = $abiturients;
    }
}