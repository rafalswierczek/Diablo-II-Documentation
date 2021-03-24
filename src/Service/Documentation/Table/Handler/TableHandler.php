<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use App\Service\Utils\NotificationHandler;
use App\Service\Enum\NotificationEnum;

class TableHandler
{
    protected NotificationHandler $notificationHandler;
    protected TableContainer $tableContainer;
    
	public function __construct(NotificationHandler $notificationHandler, TableContainer $tableContainer)
	{
        $this->notificationHandler = $notificationHandler;
        $this->tableContainer = $tableContainer;
    }

    protected function getExecutionTime(): string
    {
        return round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2).' sec';
    }

    protected function trimRow(array $row): array
    {
        foreach($row as $cellValue)
            $trimedRow[] = trim($cellValue);
        
        return $trimedRow;
    }

    /**
     * Get informations about .txt file header column
     * 
     * @param string $fileName `data/global/excel/*.txt` file name
     * @param array $rowHeader first/header row of table array of .txt file
     * @param array ...$columnNames ['name1', 'name2', ...]
     * 
     * @return array [column_name => index, ...]
     */
    protected function getColumnsData(string $fileName, array $fileHeader, string ...$columnNames): ?array
    {
        if(!empty($duplicateColumnNames = $this->getDuplicateValues($fileHeader)))
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'row.header.duplicateNames', ['fileName' => $fileName, 'duplicateColumnNames' => implode($duplicateColumnNames)]);
        else
        {
            foreach($columnNames as $columnName)
            {
                if(($index = array_search($columnName, $fileHeader)) === false)
                    $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'column.name.notFound', ['columnName' => $columnName, 'fileName' => $fileName]);
                else
                    $columnsData[] = [$columnName => $index];
            }
        }

        return $columnsData ?? null;
    }

    protected function processTable(string $filePath, string $fileName, array $columnNames, callable $tableLogic): void
    {
        $fileLines = file($filePath);
        $fileHeader = explode(" ", $fileLines[0]);
        
        $columnsData = $this->getColumnsData($fileName, $fileHeader, ...$columnNames);
        
        if(!empty($columnsData))
        {
            for($i = 1, $c = count($fileLines); $i < $c; $i++)
            {
                $row = explode("    ", $fileLines[$i]);

                if(empty($row))
                    $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'row.empty', ['fileName' => $fileName, 'rowIndex' => ($i+1)]);
                else if(count($fileHeader) !== count($row))
                    $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'table.column.invalidNumber', ['fileName' => $fileName, 'rowIndex' => ($i+1)]);
                else
                {
                    $row = $this->trimRow($row);
                    [$this, $tableLogic]($row, $i, $columnsData);
                }
            }
        }
    }

    // OGAR:

    private function getDup($table, ...$checkColumnNames)
    {
        $duplicatedRows = [];

        foreach($table as $indexA => $rowA)
        {
            if(!empty($duplicatedRows[$indexA]))
                continue;
            
            foreach($table as $indexB => $rowB)
            {
                if($indexA === $indexB)
                    continue;
                    
                $sameValues = [];

                foreach($checkColumnNames as $columnName)
                    if($rowA[$columnName] === $rowB[$columnName])
                        $sameValues[$columnName] = true;

                if(count($sameValues) === count($checkColumnNames))
                    $duplicatedRows[$indexB] = $rowB;
            }
        }

        return $duplicatedRows ?? [];
    }

    protected function getDuplicateValues(array $simpleArray): array
    {
        return array_values(array_diff_assoc($simpleArray, array_unique($simpleArray))); // array_values: reset index
    }

    // if empty: no duplicates and table is not empty
    protected function getDuplicateErrors(string $file, ?array $table, array $checkColumnKeys, int $rowIndex = null): array
    {
        if(empty($table)) $errors['errors'][] = "Cannot extract data from $file.";
        else
        {
            foreach($checkColumnKeys as $columnIndex => $columnKey)
            {
                if(!array_key_exists($columnKey, $table[0])) throw new \Exception("Cannot find the key '$columnKey' in searched array from file $file");
                $columnValues = array_map(function($row) use ($columnKey){ return $row[$columnKey];}, $table);
                $dups = array_values(array_diff_assoc($columnValues, array_unique($columnValues))); // array_values: reset index

                if(!empty($dups) && $dups[0] !== 'Rainbow Facet')
                {
                    if(isset($rowIndex)) $errors['errors'][] = "Found duplicate values: ".implode(', ', $dups)." at row $rowIndex for column ".($columnIndex+1)." in $file.";
                    else $errors['errors'][] = "Found duplicate values: ".implode(', ', $dups)." in column '$columnKey ' in $file.";
                }
            }
        }

        return $errors ?? [];
    }

    // returns unique reindexed table
    protected function getUniqueTableByColumnName(array $table, string $uniqueColumnName): array
    {
        $tableUnique = [];

        foreach($table as $row)
        {
            foreach($tableUnique as $rowUnique)
                if($row[$uniqueColumnName] === $rowUnique[$uniqueColumnName])
                    continue 2;
                    
            $tableUnique[] = $row;
        }

        return $tableUnique;
    }

    protected function getStringFormatError(array $formatOrder, string $stringFormat): array
    {
        $stringFormatLength = strlen($stringFormat);
        $even = true;
        $specifierIndex = 0;
        $errInfo = "String format: '$stringFormat'. Valid specifiers (in order): ".implode(', ', $formatOrder);

        for($i = 0; $i < $stringFormatLength; $i++)
        {
            if($stringFormat[$i] === '%')
            {
                if(!empty($stringFormat[$i-1]) && $stringFormat[$i-1] === '%' && !$even) $even = true;
                else $even = false;

                if(!empty($stringFormat[$i+1]) && !$even && $stringFormat[$i+1] !== '%')
                {
                    if($specifierIndex >= count($formatOrder)) return ['errors' => ["Too much specifiers in string format. $errInfo"]];

                    if('%'.$stringFormat[$i+1] === $formatOrder[$specifierIndex])
                    {
                        $specifierIndex++;
                        $even = true;
                    }
                    else
                        return ['errors' => ["Found invalid specifier '%{$stringFormat[$i+1]}' in string format. $errInfo"]];
                }
            }
        }

        if(!$even) return ['errors' => ["String format is invalid because it contains unexpected '%' sign. $errInfo"]];
        if($specifierIndex < count($formatOrder)) return ['errors' => ["Not enough specifiers in string format. $errInfo"]];
        return []; // valid string format for this format order
    }

    protected function setLangName(string $code, ?string $nameEn, ?string $namePl): void
    {
        $d2d_langNames = $this->session->get('d2d_langNames') ?? [];
        array_push($d2d_langNames, ['code' => $code, 'en' => $nameEn, 'pl' => $namePl]);
        $this->session->set('d2d_langNames', $d2d_langNames);
    }

    protected function setLangType(string $type, ?string $typeEn, ?string $typePl)
    {
        $d2d_typeNames = $this->session->get('d2d_typeNames') ?? [];
        array_push($d2d_typeNames, ['type' => $type, 'en' => $typeEn, 'pl' => $typePl]);
        $this->session->set('d2d_typeNames', $d2d_typeNames);
    }

    // // $localErrors = ['errors' => ["", ""], 'notices' => ["", ""]]
    // protected function setErrors(array $localErrors, bool $session = false): void
    // {
    //     if(!empty($localErrors['errors']))
    //     {
    //         if($session)
    //         {
    //             $d2d_errors = $this->session->get('d2d_errors') ?? [];
    //             array_push($d2d_errors, ...$localErrors['errors']);
    //             $this->session->set('d2d_errors', $d2d_errors);
    //         }
    //         else
    //         {
    //             if(isset($this->errors['errors'])) array_push($this->errors['errors'], ...$localErrors['errors']);
    //             else $this->errors['errors'] = $localErrors['errors'];
    //         }
    //     }

    //     if(!empty($localErrors['notices']))
    //     {
    //         if($session)
    //         {
    //             $d2d_notices = $this->session->get('d2d_notices') ?? [];
    //             array_push($d2d_notices, ...$localErrors['notices']);
    //             $this->session->set('d2d_notices', $d2d_notices);
    //         }
    //         else
    //         {
    //             if(isset($this->errors['notices'])) array_push($this->errors['notices'], ...$localErrors['notices']);
    //             else $this->errors['notices'] = $localErrors['notices'];
    //         }
    //     }
    // }

    // protected function getErrors(bool $session = false): array
    // {
    //     if($session)
    //         return [
    //             'errors' => $this->session->get('d2d_errors'),
    //             'notices' => $this->session->get('d2d_notices')
    //         ];
    //     else
    //         return $this->errors ?? ['errors' => null,'notices' => null];
    // }
    protected function setErrors(array $localErrors, bool $session = false){}
    protected function getErrors(bool $session = false): array {return [];}
}