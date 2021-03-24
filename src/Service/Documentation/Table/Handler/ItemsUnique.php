<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Utils\ErrorHandler;

final class ItemsUnique extends TableTool
{
    private $strings;
	
	public function __construct(ErrorHandler $errorHandler, Strings $strings, SessionInterface $session)
	{
        $this->strings = $strings;
        parent::__construct($errorHandler, $session);
	}

    public function getUniqueItems(array $pathList, array $tblContentLang): ?array
	{
		$errors = [];
        $uniqueItemsWithoutTranslations = null;
        $uniqueItemsContent = file($pathList['uniqueitems']);
        $firstRow = str_getcsv($uniqueItemsContent[0], "	");
    
        $columnNameErrors = $this->getColumnNameErrors("UniqueItems.txt", $firstRow,
            [0, 'index'],
            [2, 'enabled'],
            [6, 'lvl'],
            [7, 'lvl req'],
            [8, 'code'],
            [17, 'invfile'],
            [21, 'prop1'], [22, 'par1'], [23, 'min1'], [24, 'max1'],
            [25, 'prop2'], [26, 'par2'], [27, 'min2'], [28, 'max2'],
            [29, 'prop3'], [30, 'par3'], [31, 'min3'], [32, 'max3'],
            [33, 'prop4'], [34, 'par4'], [35, 'min4'], [36, 'max4'],
            [37, 'prop5'], [38, 'par5'], [39, 'min5'], [40, 'max5'],
            [41, 'prop6'], [42, 'par6'], [43, 'min6'], [44, 'max6'],
            [45, 'prop7'], [46, 'par7'], [47, 'min7'], [48, 'max7'],
            [49, 'prop8'], [50, 'par8'], [51, 'min8'], [52, 'max8'],
            [53, 'prop9'], [54, 'par9'], [55, 'min9'], [56, 'max9'],
            [57, 'prop10'], [58, 'par10'], [59, 'min10'], [60, 'max10'],
            [61, 'prop11'], [62, 'par11'], [63, 'min11'], [64, 'max11'],
            [65, 'prop12'], [66, 'par12'], [67, 'min12'], [68, 'max12']
        ); $this->setErrors($columnNameErrors);
        
        if(empty($columnNameErrors))
        {
            $id = 1;

            for($i = 1, $c = count($uniqueItemsContent); $i < $c; $i++)
            {
                $row = str_getcsv($uniqueItemsContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in UniqueItems.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in UniqueItems.txt!";
                else
                {
                    $this->trimRow($row);

                    if(!in_array($row[0], ['Expansion', 'Armor', 'Elite Uniques', 'Rings', 'Class Specific']) && $row[2] === '1')
                    {
                        if(!strlen($row[0])) $errors['errors'][] = "Found empty value in column 'index' at row " .($i+1). " in UniqueItems.txt.";
                        if((strlen($row[2]) && !ctype_digit($row[2])) || (ctype_digit($row[2]) && (int)$row[2] > 1)) $errors['errors'][] = "Found invalid value in column 'enabled' at row " .($i+1). " in UniqueItems.txt.";
                        if(!strlen($row[6]) || !ctype_digit($row[6]) || (int)$row[6] < 0 || (int)$row[6] > 255) $errors['errors'][] = "Found empty or invalid value in column 'lvl' at row " .($i+1). " in UniqueItems.txt.";
                        if(!strlen($row[7]) || !ctype_digit($row[7]) || (int)$row[7] < 0 || (int)$row[7] > 255) $errors['errors'][] = "Found empty or invalid value in column 'lvl req' at row " .($i+1). " in UniqueItems.txt.";
                        if(!strlen($row[8])) $errors['errors'][] = "Found empty value in column 'code' at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[21]) && !strlen($row[22]) && (filter_var($row[23], FILTER_VALIDATE_INT) === false || filter_var($row[24], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par1' column value and empty or invalid 'min1' column value or 'max1' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[25]) && !strlen($row[26]) && (filter_var($row[27], FILTER_VALIDATE_INT) === false || filter_var($row[28], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par2' column value and empty or invalid 'min2' column value or 'max2' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[29]) && !strlen($row[30]) && (filter_var($row[31], FILTER_VALIDATE_INT) === false || filter_var($row[32], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par3' column value and empty or invalid 'min3' column value or 'max3' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[33]) && !strlen($row[34]) && (filter_var($row[35], FILTER_VALIDATE_INT) === false || filter_var($row[36], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par4' column value and empty or invalid 'min4' column value or 'max4' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[37]) && !strlen($row[38]) && (filter_var($row[39], FILTER_VALIDATE_INT) === false || filter_var($row[40], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par5' column value and empty or invalid 'min5' column value or 'max5' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[41]) && !strlen($row[42]) && (filter_var($row[43], FILTER_VALIDATE_INT) === false || filter_var($row[44], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par6' column value and empty or invalid 'min6' column value or 'max6' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[45]) && !strlen($row[46]) && (filter_var($row[47], FILTER_VALIDATE_INT) === false || filter_var($row[48], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par7' column value and empty or invalid 'min7' column value or 'max7' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[49]) && !strlen($row[50]) && (filter_var($row[51], FILTER_VALIDATE_INT) === false || filter_var($row[52], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par8' column value and empty or invalid 'min8' column value or 'max8' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[53]) && !strlen($row[54]) && (filter_var($row[55], FILTER_VALIDATE_INT) === false || filter_var($row[56], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par9' column value and empty or invalid 'min9' column value or 'max9' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[57]) && !strlen($row[58]) && (filter_var($row[59], FILTER_VALIDATE_INT) === false || filter_var($row[60], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par10' column value and empty or invalid 'min10' column value or 'max10' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[61]) && !strlen($row[62]) && (filter_var($row[63], FILTER_VALIDATE_INT) === false || filter_var($row[64], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par11' column value and empty or invalid 'min11' column value or 'max11' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(strlen($row[65]) && !strlen($row[66]) && (filter_var($row[67], FILTER_VALIDATE_INT) === false || filter_var($row[68], FILTER_VALIDATE_INT) === false)) $errors['errors'][] = "Found empty 'par12' column value and empty or invalid 'min12' column value or 'max12' column value at row " .($i+1). " in UniqueItems.txt.";
                        if(
                            strlen($row[0]) &&
                            ctype_digit($row[2]) && (int)$row[2] === 1 &&
                            strlen($row[6]) && ctype_digit($row[6]) && (int)$row[6] >= 0 && (int)$row[6] <= 255 &&
                            strlen($row[7]) && ctype_digit($row[7]) && (int)$row[7] >= 0 && (int)$row[7] <= 255 &&
                            strlen($row[8]) &&
                            ((strlen($row[21]) && (strlen($row[22]) || (filter_var($row[23], FILTER_VALIDATE_INT) !== false && filter_var($row[24], FILTER_VALIDATE_INT) !== false))) || !strlen($row[21])) &&
                            ((strlen($row[25]) && (strlen($row[26]) || (filter_var($row[27], FILTER_VALIDATE_INT) !== false && filter_var($row[28], FILTER_VALIDATE_INT) !== false))) || !strlen($row[25])) &&
                            ((strlen($row[29]) && (strlen($row[30]) || (filter_var($row[31], FILTER_VALIDATE_INT) !== false && filter_var($row[32], FILTER_VALIDATE_INT) !== false))) || !strlen($row[29])) &&
                            ((strlen($row[33]) && (strlen($row[34]) || (filter_var($row[35], FILTER_VALIDATE_INT) !== false && filter_var($row[36], FILTER_VALIDATE_INT) !== false))) || !strlen($row[33])) &&
                            ((strlen($row[37]) && (strlen($row[38]) || (filter_var($row[39], FILTER_VALIDATE_INT) !== false && filter_var($row[40], FILTER_VALIDATE_INT) !== false))) || !strlen($row[37])) &&
                            ((strlen($row[41]) && (strlen($row[42]) || (filter_var($row[43], FILTER_VALIDATE_INT) !== false && filter_var($row[44], FILTER_VALIDATE_INT) !== false))) || !strlen($row[41])) &&
                            ((strlen($row[45]) && (strlen($row[46]) || (filter_var($row[47], FILTER_VALIDATE_INT) !== false && filter_var($row[48], FILTER_VALIDATE_INT) !== false))) || !strlen($row[45])) &&
                            ((strlen($row[49]) && (strlen($row[50]) || (filter_var($row[51], FILTER_VALIDATE_INT) !== false && filter_var($row[52], FILTER_VALIDATE_INT) !== false))) || !strlen($row[49])) &&
                            ((strlen($row[53]) && (strlen($row[54]) || (filter_var($row[55], FILTER_VALIDATE_INT) !== false && filter_var($row[56], FILTER_VALIDATE_INT) !== false))) || !strlen($row[53])) &&
                            ((strlen($row[57]) && (strlen($row[58]) || (filter_var($row[59], FILTER_VALIDATE_INT) !== false && filter_var($row[60], FILTER_VALIDATE_INT) !== false))) || !strlen($row[57])) &&
                            ((strlen($row[61]) && (strlen($row[62]) || (filter_var($row[63], FILTER_VALIDATE_INT) !== false && filter_var($row[64], FILTER_VALIDATE_INT) !== false))) || !strlen($row[61])) &&
                            ((strlen($row[65]) && (strlen($row[66]) || (filter_var($row[67], FILTER_VALIDATE_INT) !== false && filter_var($row[68], FILTER_VALIDATE_INT) !== false))) || !strlen($row[65]))
                        )
                        {
                            foreach($tblContentLang as $lang => $tblContent)
                            {
                                if(empty($tblContent[$row[0]])) $errors['errors'][] = "Cannot find the key '{$row[0]}' in any of uploaded .tbl files for language code '$lang'. Key found in UniqueItems.txt in column 'index' at row " .($i+1);
                                else
                                {
                                    $namestr[$lang] = $tblContent[$row[0]];
                                    if(in_array(substr($namestr[$lang], 0, 4), ['[ms]','[fs]','[nl]'])) $namestr[$lang] = substr($namestr[$lang], 4);
                                }
                            }
                            if(empty($errors['errors']))
                            {
                                $this->setLangName($row[0], $namestr['en'] ?? null, $namestr['pl'] ?? null);

                                $propDataList = [];
                                for($propDataN = 1; $propDataN <= 12; $propDataN++)
                                {
                                    $propN = 21+($propDataN-1)*4; // a1+(n-1)*r
                                    $prop = $row[$propN];
                                    if(empty($prop) || $prop[0] === '*') continue;

                                    if($prop === 'randclassskill')
                                    {
                                        if(!strlen($row[$propN+2]) || (int)$row[$propN+2] < 0) $errors['errors'][] = "Found empty or invalid 'min$propDataN' column value for property: '$prop' at row " .($i+1). " in UniqueItems.txt";
                                        if(!strlen($row[$propN+3]) || (int)$row[$propN+3] > 6) $errors['errors'][] = "Found empty or invalid 'max$propDataN' column value for property: '$prop' at row " .($i+1). " in UniqueItems.txt";
                                    }

                                    $par = strlen($row[$propN+1]) ? $row[$propN+1] : null; // mixed
                                    $min = strlen($row[$propN+2]) ? (int)$row[$propN+2] : null; // int
                                    $max = strlen($row[$propN+3]) ? (int)$row[$propN+3] : null; // int
                                    
                                    $propDataList[] = ['prop' => $prop, 'par' => $par, 'min' => $min, 'max' => $max];
                                }
                                if(empty($propDataList)) $errors['notice'][] = "Cannot find any valid properties for item '{$row[0]}' at row " .($i+1). " in UniqueItems.txt";
                                else
                                    $propDataList = $this->getUniqueTableByColumnName($propDataList, 'prop'); // impossible to create unique item with 2 or more the same property names
                                
                                if(empty($errors['errors']))
                                {
                                    $uniqueItemsWithoutTranslations[] = ['id' => $id, 'index' => $row[0], 'lvl' => (int)$row[6], 'lvl req' => (int)$row[7], 'code' => $row[8], 'invfile' => !empty($row[17]) ? strtolower($row[17]) : null, 'props' => $propDataList];
                                    $id++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $duplicateErrors = $this->getDuplicateErrors("UniqueItems.txt", $uniqueItemsWithoutTranslations, ['index']);
        if(empty($duplicateErrors))
            $this->strings->setPropTranslations($pathList, $tblContentLang, $uniqueItemsWithoutTranslations, 'UniqueItems.txt');
        
        $this->setErrors($errors, true);
        $this->setErrors($duplicateErrors, true);
        return $uniqueItemsWithoutTranslations;
    }
}