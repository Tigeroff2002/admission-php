<?php

namespace App\Contracts;

use App\Contracts\Content;
use App\Contracts\DirectionLinksList;

class UserLkContent extends Content
{
    public string $first_name;

    public string $second_name;

    public string $email;

    public bool $has_diplom_original;

    public array $directions_links;

    public function __construct($first_name, $second_name, $email, $has_diplom_original, $directions_links)
    {
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->email = $email;
        $this->has_diplom_original = $has_diplom_original;
        $this->directions_links = $directions_links;
    }
}