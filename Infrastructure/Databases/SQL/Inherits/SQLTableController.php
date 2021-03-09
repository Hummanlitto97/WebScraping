<?php declare(strict_types=1);

namespace WebScraping\Infrastructure\Databases\SQL\Inherits;

class SQLTableController
{
    /**
     * Strict table identification (Hardcoded)
     * @var string
     */
    public const ID = "undefined";
    /**
     * Connection to database
     * @var \mysqli
     */
    protected \mysqli $connection;
    /**
     * Table controller status (Indicates if it is working)
     * @var bool
     */
    protected bool $error = true;
    /**
     * Creates Table Controller object and runs protected Init()
     * @param \mysqli $connection Connection to DB
     */
    function __construct($connection)
    {
        $this->connection = $connection;
        $this->error = !($this->Init());
    }
    /**
     * Query to DB connection
     * @param string $query Query for SQL
     * @return mixed
     */
    protected function Query(string $query) : mixed
    {
        return $this->connection->query($query);
    }
    /**
     * SQL Table needed initializations
     * @return bool MySQL Table setup success
     */
    protected function Init() : bool
    {
        return true;
    }
    /**
     * Check if SQL Controller is available
     */
    public function Is_Error() : bool
    {
        return $this->error;
    }
}