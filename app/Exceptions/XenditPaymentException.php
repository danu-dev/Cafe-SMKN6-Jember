<?php

namespace App\Exceptions;

use Exception;

class XenditPaymentException extends Exception
{
    public function __construct(string $message = 'Terjadi kesalahan saat memproses pembayaran Xendit.')
    {
        parent::__construct($message);
    }
}
