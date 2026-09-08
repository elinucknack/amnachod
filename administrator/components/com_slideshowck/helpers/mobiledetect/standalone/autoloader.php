<?php
$dir = dirname(__FILE__);

spl_autoload_register(function ($class) use ($dir) {
    $classMap = [
        // "mobiledetect/mobiledetectlib"
        "Slideshowck\Detection\Cache\Cache" => $dir . "/../src/Cache/Cache.php",
        "Slideshowck\Detection\Cache\CacheException" => $dir . "/../src/Cache/CacheException.php",
        "Slideshowck\Detection\Cache\CacheInvalidArgumentException" => $dir . "/../src/Cache/CacheInvalidArgumentException.php",
        "Slideshowck\Detection\Exception\MobileDetectException" => $dir . "/../src/Exception/MobileDetectException.php",
        "Slideshowck\Detection\Exception\MobileDetectExceptionCode" => $dir . "/../src/Exception/MobileDetectExceptionCode.php",
        "Slideshowck\Detection\MobileDetect" => $dir . "/../src/MobileDetect.php",

        // "psr/simple-cache"
        "Psr\SimpleCache\CacheException" => $dir . "/deps/simple-cache/src/CacheException.php",
        "Psr\SimpleCache\CacheInterface" => $dir . "/deps/simple-cache/src/CacheInterface.php",
        "Psr\SimpleCache\InvalidArgumentException" => $dir . "/deps/simple-cache/src/InvalidArgumentException.php",
    ];

    $fileFound = $classMap[$class] ?? false;

    if ($fileFound) {
        require $fileFound;
        return true;
    }

    return false;
});
