<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Tool;

class DuplicateHandler
{
    private array $duplicatedRows = [];

    /**
     * Get duplicated rows from 
     * 
     * @param array $table
     * @param mixed ...$columnNames
     * 
     * @return [type]
     */
    public function getTableDuplicatedRows(array $table, string ...$columnNames)
    {

    }

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
}