<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use InvalidArgumentException;

class SourcePlayerSplitter
{
    public function getLatinPlayerNameParts($sourcePlayer)
    {
        $pattern = '/(?<lastName>[\w\s-]+) (?<firstNameFirstLetters>[\w.-]+)( \((?<country>\w+)\))?/';
        $matches = [];

        if (!preg_match($pattern, $sourcePlayer, $matches)) {
            throw new InvalidArgumentException('Invalid source player name: '. $sourcePlayer);
        }

        $country = array_key_exists('country', $matches) ? $matches['country'] : null;

        return [
            $matches['lastName'],
            $matches['firstNameFirstLetters'],
            $country,
        ];
    }
}
