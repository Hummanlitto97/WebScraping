# WebScraping
All settings are in Settings.ini.
Created to get articles from 15min.lt

#Settings

##Database
|  Setting  |  Description | Value |
|:---------:|:------------:|:-----:|
| host    | MySQL server host | string |
| username    | MySQL user username | string |
| password   | MySQL user password | string |
| charset  | MySQL connection charset | string |
| title   | MySQL database name/title | string |

##Scraping
|  Setting  |  Description | Value |
|:---------:|:------------:|:-----:|
| limit     | How many articles to get before closing connection| int (0 - All)
| robots    | Look for robots.txt file | bool |
