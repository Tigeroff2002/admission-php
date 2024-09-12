<?php

namespace App\Contracts\Requests;

class RegisterRequest
{
    public string $email;

    public string $password;

    public string $first_name;

    public string $second_name;

    public boolean $is_admin;

    function __construct($email, $password, $first_name, $second_name, $is_admin) {
        $this->email = $email;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->is_admin = $is_admin;
      }
}