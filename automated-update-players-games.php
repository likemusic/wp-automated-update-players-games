<?php
/**
 * Automated Update Players Games
 *
 * @package     PluginPackage
 * @author      Valerij Ivashchenko
 *
 * @wordpress-plugin
 * Plugin Name: Automated Update Players Games
 * Plugin URI: https://github.com/likemusic/wp-automated-update-players-games
 * Description: Automated update players games by https://xscores.com/tennis/livescores.
 * Version: 0.0.1
 * Author: Valerij Ivashchenko
 * Author URI: https://github.com/likemusic
 */

namespace Likemusic\AutomatedUpdatePlayersGames;

require __DIR__ . '/vendor/autoload.php';

use Likemusic\AutomatedUpdatePlayersGames\Controller\AdminPage as AdminPageController;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeConverter\DonorLatinToSiteCyrillic as DonorLatinToSiteCyrillicCountryCodeConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeConverter\DonorLatinToSiteLatin as DonorLatinToSiteLatinCountryCodeConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron as CronService;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron\Manager as CronManager;
use Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable\Creator as PlayerGameTableCreator;
use Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable\Updater as PlayerTableGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Hooks as HooksService;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerBaseInfoProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\SourcePlayerSplitter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\TablePress as TablePressHelper;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\SimpleHttpClient\FileGetContents\SimpleHttpClient;
use TablePress_Table_Model;
use TennisScoresGrabber\XScores\Helper\GameRowToGameConverter;
use TennisScoresGrabber\XScores\HtmlParser;
use TennisScoresGrabber\XScores\HtmlProvider;
use TennisScoresGrabber\XScores\ScoresProvider;
use TennisScoresGrabber\XScores\TableParser;
use TennisScoresGrabber\XScores\UrlProvider;

$simpleHttpClient = new SimpleHttpClient();
$urlProvider = new UrlProvider();

$htmlProvider = new HtmlProvider($simpleHttpClient, $urlProvider);

$gameRowToGameConverter = new GameRowToGameConverter();
$tableParser = new TableParser($gameRowToGameConverter);
$scoresHtmlParser = new HtmlParser($tableParser);

$scoresProvider = new ScoresProvider($htmlProvider, $scoresHtmlParser);

$donorLatinToSiteCyrillicCountryCodeConverter = new DonorLatinToSiteCyrillicCountryCodeConverter();
$XScoreGameToTableRowConverter = new XScoreGameToTableRowConverter($donorLatinToSiteCyrillicCountryCodeConverter);

$tablePressModel = new TablePress_Table_Model();
$tablePressHelper = new TablePressHelper($tablePressModel);
$playerTableGamesUpdater = new PlayerTableGamesUpdater($tablePressHelper);
$playerBaseInfoProvider = new PlayerBaseInfoProvider();
$sourcePlayerSplitter = new SourcePlayerSplitter();
$donorLatinToSiteLatinCountryCodeConverter = new DonorLatinToSiteLatinCountryCodeConverter();
$playerTableGamesCreator = new PlayerGameTableCreator($tablePressHelper);

$playersGamesUpdater = new PlayersGamesUpdater(
    $scoresProvider,
    $XScoreGameToTableRowConverter,
    $playerTableGamesUpdater,
    $playerBaseInfoProvider,
    $sourcePlayerSplitter,
    $donorLatinToSiteLatinCountryCodeConverter,
    $playerTableGamesCreator
);

$cronManager = new CronManager();
$cronService = new CronService($cronManager);
$hooksService = new HooksService($playersGamesUpdater);
$adminPageController = new AdminPageController($playersGamesUpdater);
$plugin = new Plugin($cronService, $hooksService, $adminPageController);
$plugin->run(__FILE__);
