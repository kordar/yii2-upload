<?php
namespace kordar\upload\scs;

class ScsHelper
{
    /**
     * @return SCS
     */
    public static function getScsInstance()
    {
        return new SCS( SCS_ACCESS_KEY,  SCS_SECRE_KEY);
    }
}