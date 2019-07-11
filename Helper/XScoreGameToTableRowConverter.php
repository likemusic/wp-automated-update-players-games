<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use DateTime;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;

class XScoreGameToTableRowConverter
{
    public function convert(
        DateTime $dateTime,
        GameInterface $game,
        PlayerBaseInfo $homePlayerBaseInfo,
        PlayerBaseInfo $awayPlayerBaseInfo
    ) {
        $score = $this->getScore($game);

        $commonTableRowData = $this->getCommonTableRowData($dateTime, $homePlayerBaseInfo, $awayPlayerBaseInfo, $score);

        $homePlayerTableRow = $this->getHomePlayerTableRow($commonTableRowData, $score);
        $awayPlayerTableRow = $this->getAwayPlayerTableRow($commonTableRowData, $score);

        return [$homePlayerTableRow, $awayPlayerTableRow];
    }

    private function getScore(GameInterface $game)
    {
        //todo: implement
        return [$game->getFinalScoreHome(), $game->getFinalScoreAway()];
    }

    private function getCommonTableRowData(
        DateTime $dateTime,
        PlayerBaseInfo $homePlayerBaseInfo,
        PlayerBaseInfo $awayPlayerBaseInfo,
        array $score
    ) {
        $tableDate = $this->getTableDateByDateTime($dateTime);

        $tablePlayerHome = $this->getTablePlayer($homePlayerBaseInfo);
        $tablePlayerAway = $this->getTablePlayer($awayPlayerBaseInfo);

        $tableScore = $this->getTableScore($score);

        return [
            $tableDate,
            $tablePlayerHome,
            $tablePlayerAway,
            $tableScore
        ];
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    private function getTableDateByDateTime(DateTime $dateTime)
    {
        return $dateTime->format('d.m.Y');
    }

    private function getTablePlayer(PlayerBaseInfo $playerBaseInfo)
    {
        list($cyrillicFirstName, $cyrillicLastNameFirstLetter, $cyrillicCountryCode) = $this->getCyrillicPlayerParts($playerBaseInfo);

        return "{$cyrillicFirstName} {$cyrillicLastNameFirstLetter}. ({$cyrillicCountryCode})";
    }

    private function getCyrillicPlayerParts(PlayerBaseInfo $playerBaseInfo)
    {
        $playerFullName = $playerBaseInfo->getPostTitle();
        list($playerFirstName, $playerLastName) = explode(' ', $playerFullName);

        $playerFirstNameFirstLetter = $playerFirstName[0];

        $latinCountryCode = $playerBaseInfo->getCountryCode();
        $countryCode = $this->getCyrillicCountryCode($latinCountryCode);

        return [
            $playerLastName,
            $playerFirstNameFirstLetter,
            $countryCode
        ];
    }

    private function getCyrillicCountryCode(string $latinCountryCode)
    {
        return $latinCountryCode;
        //TODO: implement
    }

    private function getTableScore($score)
    {
        return "{$score[0]}:{$score[1]}";
    }

    private function getHomePlayerTableRow(array $commonTableRowData,array $score)
    {
        $tableResult = $this->getTableHomeResult($score);
        $commonTableRowData[] = $tableResult;

        return $commonTableRowData;
    }

    private function getTableHomeResult(array $score)
    {
        return ($score[0] > $score[1]) ? 'в' : 'п';
    }

    private function getAwayPlayerTableRow(array $commonTableRowData,array $score)
    {
        $tableResult = $this->getTableAwayResult($score);
        $commonTableRowData[] = $tableResult;

        return $commonTableRowData;
    }

    private function getTableAwayResult(array $score)
    {
        return ($score[0] < $score[1]) ? 'в' : 'п';
    }
}
