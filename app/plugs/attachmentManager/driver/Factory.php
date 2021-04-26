<?php


namespace app\plugs\attachmentManager\driver;


class Factory
{
    public static function init($driver_type):? AbstractDriver
    {
        switch ((int) $driver_type){
            case 0:
                return new FileDrvier;
        }

        return null;
    }
}