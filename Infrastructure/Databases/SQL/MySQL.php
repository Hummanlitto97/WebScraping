<?php declare(strict_types=1);

namespace WebScraping\Infrastructure\Databases\SQL;

use WebScraping\Infrastructure\Errors\ErrorCodes;
/**
 * Object to setup MySQL database Connection
 */
class MySQL
{
    /**
     * Connection to MySQL database
     * @var \mysqli
     */
    private \mysqli $Connection;
    /**
     * Error code if any
     * @var int
     */
    private int $ErrorCode = -1;
    /**
     * Initializes MySQL object
     * @param string $host DB server host
     * @param string $username DB user username
     * @param string $password DB user password
     * @param string $dbname DB to connecto to
     * @param string $charset Charater set when saving in DB
     */
    public function __construct(string $host, string $username, string $password, string $dbname, string $charset)
    {
        try
        {
            $this->Connection = new \mysqli($host, $username, $password, $dbname);
            $this->Connection->connect_errno && $this->Connection->set_charset($charset);
        }
        catch(\Exception $error)
        {
            $this->ErrorCode = ErrorCodes::DATABASE_SETUP_FAILED;
        }
    }
    /**
     * Getter for Connection objecto to MySQL database
     * @return \mysqli
     */
    public function Get_Access_Point() : \mysqli
    {
        return $this->Connection;
    }
    /**
     * Get error
     * @return int
     */
    public function Get_Error_Code() : int
    {
        return $this->ErrorCode;
    }
}