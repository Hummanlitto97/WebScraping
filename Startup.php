<?php 

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "./vendor/autoload.php";

use WebScraping\Infrastructure\Databases\SQL\MySQL;
use WebScraping\Infrastructure\Errors\ErrorCodes;
use WebScraping\App\Services;

function Read_Settings(?array &$settings) : void
{
    echo "Reading settings file...\n";
    $settings = parse_ini_file("Settings.ini", true);
    echo "Settings read.\n";
    if(!$settings) Services\ErrorService::Report_Error("Settings.ini Error: ", ErrorCodes::INI_FILE_FAILED, ". Check file content", null);
}

function Connect_To_Database(?\mysqli &$databaseConnection) : void
{
    $database = (new MySQL(SQL["host"], SQL["username"], SQL["password"], SQL["title"], SQL["charset"]));
    $database->Get_Error_Code() > -1 ? 
    Services\ErrorService::Report_Error("MySQL Error: ", $database->Get_Error_Code(), ". Check MySQL server", null) 
    : $databaseConnection = $database->Get_Access_Point();
}

function Ready_Services(?Services\ArticlesService &$articlesService, \mysqli $databaseConnection, string $websiteURL, string &$websiteID) : void
{
    echo "Checking Services\n";
    $websitesService = new Services\WebsitesService($databaseConnection);
    $articlesService = new Services\ArticlesService($websitesService, $databaseConnection);
    if($articlesService->Is_Error()) Services\ErrorService::Report_Error("Startup.php: ",
                                                                        ErrorCodes::SERVICES_LOAD_FAILED,
                                                                        ". Tables were not succesfully created. Check DB user permissions",
                                                                        null);
    !($websiteID = $websitesService->Get_Website($websiteURL)) && $websiteID = $websitesService->Insert_New_Website($websiteURL);
    if(!$websiteID) Services\ErrorService::Report_Error("Website Service: ", 
                                                        ErrorCodes::SERVICE_FAILED, ". Related to inserting new URL", null);
    echo "Services are ready\n";
}

/**
 * Keeps all Settings.ini file content
 * @var ?array $settings Keeps all Settings.ini file content
 */
$settings = null;
/**
 * Database Connection
 * @var \?mysqli $databaseConnection
 */
$databaseConnection = null;
/**
 * @var Services\?ArticlesService $articlesService
 */
$articlesService = null;

$websiteID = "";
$websiteURL = "www.15min.lt";
/*--- Reading file ---*/
Read_Settings($settings);
/*-----Getting database connection-------*/
define("SQL", $settings["Database_SQL"]);
define("SCRAPING", $settings["Scraping"]);
Connect_To_Database($databaseConnection);
//Checks if database connection was successful
if(!$databaseConnection->connect_errno)
{
    Ready_Services($articlesService, $databaseConnection, $websiteURL, $websiteID);
    $articlesService->Fetch_Articles_From_URL($websiteURL, $websiteID, intval(SCRAPING['limit']), (bool)SCRAPING['robots']);
}
else
{
    Services\ErrorService::Report_Error("Main.php: ", ErrorCodes::SERVICES_LOAD_FAILED, ". More details: ".$databaseConnection->connect_errno, 
    function () use ($settings)
    {
        var_dump($settings);
    });
}