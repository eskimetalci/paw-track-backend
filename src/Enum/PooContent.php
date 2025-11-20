<?php

namespace App\Enum;

enum PooContent: string
{
    case NORMAL = 'NORMAL';
    case MUCUS = 'MUCUS';
    case BLOOD = 'BLOOD';
    case WORMS = 'WORMS';
    case FOREIGN_OBJECT = 'FOREIGN_OBJECT';
}

