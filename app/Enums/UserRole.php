<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case KURIR = 'kurir';
    case SISWA = 'siswa';
}
