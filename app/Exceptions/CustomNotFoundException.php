<?php

namespace App\Exceptions;

class CustomNotFoundException extends CustomException
{
    public function __construct($message = 'not found')
    {
        parent::__construct($message, 404, 'not found');
    }
}
