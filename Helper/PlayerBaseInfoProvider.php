<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\MetaFieldKeyInterface;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\PostIdInterface;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;
use WP_Query;

class PlayerBaseInfoProvider
{
    public function getByLatinPlayerInfo(string $latinLastName, string $latinPlayerFirstNameFirstLetters, string $latinCountryCode): PlayerBaseInfo
    {
        $firstNameLettersPattern = $this->getFirstNameLettersPattern($latinPlayerFirstNameFirstLetters);

        $wpQueryArgs = [
            'nopaging' => true,
            'post_type' => 'page',
            'post_parent' => PostIdInterface::PLAYERS,
            'meta_query' => [
                [
                    'key'         => 'country',
                    'value'       => $latinCountryCode,
                ],
                [
                    'key' => 'name_lat',
//                    'value' => "{$latinPlayerFirstNameFirstLetter}\w+ {$latinLastName}",
//                    'value' => "{$latinLastName}",
                    'value' => "^{$firstNameLettersPattern} {$latinLastName}$",//todo: check for multiple firstLetters
                    'compare'   => 'REGEXP'
                ]
            ]
        ];

        $wpQuery = new WP_Query($wpQueryArgs);

        if (!$wpQuery->have_posts()) {
            throw new Exception("No page for {$latinLastName} ({$latinCountryCode})");
        }

        $posts = $wpQuery->get_posts();

        if (($postsCount = count($posts)) > 1) {
            throw new Exception("Too many posts ({$postsCount}) for {$latinLastName} ({$latinCountryCode}). Expected 1");
        }

        $post = current($posts);

        $postId = $post->ID;
        $postName = $post->post_name;
        $postTitle = $post->post_title;

        $tableShortCode = get_post_meta($postId, MetaFieldKeyInterface::GAMES_TABLE_SHORT_CODE, true);

        $playerBaseInfo = new PlayerBaseInfo();
        $playerBaseInfo
            ->setPostId($postId)
            ->setPostName($postName)
            ->setPostTitle($postTitle)
            ->setTableShortCode($tableShortCode)
            ->setLatinCountryCode($latinCountryCode)
        ;

        return $playerBaseInfo;
    }

    /**
     * @param string $firstNameFirstLetters
     * @return string
     */
    private function getFirstNameLettersPattern(string $firstNameFirstLetters)
    {
        $letters = explode('.', $firstNameFirstLetters);
        $lettersWithoutEmpty = array_filter($letters);

        $patterns = [];

        foreach ($lettersWithoutEmpty as $letter)
        {
            $patterns[] = "{$letter}[[:alpha:]]+";
        }

        return implode(' ', $patterns);
    }
}
