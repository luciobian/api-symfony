<?php 

namespace App\Exception;

class InvalidConfirmationTokenException extends \Exception
{
    public function __construct(
        string $message ='',
        int $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct('Confirmation token is invalid.', $code,$previous);
    }
}