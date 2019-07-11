<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Likemusic\AutomatedUpdatePlayersGames\Helper\TablePress as TablePressHelper;

class PlayerTableGamesUpdater
{
    /** @var TablePressHelper */
    private $tablePressHelper;

    public function __construct(TablePress $tablePressHelper)
    {
        $this->tablePressHelper = $tablePressHelper;
    }

    public function updateGamesIfNecessary($playerTableId, $playerTableRowData)
    {
        $pressTable = $this->getPressTableById($playerTableId);

        if ($pressTable = $this->updateTableDataIfNecessary($pressTable, $playerTableRowData)) {
            $this->savePressTable($pressTable);
        };
    }

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
        } else {//($existsRow != $playerTableRowData)
            $tableData = $this->updateTableRow($tableData, $existsRow, $playerTableRowData);
        }

        $pressTable['data'] = $tableData;

        return $pressTable;
    }

    private function getTableRow($tableData, $playerTableRowData)
    {
        $filteredRows = array_filter(
            $tableData,
            function ($value) use ($playerTableRowData) {
                return array_slice($playerTableRowData, 0, 3) == array_slice($value, 0, 3);
            },
        ARRAY_FILTER_USE_BOTH
        );

        return $filteredRows ? current($filteredRows) : null;
    }

    private function addTableRow($tableData, $playerTableRowData)
    {
        array_unshift($tableData, $playerTableRowData);

        return $tableData;
    }

    private function updateTableRow($tableData, $existsRow, $playerTableRowData)
    {
        $arrayKeys = array_keys($tableData, $existsRow, $playerTableRowData);
        $arrayKey = current($arrayKeys);
        $tableData[$arrayKey] = $playerTableRowData;

        return $tableData;
    }

    private function savePressTable($pressTable)
    {
        $this->tablePressHelper->save($pressTable);
    }
}
