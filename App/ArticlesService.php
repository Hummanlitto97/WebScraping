<?php declare(strict_types=1);

namespace WebScraping\App\Services;

use WebScraping\Infrastructure\Databases\SQL\Tables\SQLArticles;
use WebScraping\App\Services\Interfaces;

/**
 * All articles related functionalities
 */
class ArticlesService implements Interfaces\IService
{
    /**
     * Articles table controller in MySQL
     * @var ?SQLArticles
     */
    private ?SQLArticles $ArticlesTable = null;
    /**
     * Initializes ArticleService object and fails if WebsiteService is not available
     * @param WebsiteService $WebsitesService Articles table control in MySQL
     * @param \mysqli $connection Connection to DB
     */
    public function __construct(
        private WebsitesService $WebsitesService,
        \mysqli $connection)
    {
        if(!$this->WebsitesService->Is_Error())
        {
            $this->ArticlesTable = new SQLArticles($connection);
        }
    }
    private function Try_Find_Robots(string $url, array &$articles, int $limit)
    {
        $this->Try_Find_Articles_XML($url."/robots.txt", $articles, $limit);
        return true;
    }
    private function Try_Find_Articles_XML(string $url, array &$articles, int $limit)
    {
        if($limit && count($articles) >= $limit) return true;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36");
        $xml = curl_exec($curl);
        curl_close($curl);
        $links = null;
        preg_match_all('/https:.*articles.*\.xml/', $xml, $links);
        if($links && $links[0])
        {
            foreach($links[0] as $otherXML)
            {
                $this->Try_Find_Articles_XML($otherXML, $articles, $limit);
            }
            return true;
        }
        $xmlDoc = new \DOMDocument();
        $xmlDoc->preserveWhiteSpace = false;
        $success = $xmlDoc->loadXML($xml);
        libxml_clear_errors();
        if(!$success) return true;
        $XPath = new \DOMXPath($xmlDoc);
        $articlesURL = $XPath->query("/*/*");
        foreach($articlesURL as $article)
        {
            if($limit && count($articles) >= $limit) return true;
            $articleURL = ($article->firstChild)->nodeValue;
            echo $articleURL."\n";
            !in_array($articleURL, $articles) && array_push($articles, ($article->firstChild)->nodeValue);
        }
        return true;

    }
    private function Look_For_Articles(string $url, array &$list, array &$articles, string $baseURL, int $limit, bool $tryRobots)
    {
        if($tryRobots && $this->Try_Find_Robots($url, $articles, $limit)) return true;;
        if($limit && count($articles) >= $limit) return true;
        if(in_array($url, $list)) return true;
        echo $url."\n";
        array_push($list, $url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36");
        $page = curl_exec($curl);
        if(!empty($curl))
        {
            $urlDoc = new \DOMDocument();
            $urlDoc->preserveWhiteSpace = false;
            if(!$page) return true;
            $urlDoc->loadHTML($page);
            var_dump($urlDoc);
            return $urlDoc;
            curl_close($curl);
            libxml_clear_errors();
            $urlXPath = new \DOMXPath($urlDoc);
            $relativeHrefs = $urlXPath->query("//a[starts-with(@href, '/')]");
            $absoluteHrefs = $urlXPath->query("//a[starts-with(@href, '".$baseURL."')]");
            $ogType = $urlXPath->query("/html/head/meta[@property='og:type']");
            if($ogType->length && $ogType[0]->getAttribute("content") == "article")
            {
                array_push($articles, $url);
                return true;
            }
            if($relativeHrefs !== false)
            {
                foreach($relativeHrefs as $relativeHref)
                {
                    $this->Look_For_Articles($baseURL.$relativeHref->getAttribute("href"), $list, $articles, $baseURL, $limit, $tryRobots);
                }
            }
            if($absoluteHrefs !== false)
            {
                foreach($absoluteHrefs as $absoluteHref)
                {
                    $this->Look_For_Articles(($absoluteHref->getAttribute("href")), $list, $articles, $baseURL, $limit, $tryRobots);
                }
            }
        }
        return true;
    }
    /**
     * Fetches Data from provided URL
     * @param string $url Website url
     * @return bool Data fetching successful
     */
    public function Fetch_Articles_From_URL(string $url, string $websiteID, int $limit, bool $tryRobots) : bool
    {
        if($this->Is_Error()) return false;
        libxml_use_internal_errors(true);
        $list = array();
        $articles = array();
        $this->Look_For_Articles("https://".$url, $list, $articles, "https://".$url, $limit, $tryRobots);
        $this->Insert_New_Articles($websiteID, $articles);
        return true;
    }
    /**
     * Insert new article into articles SQL Table
     * @param string $url Article url
     * @param string $websiteID Website id
     * @param array $articles
     * @return string ID of inserted or already existing article
     */
    private function Insert_New_Articles(string $websiteID, array &$articles) : string
    {
        return $this->ArticlesTable->Insert_Bulk($articles, $websiteID, $articles);
    }
    public function Is_Error(): bool
    {
        return $this->WebsitesService->Is_Error() && $this->ArticlesTable && $this->ArticlesTable->Is_Error();
    }
}