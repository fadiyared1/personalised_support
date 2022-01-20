<?php

class Localisation
{
    static function get($str)
    {
        return __($str, 'personalized-support');
    }
}
