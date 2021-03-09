<?php declare(strict_types=1);

namespace WebScraping\Infrastructure\Databases\SQL\Tables;

use WebScraping\Infrastructure\Databases\SQL\Inherits;

/**
 * MySQL Articles table controller
 */
class SQLArticles extends Inherits\SQLTableController
{
    /**
     * Strict table identification (Hardcoded)
     * @var string
     */
    public const ID = "articles";
    /**
     * Creates SQLArticles object and runs private Init()
     * @param \mysqli $connection Connection to DB
     */
    public function __construct(\mysqli $connection)
    {
        parent::__construct($connection);
    }
    protected function Init() : bool
    {
        return $this->Query("
                            CREATE TABLE IF NOT EXISTS ".$this::ID.
                            "( 
                                ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                Website INT UNSIGNED NOT NULL,
                                URL VARCHAR(2083) NOT NULL UNIQUE,
                                PRIMARY KEY(ID),
                                INDEX Website_Index (Website),
                                CONSTRAINT FK_Website FOREIGN KEY (Website)
                                    REFERENCES websites(ID)
                                )
                                COMMENT = 'Keeps all articles URL\'s from websites'");
    }
    /**
     * Check if article exists
     * @param string $url Article URL
     * @return string
     */
    public function Get(string $url) : string
    {
        $result = $this->Query("SELECT * FROM ".$this::ID. 
                            " WHERE URL='".$url."' LIMIT 1");
        return $result->num_rows ? ($result->fetch_assoc())['ID'] : "";   
    }
    /**
     * Insert new articles to articles table
     * @param array $urls Articles urls
     * @param string $websiteID Website id
     * @return string Insert successful
     */
    public function Insert_Bulk(array &$urls, string $websiteID) : string
    {
        $this->Clear_Table();
        return (string)($this->Query("
                                INSERT INTO ".$this::ID.
                                " (URL, Website) VALUES ".($this->Join_Articles_Array_For_Insert($urls, $websiteID))));
    }
    private function Clear_Table()
    {
        $this->Query("TRUNCATE TABLE ".$this::ID);
    }
    private function Join_Articles_Array_For_Insert(array &$articles, string $websiteID) : string
    {
        $result = "";
        $count = count($articles);
        for($i = 0;$i < $count;$i++)
        {
            $result .= "('".$articles[$i]."','".$websiteID."')";
            if($i == $count - 1)
            {
                return $result;
            }
            $result .= ",";
        }
        return $result;
    }

}