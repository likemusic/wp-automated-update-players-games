<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable;

use Exception;
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

        $pressTable['data'] = $tableData;

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
        array_unshift($tableData, $playerTableRowData);

        return $tableData;
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
