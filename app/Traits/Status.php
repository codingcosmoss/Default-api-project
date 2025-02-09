<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait Status
{

    /**
     * Holati nofaol hiosblanadi
     * @var int
     */
    public static int $status_inactive = 0;

    /**
     * Holati faol
     * @var int
     */
    public static int $status_active = 1;



    /**
     * @return string[]
     */
    public static function statuses()
    {
        return [
            self::$status_active => 'Faol',
            self::$status_inactive => 'Nofaol',
        ];
    }

    /**
     * @return array|\ArrayAccess|mixed|string
     */
    public static function getStatusName($status)
    {
        return Arr::get(self::statuses(), $status);
    }

  
    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->status == self::$status_active ? 'checked' : '';
    }
}
