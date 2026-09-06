<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case BELUM_DIBAYAR = 'belum_dibayar';
    case SUDAH_DIBAYAR = 'sudah_dibayar';
}
