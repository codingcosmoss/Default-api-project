<?php

namespace App\Traits;

class Action
{

    public static function decimal($summa, $decimal = 0, $unit = 'UZS'): string
    {
        return number_format($summa, $decimal, '.', ' ');
    }

}
