<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case SALDO = 'saldo';
    case XENDIT = 'xendit';
    case COD = 'cod';
}
