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

use TennisScoresGrabber\XScores\ScoresProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron as CronHelper;
use TennisScoresGrabber\XScores\HtmlProvider;
use TennisScoresGrabber\XScores\HtmlParser;
use TennisScoresGrabber\XScores\TableParser;
use TennisScoresGrabber\XScores\Helper\GameRowToGameConverter;
use Likemusic\SimpleHttpClient\FileGetContents\SimpleHttpClient;
use TennisScoresGrabber\XScores\UrlProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerTableGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerBaseInfoProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\SourcePlayerSplitter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeLatinToCyrillicConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\TablePress as TablePressHelper;
use TablePress_Table_Model;

$simpleHttpClient = new SimpleHttpClient();
$urlProvider = new UrlProvider();

$htmlProvider = new HtmlProvider($simpleHttpClient, $urlProvider);

$gameRowToGameConverter = new GameRowToGameConverter();
$tableParser = new TableParser($gameRowToGameConverter);
$scoresHtmlParser = new HtmlParser($tableParser);

$scoresProvider = new ScoresProvider($htmlProvider, $scoresHtmlParser);
$cronHelper = new CronHelper();

$countryCodeLatinToCyrillicConverter = new CountryCodeLatinToCyrillicConverter();
$XScoreGameToTableRowConverter = new XScoreGameToTableRowConverter($countryCodeLatinToCyrillicConverter);

$tablePressModel = new TablePress_Table_Model();
$tablePressHelper = new TablePressHelper($tablePressModel);
$playerTableGamesUpdater = new PlayerTableGamesUpdater($tablePressHelper);
$playerBaseInfoProvider = new PlayerBaseInfoProvider();
$sourcePlayerSplitter = new SourcePlayerSplitter();

$playersGamesUpdater = new PlayersGamesUpdater(
    $scoresProvider,
    $XScoreGameToTableRowConverter,
    $playerTableGamesUpdater,
    $playerBaseInfoProvider,
    $sourcePlayerSplitter
);

$plugin = new Plugin($cronHelper, $playersGamesUpdater);
$plugin->run(__FILE__);
