<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * Clean query strings and protocols from urls
 * Returns only hostname
 */

function cleanReferral($url)
{
    if ($url === null || $url === '') {
        return '';
    }
    $host = parse_url((string) $url, PHP_URL_HOST);
    if ($host === null || $host === false || $host === '') {
        return (string) $url;
    }
    $urlClean = implode('.', array_slice(explode('.', $host), -2));
    if ($urlClean == null || $urlClean === '') {
        return (string) $url;
    }
    return $urlClean;
}
