<?php

namespace App\Contracts\Requests;

class LoginRequest
{
    public string $email;

    public string $password;

    function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
      }
}