<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use DateTime;
use TennisScoresGrabber\XScores\Contracts\Entities\GameInterface;
use WP_Query;

class XScoreGameToTableRowConverter
{
    public function convert(GameInterface $game, DateTime $dateTime)
    {
        $tableDate = $this->getTableDateByDateTime($dateTime);
        $tablePlayerNameHome = $this->getTablePlayerName($game->getPlayerHome());
        $tablePlayerNameAway = $this->getTablePlayerName($game->getPlayerAway());

        $scoreHome = $this->getHomeScore($game);
        $scoreAway = $this->getAwayScore($game);

        $tableScore = $this->getTableScore($scoreHome, $scoreAway);

        $tableResultHome = $this->getTableResult($game);
        $tableResultAway = $this->getTableResult($game);

        $tablePlayerDataHome = $this->getTablePlayerData($tableDate, $tablePlayerNameHome, $tablePlayerNameAway, $tableScore, $tableResultHome);
        $tablePlayerDataAway = $this->$this->getTablePlayerData($tableDate, $tablePlayerNameHome, $tablePlayerNameAway, $tableScore, $tableResultAway);

        return [$tablePlayerDataHome, $tablePlayerDataAway];
    }

    private function getTablePlayerName($sourcePlayerName)
    {
        list($latinFirstName, $latinLastNameFirstLetter, $latinCountryCode) = $this->getLatinPlayerNameParts($sourcePlayerName);

        list($cyrillicFirstName, $cyrillicLastNameFirstLetter, $cyrillicCountryCode) = $this->getCyrillicPlayerNameParts($latinFirstName, $latinLastNameFirstLetter, $latinCountryCode);

        return "{$cyrillicFirstName} {$cyrillicLastNameFirstLetter}. ({$cyrillicCountryCode})";
    }

    private function getCyrillicPlayerNameParts($latinFirstName, $latinLastNameFirstLetter, $latinCountryCode)
    {
        $wpQueryArgs = [
            'posts_per_page'   => -1,
            'nopaging' => true,
            'post_type'        => 'page',
            'post_parent' => 110, //Игроки
            'meta_query' => [
                [
                    'meta_key'         => 'country',
                    'meta_value'       => $latinCountryCode,
                ],
                [
                    'key' => 'name_lat',
                    'value' => "{$latinLastNameFirstLetter}% {$latinFirstName}",
                    'compare'   => 'LIKE'
                ]
            ]
        ];

        $wpQuery = new WP_Query($wpQueryArgs);

        if (!$wpQuery->have_posts()) {
            throw new \Exception("No page for {$latinFirstName} {$latinLastNameFirstLetter}. ({$latinCountryCode})");
        }

        $posts = $wpQuery->get_posts();

        if (($postsCount = count($posts)) > 1) {
            throw new \Exception("Too many posts ({$postsCount}) for {$latinFirstName} {$latinLastNameFirstLetter}. ({$latinCountryCode}). Expected 1");
        }

        $post = current($posts);

        $postName = $post->post_name;
        $postId = $post->id;
        $tableShortCode = get_post_meta($post, 'n-matches-player');
        $tableId = $this->getTableIdByShortCode($tableShortCode);
    }

    private function getTableIdByShortCode($tableShortCode)
    {
        $pattern = '//';
        $matches = [];
        preg_match($pattern, $tableShortCode, $matches);

        return $matches['tableId'];
    }

    private function getLatinPlayerNameParts($sourcePlayerName)
    {
        $pattern = '(?<lastname>\w+) (?<fn>\w). \((?<country>\w+)\)';
        $matches = [];

        if (!preg_match($pattern, $sourcePlayerName, $matches)) {
            throw new \InvalidArgumentException('Invalid source player name: '. $sourcePlayerName);
        }

        return [
            $matches['lastname'],
            $matches['fn'],
            $matches['country'],
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
}
