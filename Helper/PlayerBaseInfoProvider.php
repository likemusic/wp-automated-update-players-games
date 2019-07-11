<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\PostIdInterface;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;

class PlayerBaseInfoProvider
{
    public function getByLatinPlayerInfo(string $latinLastName, string $latinCountryCode): PlayerBaseInfo
    {
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
                    'value' => "{$latinLastName}",
                    'compare'   => 'LIKE'
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

        $tableShortCode = get_post_meta($postId, 'n-matches-player', true);

        $playerBaseInfo = new PlayerBaseInfo();
        $playerBaseInfo
            ->setPostId($postId)
            ->setPostName($postName)
            ->setPostTitle($postTitle)
            ->setTableShortCode($tableShortCode)
        ;

        return $playerBaseInfo;
    }
}
