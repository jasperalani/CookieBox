<?php

declare(strict_types=1);

namespace CookieBox;

interface CookieBoxInterface
{
    public function __construct();

    /**
     * Returns an array of cookies.
     * @return array
     */
    public function cookies(): array;

    // TODO: Update create method to use an array of values for options instead of individual arguments
    /**
     * Create a cookie.
     * @param string $name
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return bool
     */
    public function create(string $name, string $value = "", int $expire = 0,
        string $path = "", string $domain = "", bool $secure = false,
        bool $httponly = false
    ): bool;

    /**
     * Get a cookie by name.
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * Edit a cookie.
     * @param string $name
     * @param array  $values
     *
     * @return bool
     */
    public function edit(string $name, array $values): bool;

    /**
     * Remove a cookie.
     * @param string $name
     *
     * @return mixed
     */
    public function remove(string $name);

    /**
     * Remove all cookies.
     */
    public function removeAll();
}

class CookieBox implements CookieBoxInterface
{
    public array $cookies;

    public function __construct()
    {
        $this->cookies = array();
    }

    public function cookies(): array
    {
        $cookies = array();

        if(!empty($this->cookies)){
            $cookies = array_merge($_COOKIE, $this->cookies);
        }

        return $cookies;
    }

    public function create(string $name, string $value = "", int $expire = 0,
        string $path = "", string $domain = "", bool $secure = false,
        bool $httponly = false
    ): bool {
        $this->cookies[$name] = [
            "value"    => $value,
            "expire"   => $expire,
            "path"     => $path,
            "domain"   => $domain,
            "secure"   => $secure,
            "httponly" => $httponly,
        ];

        setcookie(
            $name, $value, $expire, $path, $domain, $secure, $httponly
        );

        if (isset($_COOKIE[$name])) {
            return true;
        }

        return false;
    }

    public function get(string $name)
    {
        if (isset($this->cookies[$name])) {
            return $this->cookies[$name];
        }

        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }

        return '';
    }

    public function edit(string $name, array $values): bool
    {
        if (empty($values)) {
            trigger_error(
                "CookieBox: edit function values argument must not be an empty array"
            );
        }

        if (isset($this->cookies[$name])) {
            $localCookie = $this->cookies[$name];
            foreach ($values as $key => $value) {
                if (array_key_exists($key, $localCookie)) {
                    $localCookie[$key] = $value;
                }
            }
            $this->remove($name);
            $created = $this->create(
                $name, (string) $localCookie["value"], $localCookie["expire"],
                $localCookie["path"], $localCookie["domain"],
                $localCookie["secure"], $localCookie["httponly"]
            );
            if ($created) {
                $this->cookies[$name] = $localCookie;
            }

            return true;
        } elseif(isset($_COOKIE[$name]) && isset($values["value"])) {
            $_COOKIE[$name] = $values["value"];

            return true;
        }

        return false;
    }

    public function remove(string $name)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 1000);
            setcookie($name, '', time() - 1000, '/');
        }
    }

    public function removeAll()
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name  = trim($parts[0]);
                $this->remove($name);
            }
        }
    }

}