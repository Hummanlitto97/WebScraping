<?php declare(strict_types=1);

namespace WebScraping\App\Services;

use WebScraping\Infrastructure\Errors\ErrorCodes;

class ErrorService
{
    public static function Report_Error(string $initiator, int $errorCode, string $postError, ?callable $callback)
    {
        echo $initiator.ErrorCodes::Translate($errorCode).$postError;
        $callback && $callback();
        exit($errorCode);
    }
}