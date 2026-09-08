<?php

declare(strict_types=1);

namespace Slideshowck\Detection\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class CacheInvalidArgumentException extends CacheException implements InvalidArgumentException
{
}
