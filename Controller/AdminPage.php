<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Controller;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use InvalidArgumentException;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\AdminPage\DateType;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\AdminPage\FormFieldInterface;
use Likemusic\AutomatedUpdatePlayersGames\PlayersGamesUpdater;

class AdminPage
{
    const DEFAULT_TYPE = 'current';

    /**
     * @var PlayersGamesUpdater
     */
    private $playersGamesUpdater;

    public function __construct(PlayersGamesUpdater $playersGamesUpdater)
    {
        $this->playersGamesUpdater = $playersGamesUpdater;
    }

    /**
     * @throws Exception
     */
    public function execute() {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        if ($this->isPostRequest()) {
            $this->onPost();
        } else {
            $this->onGet();
        }

    }

    private function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * @throws Exception
     */
    private function onPost() {
        $messages = [];
        $errorMessages = [];

        try {
            $type = $this->getRequestedType();

            switch ($type) {
                case DateType::CURRENT:
                    $dates = [new DateTime()];
                    break;
                case DateType::YESTERDAY:
                    $dates = [new DateTime('-1 day')];
                    break;
                case DateType::DAY:
                    $requestedDate = $this->getRequestedDate();
                    $dates = [$requestedDate];
                    break;
                case DateType::PERIOD:
                    $dates = $this->getRequestedDates();
                    break;
                default:
                    throw new InvalidArgumentException('Unknown type value:'. $type);
            }

            $this->updateForDates($dates);

            $messages[] = 'История игр игроков успешно обновлена';
        } catch (Exception $exception) {
            $errorMessages[] = $exception->getMessage();
        }

        $dateStart = $this->getPostData('date-start');
        $dateEnd = $this->getPostData('date-end');
        $dateMin = $this->getDateMin();
        $dateMax = $this->getDateCurrent();

        include(__DIR__.'/../View/index.php');
    }

    private function getRequestedType()
    {
        $validTypeValues = $this->getAvailableTypes();

        return $this->getPostData('type', self::DEFAULT_TYPE, $validTypeValues);
    }

    private function getAvailableTypes()
    {
        return [
            DateType::CURRENT,
            DateType::YESTERDAY,
            DateType::DAY,
            DateType::PERIOD,
        ];
    }

    private function getPostData($key, $defaultValue = null, $allowedValues = [])
    {
        $value = isset($_POST[$key]) ? $_POST[$key] : $defaultValue;

        if ($allowedValues && !in_array($value, $allowedValues)) {
            $value = current($allowedValues);
        }

        return $value;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function getRequestedDate()
    {
        $requestedDateStr = $this->getPostData(FormFieldInterface::DATE_START);

        return new DateTime($requestedDateStr);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getRequestedDates()
    {
        $requestedDateStartStr = $this->getPostData(FormFieldInterface::DATE_START);
        $requestedDateEndStr = $this->getPostData(FormFieldInterface::DATE_END);

        $dateEnd = new DateTime($requestedDateEndStr);
        $dateEnd->setTime(1,0, 0);

        $period = new DatePeriod(
            new DateTime($requestedDateStartStr),
            new DateInterval('P1D'),
            $dateEnd
        );

        $dates = [];

        foreach ($period as $key => $value) {
            $dates[] = $value;
        }

        return $dates;
    }

    /**
     * @param DateTime[] $dates
     */
    private function updateForDates($dates) {
        foreach ($dates as $date) {
            $this->updateForDate($date);
        }
    }

    private function updateForDate(DateTime $date)
    {
        $this->playersGamesUpdater->updateForDate($date);
    }

    private function getDateMin()
    {
        $dateFormat = 'Y-m-d';

        return date($dateFormat, strtotime('-2 weeks'));
    }

    private function getDateCurrent()
    {
        $dateFormat = 'Y-m-d';

        return date($dateFormat);
    }

    private function onGet() {
        $type = self::DEFAULT_TYPE;
        $messages = [];
        $errorMessages = [];

        $dateStart = $dateEnd = $this->getDateCurrent();
        $dateMin = $this->getDateMin();
        $dateMax = $this->getDateCurrent();

        include(__DIR__.'/../View/index.php');
    }
}
