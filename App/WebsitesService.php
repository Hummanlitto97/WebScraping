<?php declare(strict_types=1);

namespace WebScraping\App\Services;

use WebScraping\Infrastructure\Databases\SQL\Tables\SQLWebsites;

/**
 * All websites related functionalities
 */
class WebsitesService
{
    /**
     * Websites table controller in MySQL
     * @var SQLWebsites
     */
    private SQLWebsites $websitesTable;
    /**
     * Initializes WebsitesService
     * @param \mysqli $connection Connection object to database
     */
    public function __construct(\mysqli $connection)
    {
        $this->websitesTable = new SQLWebsites($connection);
    }
    /**
     * Inserts new website to websites SQL table
     * @param string $url Website URL
     * @return string ID or empty if not found
     */
    public function Get_Website(string $url) : string
    {
        if($this->Is_Error()) return "";
        return $this->websitesTable->Get($url);
    }
    /**
     * Inserts new website to websites SQL table
     * @param string $url Website URL
     * @return string ID or empty if not found
     */
    public function Insert_New_Website($url) : string
    {
        if($this->Is_Error()) return "";
        return $this->websitesTable->Insert($url);
    }
    public function Is_Error() : bool
    {
        return $this->websitesTable->Is_Error();
    }
}