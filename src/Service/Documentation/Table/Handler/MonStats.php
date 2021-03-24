<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

final class MonStats extends TableTool
{
    public function getMonStats(string $monstatsPath, array $tblContentLang): ?array
	{
		$errors = [];
        $monStats = null;
        $monStatsContent = file($monstatsPath);
        $firstRow = str_getcsv($monStatsContent[0], "	");
        
        $columnNameErrors = $this->getColumnNameErrors("MonStats.txt", $firstRow, 
            [0, 'Id'],
            [1, 'hcIdx'],
            [5, 'NameStr']
        ); $this->setErrors($columnNameErrors);
        
        if(empty($columnNameErrors))
        {
            $vanillaInvalidRowsId = [340,341,342,343,392,393,412,556,560,561,567,568,569,574];

            for($i = 1, $c = count($monStatsContent); $i < $c; $i++)
            {
                $row = str_getcsv($monStatsContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in MonStats.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in MonStats.txt!";
                else
                {
                    $this->trimRow($row);

                    if($row[0] !== 'Expansion' && !in_array($row[1], $vanillaInvalidRowsId))
                    {
                        if(!strlen($row[0])) $errors['errors'][] = "Found empty value in column 'Id' at row " .($i+1). " in MonStats.txt.";
                        if(!strlen($row[1]) || !ctype_digit($row[1])) $errors['errors'][] = "Found empty or invalid value in column 'hcIdx' at row " .($i+1). " in MonStats.txt.";
                        if(!strlen($row[5])) $errors['notices'][] = "Found empty value in column 'NameStr' at row " .($i+1). " in MonStats.txt.";
                        if(
                            strlen($row[0]) &&
                            strlen($row[1]) && ctype_digit($row[1])
                        )
                        {
                            foreach($tblContentLang as $lang => $tblContent)
                            {
                                if(empty($tblContent[$row[5]])) $errors['errors'][] = "Cannot find monster name in .tbl files for 'NameStr' column value equal {$row[5]} and 'hcIdx' column value equal {$row[1]} for language code '$lang' at row " .($i+1). " in MonStats.txt.";
                                else $name = $tblContent[$row[5]];
    
                                $monStats[$lang][] = ['Id' => $row[0], 'hcIdx' => (int)$row[1], 'NameStr' => $name ?? null];
                            }
                        }
                    }
                }
            }
        }

        $this->setErrors($errors);
        $this->setErrors($this->getErrors(), true);
        return $monStats;
    }
}