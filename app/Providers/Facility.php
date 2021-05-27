<?php

namespace App\Providers;
class Facility
{
    public static $MODEL_NOT_FOUND = '-1';

    public function __construct() { }

    public function getModelNotFound(){
        return self::$MODEL_NOT_FOUND;
    }
}