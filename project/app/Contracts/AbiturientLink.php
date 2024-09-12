<?php

namespace App\Contracts;

class AbiturientLink
{
    public string $abiturient_id;

    public int $abiturient_name;

    function __construct($abiturient_id, $abiturient_name) 
    {
        $this->abiturient_id = $abiturient_id;
        $this->abiturient_name = $abiturient_name;
    }
}