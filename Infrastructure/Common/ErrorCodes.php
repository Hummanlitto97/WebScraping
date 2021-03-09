<?php declare(strict_types=1);

namespace WebScraping\Infrastructure\Errors;

/**
 * All defined software error codes
 */
class ErrorCodes
{
    public const DATABASE_CONNECTION_FAILED = 0;
    public const TABLES_READY_FAILED = 1;
    public const SERVICES_LOAD_FAILED = 2;
    public const SERVICE_FAILED = 3;
    public const DATABASE_SETUP_FAILED = 4;
    public const INI_FILE_FAILED = 5;
    /**
     * Translates error code to human readable text
     * @param int $errorCode Error code
     * @return string
     */
    public static function Translate(int $errorCode) : string
    {
        switch($errorCode)
        {
            case self::DATABASE_CONNECTION_FAILED:
                return "Unable to connect to database";
            case self::DATABASE_SETUP_FAILED:
                return "Unable to connect to MySQL server";
            case self::TABLES_READY_FAILED:
                return "Couldn't find and create needed tables";
            case self::SERVICES_LOAD_FAILED:
                return "Services failed";
            case self::SERVICE_FAILED:
                return "Service failed";
            case self::INI_FILE_FAILED:
                return "Reading ini failed";
            default:
                return "Unknown Error";
        }
    }
}