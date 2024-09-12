<?php

namespace App\Contracts\Requests;

use App\Contracts\FillExamMarksContent;

class FillExamMarksRequest
{
    public int $abiturient_id;

    public string $token;

    public FillExamMarksContent $content;

    function __construct($abiturient_id, $token, $content) {
        $this->abiturient_id = $abiturient_id;
        $this->token = $token;
        $this->content = $content;
      }
}