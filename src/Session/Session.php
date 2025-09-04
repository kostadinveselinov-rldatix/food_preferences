<?php

namespace App\Session;

abstract class Session
{
    private static function startSession():void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        self::startSession();

        $_SESSION[$key] = $value;
    }

    public static function addToArray($key,$value)
    {
        self::startSession();

        if(!isset($_SESSION[$key]) || !is_array($_SESSION[$key])){
            $_SESSION[$key] = [];
        }

        $_SESSION[$key][] = $value;
    }


    public static function get($key)
    {
        self::startSession();

        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function checkInArray($key,$value):bool
    {
        self::startSession();

        if(isset($_SESSION[$key]) && is_array($_SESSION[$key]))
        {
            return in_array($value, $_SESSION[$key]);
        }

        return false;
    }

    public static function remove($key)
    {
        self::startSession();

        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public static function removeValueFromArray($key,$value):bool
    {
        self::startSession();

        if(isset($_SESSION[$key]) && is_array($_SESSION[$key])){
            $keyIndex = array_search($value, $_SESSION[$key]);
            if($keyIndex !== false){
                unset($_SESSION[$key][$keyIndex]);
                return true;
            }
        }

        return false;
    }

    public static function getCurrentSessionId(): string
    {
        self::startSession();
        return session_id();
    }

}