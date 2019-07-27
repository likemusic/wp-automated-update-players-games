<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use DateTime;
use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\MetaFieldKeyInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeConverter\DonorLatinToSiteLatin as DonorLatinToSiteLatinCountryCodeConverter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable\Creator as PlayerGamesTableCreator;
use Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable\Updater as PlayerTableGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Helper\PlayerBaseInfoProvider;
use Likemusic\AutomatedUpdatePlayersGames\Helper\SourcePlayerSplitter;
use Likemusic\AutomatedUpdatePlayersGames\Helper\XScoreGameToTableRowConverter;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use TennisScoresGrabber\XScores\Contracts\ScoresProviderInterface;

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

    /** @var PlayerGamesTableCreator */
    private $playerGamesTableCreator;

    /**
     * PlayersGamesUpdater constructor.
     * @param ScoresProviderInterface $scoresProvider
     * @param XScoreGameToTableRowConverter $XScoreGameToTableRowConverter
     * @param PlayerTableGamesUpdater $playerGamesUpdater
     * @param PlayerBaseInfoProvider $playerBaseInfoProvider
     * @param SourcePlayerSplitter $sourcePlayerSplitter
     * @param DonorLatinToSiteLatinCountryCodeConverter $donorLatinToSiteLatinCountryCodeConverter
     * @param PlayerGamesTableCreator $playerGamesTableCreator
     */
    public function __construct(
        ScoresProviderInterface $scoresProvider,
        XScoreGameToTableRowConverter $XScoreGameToTableRowConverter,
        PlayerTableGamesUpdater $playerGamesUpdater,
        PlayerBaseInfoProvider $playerBaseInfoProvider,
        SourcePlayerSplitter $sourcePlayerSplitter,
        DonorLatinToSiteLatinCountryCodeConverter $donorLatinToSiteLatinCountryCodeConverter,
        PlayerGamesTableCreator $playerGamesTableCreator
    )
    {
        $this->scoresProvider = $scoresProvider;
        $this->XScoreGameToTableRowConverter = $XScoreGameToTableRowConverter;
        $this->playerGamesUpdater = $playerGamesUpdater;
        $this->playerBaseInfoProvider = $playerBaseInfoProvider;
        $this->sourcePlayerSplitter = $sourcePlayerSplitter;
        $this->donorLatinToSiteLatinCountryCodeConverter = $donorLatinToSiteLatinCountryCodeConverter;
        $this->playerGamesTableCreator = $playerGamesTableCreator;
    }

    /**
     * @throws Exception
     */
    public function updateCurrent()
    {
        $dateTime = $this->getCurrentDateTime();
        $this->updateForDate($dateTime);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function getCurrentDateTime()
    {
        $dateTime = new DateTime();

        return $dateTime;
    }

    public function updateForDate(DateTime $dateTime)
    {
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

                if ($this->isTransferToNextRound($game)) {
                    continue;
                }

                $this->updatePlayersGamesByGame($game, $dateTime);
            } catch (Exception $exception) {
                $dayDateStr = $dateTime->format('Y-m-d');
                $exceptionMessage = $exception->getMessage();
                error_log( "{$dayDateStr} {$exceptionMessage}");
            }
        }
    }

    private function isTransferToNextRound(GameInterface $game)
    {
        if ($game->getPlayerHome() == 'BYE') {
            return true;
        }

        if ($game->getPlayerAway() == 'BYE') {
            return true;
        }

        return false;
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

        list($homePlayerTableRowData, $awayPlayerTableRowData) = $this->getPlayersTableData(
            $dateTime,
            $game,
            $homePlayerBaseInfo,
            $awayPlayerBaseInfo
        );

        $this->updateOrCreatePlayerGamesTable($homePlayerBaseInfo, $homePlayerTableRowData);
        $this->updateOrCreatePlayerGamesTable($awayPlayerBaseInfo, $awayPlayerTableRowData);
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

        $homePlayerSiteLatinCountryCode = $homePlayerDonorLatinCountryCode
            ? $this->getSiteCountryCodeByDonorCountryCode($homePlayerDonorLatinCountryCode)
            : null;

        return $this->getPlayerBaseInfo($homePlayerLatinLastName, $homePlayerLatinFirstNameFirstLetters, $homePlayerSiteLatinCountryCode);
    }

    private function getLatinPlayerNameParts($sourcePlayer)
    {
        return $this->sourcePlayerSplitter->getLatinPlayerNameParts($sourcePlayer);
    }

    /**
     * @param string $donorCountryCode
     * @return string
     */
    private function getSiteCountryCodeByDonorCountryCode($donorCountryCode)
    {
        return $this->donorLatinToSiteLatinCountryCodeConverter->getSiteLatinByDonorLatin($donorCountryCode);
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
    )
    {
        return $this->XScoreGameToTableRowConverter->convert($dateTime, $game, $homePlayerBaseInfo, $awayPlayerBaseInfo);
    }

    /**
     * @param PlayerBaseInfo $playerBaseInfo
     * @param $playerTableRowData
     * @throws Exception
     */
    private function updateOrCreatePlayerGamesTable(PlayerBaseInfo $playerBaseInfo, array $playerTableRowData): void
    {
        if (!$homePlayerTableShortCode = $playerBaseInfo->getTableShortCode()) {
            $this->createAndLinkGamesTableWithDataRow($playerBaseInfo, $playerTableRowData);
        } else {
            $homePlayerTableId = $this->getPlayerTableIdByShortCode($homePlayerTableShortCode);
            $this->updatePlayerTableIfNecessary($homePlayerTableId, $playerTableRowData);
        }
    }

    /**
     * @param PlayerBaseInfo $playerBaseInfo
     * @param array $playerTableRowData
     * @throws Exception
     */
    private function createAndLinkGamesTableWithDataRow(PlayerBaseInfo $playerBaseInfo, array $playerTableRowData)
    {
        $tableId = $this->createGamesTable($playerBaseInfo, $playerTableRowData);
        $this->linkGamesTable($tableId, $playerBaseInfo);
    }

    /**
     * @param PlayerBaseInfo $playerBaseInfo
     * @param array $playerTableRowData
     * @return string
     * @throws Exception
     */
    private function createGamesTable(PlayerBaseInfo $playerBaseInfo, array $playerTableRowData)
    {
        return $this->playerGamesTableCreator->createTableByBaseInfo($playerBaseInfo, $playerTableRowData);
    }

    /**
     * @param string $tableId
     * @param PlayerBaseInfo $playerBaseInfo
     * @throws Exception
     */
    private function linkGamesTable($tableId, PlayerBaseInfo $playerBaseInfo)
    {
        $pageId = $playerBaseInfo->getPostId();
        $shortCode = $this->getShortCodeByTableId($tableId);
        $this->updatePostMeta($pageId, MetaFieldKeyInterface::GAMES_TABLE_SHORT_CODE, $shortCode);
    }

    /**
     * @param string $tableId
     * @return string
     */
    private function getShortCodeByTableId($tableId)
    {
        return "[table id={$tableId} /]";
    }

    /**
     * @param int $postId
     * @param string $metaKey
     * @param string $metaValue
     * @throws Exception
     */
    private function updatePostMeta($postId, $metaKey, $metaValue)
    {
        if (!update_post_meta($postId, $metaKey, $metaValue)) {
            throw new Exception("Can\'t update post meta: postId={$postId}, metaKey={$metaKey}, metaValue={$metaValue}");
        };
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
     * @param string $homePlayerTableId
     * @param array $homePlayerTableRowData
     * @throws Exception
     */
    private function updatePlayerTableIfNecessary(string $homePlayerTableId, array $homePlayerTableRowData)
    {
        $this->playerGamesUpdater->updateGamesIfNecessary($homePlayerTableId, $homePlayerTableRowData);
    }

    /**
     * @throws Exception
     */
    public function updateYesterday()
    {
        $dateTime = $this->getYesterdayDatetime();
        $this->updateForDate($dateTime);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function getYesterdayDatetime()
    {
        $dateTime = new DateTime('yesterday');

        return $dateTime;
    }
}
