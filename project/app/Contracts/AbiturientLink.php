<?php

namespace App\Contracts;

class AbiturientLink
{
    public int $abiturient_id;

    public string $abiturient_name;

    public bool $is_requested;

    public bool $is_enrolled;

    function __construct($abiturient_id, $abiturient_name, $is_requested, $is_enrolled) 
    {
        $this->abiturient_id = $abiturient_id;
        $this->abiturient_name = $abiturient_name;
        $this->is_requested = $is_requested;
        $this->is_enrolled = $is_enrolled;
    }
}