<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use DateTime;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use TennisScoresGrabber\XScores\Contracts\ScoresProviderInterface;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerTableGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerBaseInfoProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\SourcePlayerSplitter;

class PlayersGamesUpdater
{
    /** @var ScoresProviderInterface */
    private $scoresProvider;

    /** @var XScoreGameToTableRowConverter */
    private $XScoreGameToTableRowConverter;

    /** @var PlayerTableGamesUpdater */
    private $playerGamesUpdater;

    /** @var PlayerBaseInfoProvider */
    private $playerBaseInfoProvider;

    /** @var SourcePlayerSplitter */
    private $sourcePlayerSplitter;

    public function __construct(
        ScoresProviderInterface $scoresProvider,
        XScoreGameToTableRowConverter $XScoreGameToTableRowConverter,
        PlayerTableGamesUpdater $playerGamesUpdater,
        PlayerBaseInfoProvider $playerBaseInfoProvider,
        SourcePlayerSplitter $sourcePlayerSplitter
    ) {
        $this->scoresProvider = $scoresProvider;
        $this->XScoreGameToTableRowConverter = $XScoreGameToTableRowConverter;
        $this->playerGamesUpdater = $playerGamesUpdater;
        $this->playerBaseInfoProvider = $playerBaseInfoProvider;
        $this->sourcePlayerSplitter = $sourcePlayerSplitter;
    }

    public function update()
    {
        $dateTime = new DateTime();
        $games = $this->getScoresData($dateTime);
        $this->updatePlayersGames($games, $dateTime);
    }

    private function getScoresData(DateTime $dateTime)
    {
        return $this->scoresProvider->getScoresForDate($dateTime);
    }

    /**
     * @param GameInterface[] $games
     * @param DateTime $dateTime
     */
    private function updatePlayersGames($games, DateTime $dateTime)
    {
        foreach ($games as $game) {
            if ($this->isDoubleGame($game)) {
                continue;
            }

            $this->updatePlayersGamesByGame($game, $dateTime);
        }
    }

    private function isDoubleGame(GameInterface $game)
    {
        $homePlayerName = $game->getPlayerHome();

        return strpos($homePlayerName, '/') !== false;
    }

    private function updatePlayersGamesByGame(GameInterface $game, DateTime $dateTime)
    {
        $homePlayer = $game->getPlayerHome();
        $homePlayerBaseInfo = $this->getPlayerBaseInfoBySourcePlayer($homePlayer);

        $awayPlayer = $game->getPlayerAway();
        $awayPlayerBaseInfo = $this->getPlayerBaseInfoBySourcePlayer($awayPlayer);

        list($homePlayerTableData, $awayPlayerTableData) = $this->getPlayersTableData($dateTime, $game, $homePlayerBaseInfo, $awayPlayerBaseInfo);

        $this->updatePlayerTableIfNecessary($homePlayerBaseInfo, $homePlayerTableData);
        $this->updatePlayerTableIfNecessary($awayPlayerBaseInfo, $awayPlayerTableData);
    }

    private function getPlayerBaseInfoBySourcePlayer($sourcePlayer)
    {
        list($homePlayerLatinLastName, $homePlayerLatinFirstNameFirstLetter, $homePlayerLatinCountryCode) = $this->getLatinPlayerNameParts($sourcePlayer);

        return $this->getPlayerBaseInfo($homePlayerLatinLastName, $homePlayerLatinCountryCode);
    }

    private function getLatinPlayerNameParts($sourcePlayer)
    {
        return $this->sourcePlayerSplitter->getLatinPlayerNameParts($sourcePlayer);
    }

    private function getPlayerBaseInfo($latinPlayerName, $latinCountryCode): PlayerBaseInfo
    {
        return $this->playerBaseInfoProvider->getByLatinPlayerInfo($latinPlayerName, $latinCountryCode);
    }

    private function getPlayersTableData(
        DateTime $dateTime,
        GameInterface $game,
        PlayerBaseInfo $homePlayerBaseInfo,
        PlayerBaseInfo $awayPlayerBaseInfo
    ) {
        return $this->XScoreGameToTableRowConverter->convert($dateTime, $game, $homePlayerBaseInfo, $awayPlayerBaseInfo);
    }

    private function updatePlayerTableIfNecessary(string $XScorePlayerName, array $tableData)
    {
        $this->playerGamesUpdater->updateGamesIfNecessary($XScorePlayerName, $tableData);
    }
}
