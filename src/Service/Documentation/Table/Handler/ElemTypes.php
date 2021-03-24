<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

final class ElemTypes extends TableTool
{
    public function getElemTypes($elemTypesPath): ?array
    {
        $errors = [];
		$elemTypes = null;
		$elemTypesContent = file($elemTypesPath);
		$firstRow = str_getcsv($elemTypesContent[0], "	");

		$columnNameErrors = $this->getColumnNameErrors("ElemTypes.txt", $firstRow, 
            [0, 'Elemental Type'],
            [1, 'Code']
        ); $this->setErrors($columnNameErrors);

		if(empty($columnNameErrors))
		{
            $elemTypes[] = ['Elemental Type' => 'None', 'Code' => null];

			for($i = 1, $c = count($elemTypesContent); $i < $c; $i++)
            {
                $row = str_getcsv($elemTypesContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in ElemTypes.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in ElemTypes.txt!";
                else
                {
                    $this->trimRow($row);
                
                    if(!empty($row[0]) && !empty($row[1]))
                    {
                        $elemTypes[] = ['id' => $i-1, 'Elemental Type' => $row[0], 'Code' => $row[1]];
                    }
                }
            }
        }
        $duplicateErrors = $this->getDuplicateErrors("ElemTypes.txt", $elemTypes, ['Code']);

        $this->setErrors($errors);
        $this->setErrors($duplicateErrors);
        $this->setErrors($this->getErrors(), true);
		return $elemTypes;
    }
}