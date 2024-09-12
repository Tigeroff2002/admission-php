<?php

namespace App\Contracts;

class AbiturientEmptyMark
{
    public string $abiturient_id;

    public int $abiturient_name;

    public int $mark;

    function __construct($abiturient_id, $abiturient_name, $mark) 
    {
        $this->abiturient_id = $abiturient_id;
        $this->abiturient_name = $abiturient_name;
        $this->mark = $mark;
    }
}