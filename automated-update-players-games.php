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

$simpleHttpClient = new SimpleHttpClient();
$urlProvider = new UrlProvider();

$htmlProvider = new HtmlProvider($simpleHttpClient, $urlProvider);

$gameRowToGameConverter = new GameRowToGameConverter();
$tableParser = new TableParser($gameRowToGameConverter);
$scoresHtmlParser = new HtmlParser($tableParser);

$scoresProvider = new ScoresProvider($htmlProvider, $scoresHtmlParser);
$cronHelper = new CronHelper();

$XScoreGameToTableRowConverter = new XScoreGameToTableRowConverter();
$playerTableGamesUpdater = new PlayerTableGamesUpdater();
$playersGamesUpdater = new PlayersGamesUpdater(
    $scoresProvider,
    $XScoreGameToTableRowConverter,
    $playerTableGamesUpdater
);

$plugin = new Plugin($cronHelper, $playersGamesUpdater);
$plugin->run(__FILE__);
