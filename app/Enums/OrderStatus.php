<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MENUNGGU = 'menunggu';
    case DIPROSES = 'diproses';
    case SIAP = 'siap';
    case DIANTAR = 'diantar';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU => 'Menunggu Konfirmasi',
            self::DIPROSES => 'Sedang Diproses',
            self::SIAP => 'Siap',
            self::DIANTAR => 'Sedang Diantar',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }
}
