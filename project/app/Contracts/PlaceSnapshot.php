<?php

namespace App\Contracts;

class PlaceSnapshot
{
    public int $place;

    public string $abiturient_id;

    public int $abiturient_name;

    public int $mark;

    public string $admission_status;

    public int $priotitet_number;

    public bool $has_diplom_original;

    function __construct($place, $abiturient_id, $abiturient_name, $mark, $admission_status, $priotitet_number, $has_diplom_original) 
    {
        $this->place = $place;
        $this->abiturient_id = $abiturient_id;
        $this->abiturient_name = $abiturient_name;
        $this->mark = $mark;
        $this->admission_status = $admission_status;
        $this->admission_status = $admission_status;
        $this->prioritet_number = $priotitet_number;
        $this->has_diplom_original = $has_diplom_original;
    }
}