<?php

declare(strict_types=1);

namespace CookieBox;

include_once 'UnixTime/UnixTime.php';

use UnixTime\UnixTime;

interface CookieBoxInterface
{
    public function cookies(bool $echo = false): array;

    public function create(string $name, string $value = "", int $expire = 0,
        string $path = "", string $domain = "", bool $secure = false,
        bool $httponly = false
    ): bool;
}

class CookieBox implements CookieBoxInterface
{

    public function cookies(bool $echo = false): array
    {
        if ($echo) {
            if('array' === gettype($_COOKIE)){
                print_r($_COOKIE);
            }else{
                echo 'No cookies';
            }
        }

        return $_COOKIE;
    }

    public function create(string $name, string $value = "", int $expire = 0,
        string $path = "", string $domain = "", bool $secure = false,
        bool $httponly = false
    ): bool {
        setcookie(
            $name, $value, $expire, $path, $domain, $secure, $httponly
        );

        if (isset($_COOKIE[$name])) {
            return true;
        }

        return false;
    }
}