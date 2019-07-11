<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use DateTime;
use TennisScoresGrabber\XScores\Contracts\ScoresProviderInterface;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerTableGamesUpdater;

class PlayersGamesUpdater
{
    /** @var ScoresProviderInterface */
    private $scoresProvider;

    /** @var XScoreGameToTableRowConverter */
    private $XScoreGameToTableRowConverter;

    /** @var PlayerTableGamesUpdater */
    private $playerGamesUpdater;

    public function __construct(
        ScoresProviderInterface $scoresProvider,
        XScoreGameToTableRowConverter $XScoreGameToTableRowConverter,
        PlayerTableGamesUpdater $playerGamesUpdater
    ) {
        $this->scoresProvider = $scoresProvider;
        $this->XScoreGameToTableRowConverter = $XScoreGameToTableRowConverter;
        $this->playerGamesUpdater = $playerGamesUpdater;
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
        list($homePlayerTableData, $awayPlayerTableData) = $this->getPlayersTableData($game, $dateTime);

        $this->updatePlayerTableIfNecessary($game->getPlayerHome(), $homePlayerTableData);
        $this->updatePlayerTableIfNecessary($game->getPlayerAway(), $awayPlayerTableData);
    }

    private function getPlayersTableData(GameInterface $game, DateTime $dateTime)
    {
        return $this->XScoreGameToTableRowConverter->convert($game, $dateTime);
    }

    private function updatePlayerTableIfNecessary(string $XScorePlayerName, array $tableData)
    {
        $this->playerGamesUpdater->updateGamesIfNecessary($XScorePlayerName, $tableData);
    }
}
