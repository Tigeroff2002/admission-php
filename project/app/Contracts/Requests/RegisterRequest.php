<?php

namespace App\Contracts\Requests;

use Symfony\Component\Serializer\Annotation\Groups;


class RegisterDto
{
    public $email;

    public $password;

    public $first_name;

    public $second_name;

    public $is_admin;
}