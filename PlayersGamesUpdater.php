<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use DateTime;
use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use TennisScoresGrabber\XScores\Contracts\ScoresProviderInterface;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerTableGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerBaseInfoProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\SourcePlayerSplitter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeConverter\DonorLatinToSiteLatin as DonorLatinToSiteLatinCountryCodeConverter;

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

    /** @var DonorLatinToSiteLatinCountryCodeConverter */
    private $donorLatinToSiteLatinCountryCodeConverter;

    public function __construct(
        ScoresProviderInterface $scoresProvider,
        XScoreGameToTableRowConverter $XScoreGameToTableRowConverter,
        PlayerTableGamesUpdater $playerGamesUpdater,
        PlayerBaseInfoProvider $playerBaseInfoProvider,
        SourcePlayerSplitter $sourcePlayerSplitter,
        DonorLatinToSiteLatinCountryCodeConverter $donorLatinToSiteLatinCountryCodeConverter
    ) {
        $this->scoresProvider = $scoresProvider;
        $this->XScoreGameToTableRowConverter = $XScoreGameToTableRowConverter;
        $this->playerGamesUpdater = $playerGamesUpdater;
        $this->playerBaseInfoProvider = $playerBaseInfoProvider;
        $this->sourcePlayerSplitter = $sourcePlayerSplitter;
        $this->donorLatinToSiteLatinCountryCodeConverter = $donorLatinToSiteLatinCountryCodeConverter;
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
            try {
                if ($this->isDoubleGame($game)) {
                    continue;
                }

                $this->updatePlayersGamesByGame($game, $dateTime);
            } catch (Exception $exception) {
                error_log($exception->getMessage());
            }
        }
    }

    private function isDoubleGame(GameInterface $game)
    {
        $homePlayerName = $game->getPlayerHome();

        return strpos($homePlayerName, '/') !== false;
    }

    /**
     * @param GameInterface $game
     * @param DateTime $dateTime
     * @throws Exception
     */
    private function updatePlayersGamesByGame(GameInterface $game, DateTime $dateTime)
    {
        $homePlayer = $game->getPlayerHome();
        $homePlayerBaseInfo = $this->getPlayerBaseInfoBySourcePlayer($homePlayer);

        $awayPlayer = $game->getPlayerAway();
        $awayPlayerBaseInfo = $this->getPlayerBaseInfoBySourcePlayer($awayPlayer);

        list($homePlayerTableRowData, $awayPlayerTableRowData) = $this->getPlayersTableData($dateTime, $game, $homePlayerBaseInfo, $awayPlayerBaseInfo);

        if(!$homePlayerTableShortCode = $homePlayerBaseInfo->getTableShortCode()) {
            throw new Exception("No table shortcode for home player: " . $homePlayer);
        }

        $homePlayerTableId = $this->getPlayerTableIdByShortCode($homePlayerTableShortCode);

        if(!$awayPlayerTableShortCode = $homePlayerBaseInfo->getTableShortCode()) {
            throw new Exception("No table shortcode for away player: " . $awayPlayer);
        }

        $awayPlayerTableId = $this->getPlayerTableIdByShortCode($awayPlayerTableShortCode);

        $this->updatePlayerTableIfNecessary($homePlayerTableId, $homePlayerTableRowData);
        $this->updatePlayerTableIfNecessary($awayPlayerTableId, $awayPlayerTableRowData);
    }

    /**
     * @param $tableShortCode
     * @return string
     * @throws Exception
     */
    private function getPlayerTableIdByShortCode($tableShortCode)
    {
        $pattern = '/\[table id=(?<tableId>[\w-]+)/';
        $matches = [];

        if (!preg_match($pattern, $tableShortCode, $matches)) {
            throw new Exception("Invalid table shortcut: " . $tableShortCode);
        }

        return $matches['tableId'];
    }


    /**
     * @param $sourcePlayer
     * @return PlayerBaseInfo
     * @throws Exception
     */
    private function getPlayerBaseInfoBySourcePlayer($sourcePlayer)
    {
        list($homePlayerLatinLastName, $homePlayerLatinFirstNameFirstLetters, $homePlayerDonorLatinCountryCode)
            = $this->getLatinPlayerNameParts($sourcePlayer);

        $homePlayerSiteLatinCountryCode = $this->getSiteCountryCodeByDonorCountryCode($homePlayerDonorLatinCountryCode);

        return $this->getPlayerBaseInfo($homePlayerLatinLastName, $homePlayerLatinFirstNameFirstLetters, $homePlayerSiteLatinCountryCode);
    }

    /**
     * @param string $donorCountryCode
     * @return string
     */
    private function getSiteCountryCodeByDonorCountryCode($donorCountryCode)
    {
        return $this->donorLatinToSiteLatinCountryCodeConverter->getSiteLatinByDonorLatin($donorCountryCode);
    }

    private function getLatinPlayerNameParts($sourcePlayer)
    {
        return $this->sourcePlayerSplitter->getLatinPlayerNameParts($sourcePlayer);
    }

    /**
     * @param $latinPlayerLastName
     * @param $latinPlayerFirstNameFirstLetters
     * @param $latinCountryCode
     * @return PlayerBaseInfo
     * @throws Exception
     */
    private function getPlayerBaseInfo($latinPlayerLastName, $latinPlayerFirstNameFirstLetters, $latinCountryCode): PlayerBaseInfo
    {
        return $this->playerBaseInfoProvider->getByLatinPlayerInfo($latinPlayerLastName, $latinPlayerFirstNameFirstLetters, $latinCountryCode);
    }

    private function getPlayersTableData(
        DateTime $dateTime,
        GameInterface $game,
        PlayerBaseInfo $homePlayerBaseInfo,
        PlayerBaseInfo $awayPlayerBaseInfo
    ) {
        return $this->XScoreGameToTableRowConverter->convert($dateTime, $game, $homePlayerBaseInfo, $awayPlayerBaseInfo);
    }

    private function updatePlayerTableIfNecessary(string $homePlayerTableId, array $homePlayerTableRowData)
    {
        $this->playerGamesUpdater->updateGamesIfNecessary($homePlayerTableId, $homePlayerTableRowData);
    }
}
