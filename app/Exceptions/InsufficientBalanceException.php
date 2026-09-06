<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(string $message = 'Saldo Anda tidak mencukupi untuk pembayaran ini.')
    {
        parent::__construct($message);
    }
}
