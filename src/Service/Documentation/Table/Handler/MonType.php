<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

final class MonType extends TableTool
{
    public function getMonType(array $requestData, array $tblContent): ?array
	{
		$errors = [];
        $monType = null;
        $monTypeContent = file($requestData['monTypeTmpPath']);
        $firstRow = str_getcsv($monTypeContent[0], "	");
        
        $columnNameErrors = $this->getColumnNameErrors("MonType.txt", $firstRow, 
            [0, 'type'],
            [1, 'equiv1'],
            [5, 'strplur']
        ); $this->setErrors($columnNameErrors);
        
        if(empty($columnNameErrors))
        {
            for($i = 1, $c = count($monTypeContent); $i < $c; $i++)
            {
                $row = str_getcsv($monTypeContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in MonType.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in MonType.txt!";
                else
                {
                    $this->trimRow($row);

                    if(strlen($row[0]))
                    {
                        $monType[] = [];
                    }
                }
            }
        }

        $this->setErrors($errors);
        $this->setErrors($this->getErrors(), true);
        return $monType;
    }
}