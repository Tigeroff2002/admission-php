<?php

namespace App\Contracts;

class DirectionPrioritetLink
{
    public int $direction_id;

    public int $mark;

    public int $priotitet_number;

    function __construct($direction_id, $mark, $priotitet_number) {
        $this->direction_id = $direction_id;
        $this->mark = $mark;
        $this->priotitet_number = $priotitet_number;
      }
}