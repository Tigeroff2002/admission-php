<?php

namespace App\Contracts;

class DirectionLink
{
    public int $direction_id;

    public string $direction_caption;

    public int $place;

    public int $mark;

    public string $admission_status;

    public int $priotitet_number;

    function __construct($direction_id, $direction_caption, $place, $mark, $admission_status, $priotitet_number) {
        $this->direction_id = $direction_id;
        $this->direction_caption = $direction_caption;
        $this->place = $place;
        $this->mark = $mark;
        $this->admission_status = $admission_status;
        $this->priotitet_number = $priotitet_number;
      }
}