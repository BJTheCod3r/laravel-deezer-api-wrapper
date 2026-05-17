<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Exceptions;

/**
 * Thrown when Deezer reports a quota error (the API uses error codes 4 and
 * "ITEMS_LIMIT_EXCEEDED_EXCEPTION" for short-window rate limiting — roughly
 * 50 requests per 5 seconds per IP).
 */
class QuotaException extends DeezerException
{
}
