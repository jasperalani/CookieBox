<?php

use CookieBox\CookieBox;
use UnixTime\UnixTime;

include_once 'CookieBox.php';

$cookieBox = new CookieBox();

$expire = new UnixTime('2d');

$cookieBox->create(
    'mycookie', '123', $expire->format()
);

$cookieBox->cookies(true);