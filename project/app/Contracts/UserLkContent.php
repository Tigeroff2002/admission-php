<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\DirectionLinksList;

class UserLkContent extends Content
{
    public string $first_name;

    public string $second_name;

    public string $email;

    public boolean $has_diplom_original;

    public DirectionLinksList $directions;

    public function __construct($first_name, $second_name, $email, $has_diplom_original, $directions)
    {
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->email = $email;
        $this->has_diplom_original = $has_diplom_original;
        $this->directions = $directions;
    }
}