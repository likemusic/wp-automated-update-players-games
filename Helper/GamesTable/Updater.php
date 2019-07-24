<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\GamesTableKeyInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\TablePress as TablePressHelper;

class Updater
{
    /** @var TablePressHelper */
    private $tablePressHelper;

    public function __construct(TablePressHelper $tablePressHelper)
    {
        $this->tablePressHelper = $tablePressHelper;
    }

    /**
     * @param string $playerTableId
     * @param array $playerTableRowData
     * @throws Exception
     */
    public function updateGamesIfNecessary(string $playerTableId, array $playerTableRowData)
    {
        $pressTable = $this->getPressTableById($playerTableId);

        if ($pressTable = $this->updateTableDataIfNecessary($pressTable, $playerTableRowData)) {
            $this->savePressTable($pressTable);
        };
    }

    /**
     * @param string $pressTableId
     * @return array
     * @throws Exception
     */
    private function getPressTableById(string $pressTableId): array
    {
        return $this->tablePressHelper->getTableById($pressTableId);
    }

    private function updateTableDataIfNecessary(array $pressTable, $playerTableRowData)
    {
        $tableData = $pressTable['data'];
        $existsRow = $this->getTableRow($tableData, $playerTableRowData);

        if ($existsRow == $playerTableRowData) {
            return false;
        }

        if (!$existsRow) {
            $tableData = $this->addTableRow($tableData, $playerTableRowData);
        } else {
            $tableData = $this->updateTableRow($tableData, $existsRow, $playerTableRowData);
        }

        return $this->setTableData($pressTable, $tableData);
    }

    private function setTableData($pressTable, $tableData)
    {
        $pressTable['data'] = $tableData;
        $rowsCount = count($tableData);
        $pressTable['visibility']['rows'] = array_fill(0, $rowsCount, 1);

        return $pressTable;
    }

    private function getTableRow($tableData, $playerTableRowData)
    {
        foreach ($tableData as $tableRow) {
            if (array_slice($playerTableRowData, 0, 3) == array_slice($tableRow, 0, 3)) {
                return $tableRow;
            }
        }

        return null;
    }

    private function addTableRow($tableData, $playerTableRowData)
    {
        $tableData[] = $playerTableRowData;
        usort($tableData, [$this, 'compareTableRowsByDate']);

        return array_reverse($tableData);
    }

    private function compareTableRowsByDate($row1, $row2)
    {
        $dateStr1 = $row1[GamesTableKeyInterface::DATE];
        $dateStr2 = $row2[GamesTableKeyInterface::DATE];

        $comparableDateStr1 = $this->getComparableDateString($dateStr1);
        $comparableDateStr2 = $this->getComparableDateString($dateStr2);

        return strcmp($comparableDateStr1, $comparableDateStr2);
    }

    private function getComparableDateString($tableRowDateString)
    {
        $chunks = explode('.', $tableRowDateString);
        $reversedChunks = array_reverse($chunks);

        return implode('', $reversedChunks);
    }

    private function updateTableRow($tableData, $existsRow, $playerTableRowData)
    {
        $arrayKeys = array_keys($tableData, $existsRow);
        $arrayKey = current($arrayKeys);
        $tableData[$arrayKey] = $playerTableRowData;

        return $tableData;
    }

    private function savePressTable($pressTable)
    {
        $this->tablePressHelper->save($pressTable);
    }
}
