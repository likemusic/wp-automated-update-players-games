<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use DateTime;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\CountryCodeConverter\DonorLatinToSiteCyrillic as DonorLatinToSiteCyrillicCountryCodeConverter;

class XScoreGameToTableRowConverter
{
    /** @var DonorLatinToSiteCyrillicCountryCodeConverter */
    private $donorLatinToSiteCyrillicCountryCodeConverter;

    /**
     * XScoreGameToTableRowConverter constructor.
     * @param DonorLatinToSiteCyrillicCountryCodeConverter $donorLatinToSiteCyrillicCountryCodeConverter
     */
    public function __construct(DonorLatinToSiteCyrillicCountryCodeConverter $donorLatinToSiteCyrillicCountryCodeConverter)
    {
        $this->donorLatinToSiteCyrillicCountryCodeConverter = $donorLatinToSiteCyrillicCountryCodeConverter;
    }

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
        list($cyrillicLastName, $cyrillicFirstNameFirstLetter, $cyrillicCountryCode) = $this->getCyrillicPlayerParts($playerBaseInfo);

        return "{$cyrillicLastName} {$cyrillicFirstNameFirstLetter}. ({$cyrillicCountryCode})";
    }

    private function getCyrillicPlayerParts(PlayerBaseInfo $playerBaseInfo)
    {
        $playerFullName = $playerBaseInfo->getPostTitle();
        list($playerFirstName, $playerLastName) = explode(' ', $playerFullName);

        $playerFirstNameFirstLetter = mb_substr($playerFirstName,0,1);

        $latinCountryCode = $playerBaseInfo->getLatinCountryCode();
        $cyrillicCountryCode = $this->getCyrillicCountryCode($latinCountryCode);
        $cyrillicCountryCodeWithUpperCaseFirst = $this->upperCaseFirst($cyrillicCountryCode);

        return [
            $playerLastName,
            $playerFirstNameFirstLetter,
            $cyrillicCountryCodeWithUpperCaseFirst
        ];
    }

    private function upperCaseFirst($str)
    {
        return mb_convert_case($str, MB_CASE_TITLE);
    }

    private function getCyrillicCountryCode(string $latinCountryCode)
    {
        return $this->donorLatinToSiteCyrillicCountryCodeConverter->getSiteCyrillicByDonorLatin($latinCountryCode);
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
        if ($score[0] === '-'){
            return '-';
        }

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
        if ($score[0] === '-'){//todo: to func
            return '-';
        }

        return ($score[0] < $score[1]) ? 'в' : 'п';
    }
}
