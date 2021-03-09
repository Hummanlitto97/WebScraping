<?php declare(strict_types=1);

namespace WebScraping\Infrastructure\Databases\SQL\Tables;

use WebScraping\Infrastructure\Databases\SQL\Inherits;

/**
 * MySQL Websites table controller
 * @var ID Strict table identification (Hardcoded)
 * @property  $connection Connection to database
 * @property  $error 
 */
class SQLWebsites extends Inherits\SQLTableController
{
    /**
     * Strict table identification (Hardcoded)
     * @var string
     */
    public const ID = "websites";

    function __construct($connection)
    {
        $this->connection = $connection;
        $this->error = !($this->Init());
    }
    protected function Init() : bool
    {
        return $this->Query("
                            CREATE TABLE IF NOT EXISTS ".$this::ID.
                            "( 
                                ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                URL VARCHAR(2083) NOT NULL UNIQUE,
                                PRIMARY KEY(ID)
                             )
                             COMMENT = 'Keeps all websites urls which are being monitored'");
    }
    /**
     * Check if website exist in table
     * @param string $url Website URL
     * @return string
     */
    public function Get(string $url) : string
    {
        $result = $this->Query("SELECT * FROM ".$this::ID. 
                            " WHERE URL='".$url."' LIMIT 1");
        return $result->num_rows ? ($result->fetch_assoc())['ID'] : "";   
    }
    /**
     * Insert new URL to websites table
     * @param string $url Website URL
     * @return int
     */
    public function Insert(string $url) : string
    {
        return $this->Query("
                                INSERT INTO ".$this::ID.
                                " (URL)
                                VALUES 
                                ('".$url."')") ? (string)($this->connection->insert_id) : "";
    }
}