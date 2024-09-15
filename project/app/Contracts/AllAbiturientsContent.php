<?php

namespace App\Contracts;

use App\Contracts\Content;

class AllAbiturientsContent extends Content
{
    public array $abiturients;

    public function __construct($abiturients)
    {
        $this->abiturients = $abiturients;
    }
}