<?php

namespace App\Contracts;

class AbiturientEmptyMark
{
    public int $abiturient_id;

    public string $abiturient_name;

    public int $mark;

    function __construct($abiturient_id, $abiturient_name, $mark) 
    {
        $this->abiturient_id = $abiturient_id;
        $this->abiturient_name = $abiturient_name;
        $this->mark = $mark;
    }
}