<?php

namespace App\Exceptions;

class CustomDatabaseException extends CustomException
{
    public function __construct($message = 'Database operation failed')
    {
        parent::__construct($message, 500, 'database error');
    }
}
