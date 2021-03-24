<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Utils\ErrorHandler;

final class ItemsCommon extends TableTool
{
    private array $wclass = ['1HS','2HS','1HT','2HT','HT1','BOW','XBW','STF'];

	public function __construct(ErrorHandler $errorHandler, SessionInterface $session)
	{
		parent::__construct($errorHandler, $session);
	}

    public function getCommonWeapons(string $weaponsPath, array $tblContentLang, array $itemTypesLang): ?array
    {
        $errors = [];
        $commonWeapons = null;
        $weaponsContent = file($weaponsPath);
        $firstRow = str_getcsv($weaponsContent[0], "	");

        $columnNameErrors = $this->getColumnNameErrors("Weapons.txt", $firstRow,
            [1, 'type'],
            [3, 'code'],
            [5, 'namestr'],
            [9, 'spawnable'],
            [10, 'mindam'], [11, 'maxdam'],
            // [12, '1or2handed'], [13, '2handed'],
            [14, '2handmindam'], [15, '2handmaxdam'],
            [16, 'minmisdam'], [17, 'maxmisdam'],
            [19, 'rangeadder'],
            [20, 'speed'],
            [21, 'StrBonus'], [22, 'DexBonus'],
            [23, 'reqstr'], [24, 'reqdex'],
            [25, 'durability'],
            [26, 'nodurability'],
            [27, 'level'],
            [28, 'levelreq'],
            [34, 'normcode'], [35, 'ubercode'], [36, 'ultracode'],
            [37, 'wclass'], [38, '2handedwclass'],
            [44, 'minstack'], [45, 'maxstack'],
            [48, 'invfile'],
            [52, 'gemsockets'],
            [65, 'quest'],
            [162, 'NightmareUpgrade'], [163, 'HellUpgrade']
        ); $this->setErrors($columnNameErrors, true);

        if(empty($columnNameErrors))
        {
            for($i = 1, $c = count($weaponsContent); $i < $c; $i++)
            {
                $row = str_getcsv($weaponsContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in Weapons.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in Weapons.txt!";
                else
                {
                    $this->trimRow($row);

                    if($row[0] !== "Expansion" && $row[1] !== "tpot" && $row[9] === '1')
                    {
                        if(empty($row[1])) $errors['errors'][] = "Found empty value in column 'type' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[3])) $errors['errors'][] = "Found empty value in column 'code' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[5])) $errors['errors'][] = "Found empty value in column 'namestr' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[10]) && !ctype_digit($row[10])) $errors['errors'][] = "Found invalid value in column 'mindam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[11]) && !ctype_digit($row[11])) $errors['errors'][] = "Found invalid value in column 'maxdam' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[10]) && strlen($row[11])) $errors['errors'][] = "Found empty value in column 'mindam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[10]) && !strlen($row[11])) $errors['errors'][] = "Found empty value in column 'maxdam' at row " .($i+1). " in Weapons.txt.";
                        // if(strlen($row[12]) && $row[12] !== '1' && $row[12] !== '0') $errors['errors'][] = "Found invalid value in column '1or2handed' at row " .($i+1). " in Weapons.txt.";
                        // if($row[12] === '1')
                        // {
                        //     if(!strlen($row[13])) $errors['errors'][] = "Found empty value in column '2handed' while '1or2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        //     if(!strlen($row[10]) || !strlen($row[11])) $errors['errors'][] = "Found empty value in columns 'mindam' or 'maxdam' while '1or2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        //     if(!strlen($row[14]) || !strlen($row[15])) $errors['errors'][] = "Found empty value in columns '2handmindam' or '2handmaxdam' while '1or2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        //     if(strlen($row[16]) || strlen($row[17])) $errors['errors'][] = "Found not empty value in columns 'minmisdam' or 'maxmisdam' while '1or2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        // }
                        // if(strlen($row[13]) && $row[13] !== '1' && $row[13] !== '0') $errors['errors'][] = "Found invalid value in column '2handed' at row " .($i+1). " in Weapons.txt.";
                        // if($row[13] === '1')
                        // {
                        //     if((strlen($row[10]) || strlen($row[11])) && $row[12] !== '1') $errors['errors'][] = "Found not empty value in columns 'mindam' or 'maxdam' while '2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        //     if(!strlen($row[14]) || !strlen($row[15])) $errors['errors'][] = "Found empty value in columns '2handmindam' or '2handmaxdam' while '2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        //     if(strlen($row[16]) || strlen($row[17])) $errors['errors'][] = "Found not empty value in columns 'minmisdam' or 'maxmisdam' while '2handed' column value is set to 1 at row " .($i+1). " in Weapons.txt.";
                        // }
                        // else if((!strlen($row[13]) || $row[13] === '0') && (strlen($row[14]) && strlen($row[15]))) $errors['notice'][] = "Found not empty value in columns '2handmindam' and '2handmaxdam' while '2handed' column value is empty at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[14]) && !ctype_digit($row[14])) $errors['errors'][] = "Found invalid value in column '2handmindam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[15]) && !ctype_digit($row[15])) $errors['errors'][] = "Found invalid value in column '2handmaxdam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[16]) && !ctype_digit($row[16])) $errors['errors'][] = "Found invalid value in column 'minmisdam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[17]) && !ctype_digit($row[17])) $errors['errors'][] = "Found invalid value in column 'maxmisdam' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[16]) && strlen($row[17])) $errors['errors'][] = "Found empty value in column 'minmisdam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[16]) && !strlen($row[17])) $errors['errors'][] = "Found empty value in column 'maxmisdam' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[19]) && !ctype_digit($row[19])) $errors['errors'][] = "Found invalid value in column 'rangeadder' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[20]) && filter_var($row[20], FILTER_VALIDATE_INT) === false) $errors['errors'][] = "Found invalid value in column 'speed' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[21]) && !ctype_digit($row[21])) $errors['errors'][] = "Found invalid value in column 'StrBonus' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[22]) && !ctype_digit($row[22])) $errors['errors'][] = "Found invalid value in column 'DexBonus' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[23]) && !ctype_digit($row[23])) $errors['errors'][] = "Found invalid value in column 'reqstr' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[24]) && !ctype_digit($row[24])) $errors['errors'][] = "Found invalid value in column 'reqdex' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[25]) || !ctype_digit($row[25])) $errors['errors'][] = "Found empty or invalid value in column 'durability' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[26]) && $row[26] !== '1' && $row[26] !== '0') $errors['errors'][] = "Found invalid value in column 'nodurability' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[27]) || !ctype_digit($row[27])) $errors['errors'][] = "Found empty or invalid value in column 'level' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[28]) || !ctype_digit($row[28])) $errors['errors'][] = "Found empty or invalid value in column 'levelreq' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[34]) && empty($row[35]) && empty($row[36])) $errors['errors'][] = "Found empty value in columns 'normcode', 'ubercode' and 'ultracode' at row " .($i+1). " in Weapons.txt. At least one type has to be specified.";
                        if(empty($row[37]) || !in_array(strtoupper($row[37]), $this->wclass)) $errors['errors'][] = "Found empty or invalid value in column 'wclass' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[38]) || !in_array(strtoupper($row[38]), $this->wclass)) $errors['errors'][] = "Found empty or invalid value in column '2handedwclass' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[44]) && !ctype_digit($row[44])) $errors['errors'][] = "Found invalid value in column 'minstack' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[45]) && !ctype_digit($row[45])) $errors['errors'][] = "Found invalid value in column 'maxstack' at row " .($i+1). " in Weapons.txt.";
                        if(!strlen($row[44]) && strlen($row[45])) $errors['errors'][] = "Found empty value in column 'minstack' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[44]) && !strlen($row[45])) $errors['errors'][] = "Found empty value in column 'maxstack' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[48])) $errors['errors'][] = "Found empty value in column 'invfile' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[52]) && !ctype_digit($row[52])) $errors['errors'][] = "Found invalid value in column 'gemsockets' at row " .($i+1). " in Weapons.txt.";
                        if(strlen($row[65]) && !ctype_digit($row[65])) $errors['errors'][] = "Found invalid value in column 'quest' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[162])) $errors['errors'][] = "Found empty value in column 'NightmareUpgrade' at row " .($i+1). " in Weapons.txt.";
                        if(empty($row[163])) $errors['errors'][] = "Found empty value in column 'HellUpgrade' at row " .($i+1). " in Weapons.txt.";
                        if(
                            !empty($row[1]) && !empty($row[3]) && !empty($row[5]) &&
                            (!strlen($row[10]) || ctype_digit($row[10])) &&
                            (!strlen($row[11]) || ctype_digit($row[11])) &&
                            ((strlen($row[10]) && strlen($row[11])) || (!strlen($row[10]) && !strlen($row[11]))) &&
                            (!strlen($row[14]) || ctype_digit($row[14])) &&
                            (!strlen($row[15]) || ctype_digit($row[15])) &&
                            ((strlen($row[14]) && strlen($row[15])) || (!strlen($row[14]) && !strlen($row[15]))) &&
                            (!strlen($row[16]) || ctype_digit($row[16])) &&
                            (!strlen($row[17]) || ctype_digit($row[17])) &&
                            ((strlen($row[16]) && strlen($row[17])) || (!strlen($row[16]) && !strlen($row[17]))) &&
                            (!strlen($row[19]) || ctype_digit($row[19])) &&
                            (!strlen($row[20]) || filter_var($row[20], FILTER_VALIDATE_INT) !== false) &&
                            (!strlen($row[21]) || ctype_digit($row[21])) &&
                            (!strlen($row[22]) || ctype_digit($row[22])) &&
                            (!strlen($row[23]) || ctype_digit($row[23])) &&
                            (!strlen($row[24]) || ctype_digit($row[24])) &&
                            (strlen($row[25]) && ctype_digit($row[25])) &&
                            (!strlen($row[26]) || $row[26] === '1' || $row[26] === '0') &&
                            (strlen($row[27]) && ctype_digit($row[27])) &&
                            (strlen($row[28]) && ctype_digit($row[28])) &&
                            (!empty($row[34]) || !empty($row[35]) || !empty($row[36])) &&
                            !empty($row[37]) && in_array(strtoupper($row[37]), $this->wclass) &&
                            !empty($row[38]) && in_array(strtoupper($row[38]), $this->wclass) &&
                            (!strlen($row[44]) || ctype_digit($row[44])) &&
                            (!strlen($row[45]) || ctype_digit($row[45])) &&
                            ((strlen($row[44]) && strlen($row[45])) || (!strlen($row[44]) && !strlen($row[45]))) &&
                            !empty($row[48]) &&
                            (!strlen($row[52]) || ctype_digit($row[52])) &&
                            (!strlen($row[65]) || ctype_digit($row[65])) &&
                            !empty($row[162]) &&
                            !empty($row[163])
                        )
                        {
                            $rank = null;
                            if($row[3] === $row[34]) $rank = 'Normal';
                            else if($row[3] === $row[35]) $rank = 'Exceptional';
                            else if($row[3] === $row[36]) $rank = 'Elite';
                            else $errors['errors'][] = "Cannot match weapon code '{$row[3]}' with its type group: '{$row[34]}', '{$row[35]}', '{$row[36]}'";
                            if(!empty($rank))
                            {
                                foreach($itemTypesLang as $lang => $itemTypes)
                                    if(empty($itemTypes[$row[1]])) $errors['errors'][] = "Cannot find translation for 'type' column value '{$row[1]}' for language code '$lang' at row " .($i+1). " in Weapons.txt."; // unsupported user type
                                
                                foreach($tblContentLang as $lang => $tblContent)
                                {
                                    if(empty($tblContent[$row[5]])) $errors['errors'][] = "Cannot find the key '{$row[5]}' in any of uploaded .tbl files for language code '$lang'. Key found in Weapons.txt in column 'namestr' at row " .($i+1);
                                    else
                                    {
                                        $namestr[$lang] = $tblContent[$row[5]];
                                        if(in_array(substr($namestr[$lang], 0, 4), ['[ms]','[fs]','[nl]'])) $namestr[$lang] = substr($namestr[$lang], 4);
                                    }
                                }
                                if(empty($errors['errors']))
                                {
                                    $this->setLangName($row[3], $namestr['en'] ?? null, $namestr['pl'] ?? null);
                                    $this->setLangType($row[1], $itemTypesLang['en'][$row[1]] ?? null, $itemTypesLang['pl'][$row[1]] ?? null);

                                    $commonWeapons[] = [
                                        'code' => $row[3],
                                        'type' => $row[1],
                                        'mindam' => strlen($row[10]) ? (int)$row[10] : null,
                                        'maxdam' => strlen($row[11]) ? (int)$row[11] : null,
                                        // '1or2handed' => $row[12] === '1' ? true : false,
                                        // '2handed' => $row[13] === '1' ? true : false,
                                        '2handmindam' => strlen($row[14]) ? (int)$row[14] : null,
                                        '2handmaxdam' => strlen($row[15]) ? (int)$row[15] : null,
                                        'minmisdam' => strlen($row[16]) ? (int)$row[16] : null,
                                        'maxmisdam' => strlen($row[17]) ? (int)$row[17] : null,
                                        'rangeadder' => strlen($row[17]) ? (int)$row[17] : 0,
                                        'speed' => strlen($row[20]) ? (int)$row[20] : 0,
                                        'StrBonus' => strlen($row[21]) ? (int)$row[21] : 1,
                                        'DexBonus' => strlen($row[22]) ? (int)$row[22] : 1,
                                        'reqstr' => strlen($row[23]) ? (int)$row[23] : 0,
                                        'reqdex' => strlen($row[24]) ? (int)$row[24] : 0,
                                        'durability' => (int)$row[25],
                                        'nodurability' => strlen($row[16]) ? true : ($row[26] === '1' ? true : false),
                                        'level' => (int)$row[27],
                                        'levelreq' => (int)$row[28],
                                        'rank' => $rank,
                                        'normcode' => !empty($row[34]) ? $row[34] : null,
                                        'ubercode' => !empty($row[35]) ? $row[35] : null,
                                        'ultracode' => !empty($row[36]) ? $row[36] : null,
                                        'wclass' => strtoupper($row[37]),
                                        '2handedwclass' => strtoupper($row[38]),
                                        'minstack' => strlen($row[44]) ? $row[44] : null,
                                        'maxstack' => strlen($row[45]) ? $row[45] : null,
                                        'invfile' => strtolower($row[48]),
                                        'gemsockets' => strlen($row[52]) ? (int)$row[52] : 0,
                                        'NightmareUpgrade' => $row[162] !== 'xxx' ? $row[162] : null,
                                        'HellUpgrade' => $row[163] !== 'xxx' ? $row[163] : null,
                                        'quest' => (strlen($row[65]) && (int)$row[65] > 0) ? true : false
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        $duplicateErrors = $this->getDuplicateErrors("Weapons.txt", $commonWeapons, ['code']);

        $this->setErrors($errors, true);
        $this->setErrors($duplicateErrors, true);
        return $commonWeapons;
    }

    public function getCommonArmor(string $armorPath, array $tblContentLang, array $itemTypesLang): ?array
    {
        $errors = [];
        $commonArmor = null;
        $armorContent = file($armorPath);
        $firstRow = str_getcsv($armorContent[0], "	");

        $columnNameErrors = $this->getColumnNameErrors("Armor.txt", $firstRow,
            [4, 'spawnable'],
            [5, 'minac'], [6, 'maxac'],
            [8, 'speed'],
            [9, 'reqstr'],
            [10, 'block'],
            [11, 'durability'],
            [12, 'nodurability'],
            [13, 'level'],
            [14, 'levelreq'],
            [17, 'code'],
            [18, 'namestr'],
            [23, 'normcode'], [24, 'ubercode'], [25, 'ultracode'],
            [31, 'gemsockets'],
            [34, 'invfile'],
            [48, 'type'],
            [59, 'quest'],
            [63, 'mindam'], [64, 'maxdam'],
            [65, 'StrBonus'], [66, 'DexBonus'],
            [159, 'NightmareUpgrade'], [160, 'HellUpgrade']
        ); $this->setErrors($columnNameErrors, true);

        if(empty($columnNameErrors))
        {
            for($i = 1, $c = count($armorContent); $i < $c; $i++)
            {
                $row = str_getcsv($armorContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in Armor.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in Armor.txt!";
                else
                {
                    $this->trimRow($row);

                    if($row[0] !== "Expansion" && $row[4] === '1')
                    {
                        if(!ctype_digit($row[5])) $errors['errors'][] = "Found invalid value in column 'minac' at row " .($i+1). " in Armor.txt.";
                        if(!ctype_digit($row[6])) $errors['errors'][] = "Found invalid value in column 'maxac' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[8]) && filter_var($row[8], FILTER_VALIDATE_INT) === false) $errors['errors'][] = "Found invalid value in column 'speed' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[9]) && !ctype_digit($row[9])) $errors['errors'][] = "Found invalid value in column 'reqstr' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[10]) && filter_var($row[10], FILTER_VALIDATE_INT) === false) $errors['errors'][] = "Found invalid value in column 'block' at row " .($i+1). " in Armor.txt.";
                        if(!strlen($row[11]) || !ctype_digit($row[11])) $errors['errors'][] = "Found empty or invalid value in column 'durability' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[12]) && $row[12] !== '1' && $row[12] !== '0') $errors['errors'][] = "Found invalid value in column 'nodurability' at row " .($i+1). " in Armor.txt.";
                        if(!strlen($row[13]) || !ctype_digit($row[13])) $errors['errors'][] = "Found empty or invalid value in column 'level' at row " .($i+1). " in Armor.txt.";
                        if(!strlen($row[14]) || !ctype_digit($row[14])) $errors['errors'][] = "Found empty or invalid value in column 'levelreq' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[17])) $errors['errors'][] = "Found empty value in column 'code' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[18])) $errors['errors'][] = "Found empty value in column 'namestr' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[23]) && empty($row[24]) && empty($row[25])) $errors['errors'][] = "Found empty value in columns 'normcode', 'ubercode' and 'ultracode' at row " .($i+1). " in Armor.txt. At least one type has to be specified.";
                        if(strlen($row[31]) && !ctype_digit($row[31])) $errors['errors'][] = "Found invalid value in column 'gemsockets' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[34])) $errors['errors'][] = "Found empty value in column 'invfile' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[48])) $errors['errors'][] = "Found empty value in column 'type' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[59]) && !ctype_digit($row[59])) $errors['errors'][] = "Found invalid value in column 'quest' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[63]) && !ctype_digit($row[63])) $errors['errors'][] = "Found invalid value in column 'mindam' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[64]) && !ctype_digit($row[64])) $errors['errors'][] = "Found invalid value in column 'maxdam' at row " .($i+1). " in Armor.txt.";
                        if(!strlen($row[63]) && strlen($row[64])) $errors['errors'][] = "Found empty value in column 'mindam' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[63]) && !strlen($row[64])) $errors['errors'][] = "Found empty value in column 'maxdam' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[65]) && !ctype_digit($row[65])) $errors['errors'][] = "Found invalid value in column 'StrBonus' at row " .($i+1). " in Armor.txt.";
                        if(strlen($row[66]) && !ctype_digit($row[66])) $errors['errors'][] = "Found invalid value in column 'DexBonus' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[159])) $errors['errors'][] = "Found empty value in column 'NightmareUpgrade' at row " .($i+1). " in Armor.txt.";
                        if(empty($row[160])) $errors['errors'][] = "Found empty value in column 'HellUpgrade' at row " .($i+1). " in Armor.txt.";
                        if(
                            ctype_digit($row[5]) &&
                            ctype_digit($row[6]) &&
                            (!strlen($row[8]) || filter_var($row[8], FILTER_VALIDATE_INT) !== false) &&
                            (!strlen($row[9]) || ctype_digit($row[9])) &&
                            (!strlen($row[10]) || filter_var($row[10], FILTER_VALIDATE_INT) !== false) &&
                            strlen($row[11]) && ctype_digit($row[11]) &&
                            (!strlen($row[12]) || $row[12] === '1' || $row[12] === '0') &&
                            strlen($row[13]) && ctype_digit($row[13]) &&
                            strlen($row[14]) && ctype_digit($row[14]) &&
                            !empty($row[17]) &&
                            !empty($row[18]) &&
                            (!empty($row[23]) || !empty($row[24]) || !empty($row[25])) &&
                            (!strlen($row[31]) || ctype_digit($row[31])) &&
                            !empty($row[34]) &&
                            !empty($row[48]) &&
                            (!strlen($row[59]) || ctype_digit($row[59])) &&
                            (!strlen($row[63]) || ctype_digit($row[63])) &&
                            (!strlen($row[64]) || ctype_digit($row[64])) &&
                            ((strlen($row[63]) && strlen($row[64])) || (!strlen($row[63]) && !strlen($row[64]))) &&
                            (!strlen($row[65]) || ctype_digit($row[65])) &&
                            (!strlen($row[66]) || ctype_digit($row[66])) &&
                            !empty($row[159]) &&
                            !empty($row[160])
                        )
                        {
                            $rank = null;
                            if($row[17] === $row[23]) $rank = 'Normal';
                            else if($row[17] === $row[24]) $rank = 'Exceptional';
                            else if($row[17] === $row[25]) $rank = 'Elite';
                            else $errors['errors'][] = "Cannot match armor code '{$row[17]}' with its type group: '{$row[23]}', '{$row[24]}', '{$row[25]}' at row " .($i+1). " in Armor.txt.";
                            if(!empty($rank))
                            {
                                foreach($itemTypesLang as $lang => $itemTypes)
                                    if(empty($itemTypes[$row[48]])) $errors['errors'][] = "Cannot find translation for 'type' column value '{$row[48]}' for language code '$lang' at row " .($i+1). " in Armor.txt."; // unsupported user type
                                foreach($tblContentLang as $lang => $tblContent)
                                {
                                    if(empty($tblContent[$row[18]])) $errors['errors'][] = "Cannot find the key '{$row[18]}' in any of uploaded .tbl files for language code '$lang'. Key found in Armor.txt in column 'namestr' at row " .($i+1);
                                    else
                                    {
                                        $namestr[$lang] = $tblContent[$row[18]];
                                        if(in_array(substr($namestr[$lang], 0, 4), ['[ms]','[fs]','[nl]'])) $namestr[$lang] = substr($namestr[$lang], 4);
                                    }
                                }
                                if(empty($errors['errors']))
                                {
                                    $this->setLangName($row[17], $namestr['en'] ?? null, $namestr['pl'] ?? null);
                                    $this->setLangType($row[48], $itemTypesLang['en'][$row[48]] ?? null, $itemTypesLang['pl'][$row[48]] ?? null);

                                    $commonArmor[] = [
                                        'code' => $row[17],
                                        'type' => $row[48],
                                        'mindam' => strlen($row[63]) ? (int)$row[63] : null,
                                        'maxdam' => strlen($row[64]) ? (int)$row[64] : null,
                                        'minac' => (int)$row[5],
                                        'maxac' => (int)$row[6],
                                        'block' => strlen($row[10]) ? (int)$row[10] : 0,
                                        'speed' => strlen($row[8]) ? (int)$row[8] : 0,
                                        'StrBonus' => strlen($row[65]) ? (int)$row[65] : 1,
                                        'DexBonus' => strlen($row[66]) ? (int)$row[66] : 1,
                                        'reqstr' => strlen($row[9]) ? (int)$row[9] : 0,
                                        'durability' => (int)$row[11],
                                        'nodurability' => $row[12] === '1' ? true : false,
                                        'level' => (int)$row[13],
                                        'levelreq' => (int)$row[14],
                                        'rank' => $rank,
                                        'normcode' => !empty($row[23]) ? $row[23] : null,
                                        'ubercode' => !empty($row[24]) ? $row[24] : null,
                                        'ultracode' => !empty($row[25]) ? $row[25] : null,
                                        'invfile' => strtolower($row[34]),
                                        'gemsockets' => strlen($row[31]) ? (int)$row[31] : 0,
                                        'NightmareUpgrade' => $row[159] !== 'xxx' ? $row[159] : null,
                                        'HellUpgrade' => $row[160] !== 'xxx' ? $row[160] : null,
                                        'quest' => (strlen($row[59]) && (int)$row[59] > 0) ? true : false
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        $duplicateErrors = $this->getDuplicateErrors("Armor.txt", $commonArmor, ['code']);

        $this->setErrors($errors, true);
        $this->setErrors($duplicateErrors, true);
        return $commonArmor;
    }

    public function getMisc(string $miscPath, array $tblContentLang, array $itemTypesLang): ?array
    {
        $errors = [];
        $misc = null;
        $miscContent = file($miscPath);
        $firstRow = str_getcsv($miscContent[0], "	");

        $columnNameErrors = $this->getColumnNameErrors("Misc.txt", $firstRow,
            [5, 'level'],
            [6, 'levelreq'],
            [8, 'spawnable'],
            [9, 'speed'],
            [13, 'code'],
            [15, 'namestr'],
            [20, 'gemsockets'],
            [23, 'invfile'],
            [27, 'TMogType'],
            [28, 'TMogMin'],
            [29, 'TMogMax'],
            [32, 'type'],
            [44, 'minstack'], [45, 'maxstack'],
            [47, 'quest'],
            [63, 'spelldescstr'],
            [64, 'spelldesccalc'],
            [68, 'BetterGem'],
            [160, 'NightmareUpgrade'],
            [161, 'HellUpgrade']
        ); $this->setErrors($columnNameErrors, true);

        if(empty($columnNameErrors))
        {
            for($i = 1, $c = count($miscContent); $i < $c; $i++)
            {
                $row = str_getcsv($miscContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in Misc.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in Misc.txt!";
                else
                {
                    $this->trimRow($row);

                    if($row[8] === '1')
                    {
                        if(!strlen($row[5]) || !ctype_digit($row[5])) $errors['errors'][] = "Found empty or invalid value in column 'level' at row " .($i+1). " in Misc.txt.";
                        if(!strlen($row[6]) || !ctype_digit($row[6])) $errors['errors'][] = "Found empty or invalid value in column 'levelreq' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[9]) && filter_var($row[9], FILTER_VALIDATE_INT) === false) $errors['errors'][] = "Found invalid value in column 'speed' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[13])) $errors['errors'][] = "Found empty value in column 'code' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[15])) $errors['errors'][] = "Found empty value in column 'namestr' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[20]) && !ctype_digit($row[20])) $errors['errors'][] = "Found invalid value in column 'gemsockets' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[23])) $errors['errors'][] = "Found empty value in column 'invfile' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[27])) $errors['errors'][] = "Found empty value in column 'TMogType' at row " .($i+1). " in Misc.txt.";
                        if($row[27] !== 'xxx')
                        {
                            if(!strlen($row[28])) $errors['errors'][] = "Found empty value in column 'TMogMin' while 'TMogType' column has value at row " .($i+1). " in Misc.txt.";
                            if(!strlen($row[29])) $errors['errors'][] = "Found empty value in column 'TMogMax' while 'TMogType' column has value at row " .($i+1). " in Misc.txt.";
                        }
                        if(strlen($row[28]) && !ctype_digit($row[28])) $errors['errors'][] = "Found invalid value in column 'TMogMin' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[29]) && !ctype_digit($row[29])) $errors['errors'][] = "Found invalid value in column 'TMogMax' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[32])) $errors['errors'][] = "Found empty value in column 'type' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[44]) && !ctype_digit($row[44])) $errors['errors'][] = "Found invalid value in column 'minstack' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[45]) && !ctype_digit($row[45])) $errors['errors'][] = "Found invalid value in column 'maxstack' at row " .($i+1). " in Misc.txt.";
                        if(!strlen($row[44]) && strlen($row[45])) $errors['errors'][] = "Found empty value in column 'minstack' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[44]) && !strlen($row[45])) $errors['errors'][] = "Found empty value in column 'maxstack' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[47]) && !ctype_digit($row[47])) $errors['errors'][] = "Found invalid value in column 'quest' at row " .($i+1). " in Misc.txt.";
                        if(strlen($row[64]) && !ctype_digit($row[64])) $errors['errors'][] = "Found invalid value in column 'spelldesccalc' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[68])) $errors['errors'][] = "Found empty value in column 'BetterGem' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[160])) $errors['errors'][] = "Found empty value in column 'NightmareUpgrade' at row " .($i+1). " in Misc.txt.";
                        if(empty($row[161])) $errors['errors'][] = "Found empty value in column 'HellUpgrade' at row " .($i+1). " in Misc.txt.";
                        if(
                            strlen($row[5]) && ctype_digit($row[5]) &&
                            strlen($row[6]) && ctype_digit($row[6]) &&
                            (!strlen($row[9]) || filter_var($row[9], FILTER_VALIDATE_INT) !== false) &&
                            !empty($row[13]) &&
                            !empty($row[15]) &&
                            (!strlen($row[20]) || ctype_digit($row[20])) &&
                            !empty($row[23]) &&
                            !empty($row[27]) &&
                            (
                                ($row[27] !== 'xxx' && strlen($row[28]) && strlen($row[29])) ||
                                ($row[27] === 'xxx' && !strlen($row[28]) && !strlen($row[29]))
                            ) &&
                            (!strlen($row[28]) || ctype_digit($row[28])) &&
                            (!strlen($row[29]) || ctype_digit($row[29])) &&
                            !empty($row[32]) &&
                            (!strlen($row[44]) || ctype_digit($row[44])) &&
                            (!strlen($row[45]) || ctype_digit($row[45])) &&
                            ((strlen($row[44]) && strlen($row[45])) || (!strlen($row[44]) && !strlen($row[45]))) &&
                            (!strlen($row[47]) || ctype_digit($row[47])) &&
                            (!strlen($row[64]) || ctype_digit($row[64])) &&
                            !empty($row[68]) &&
                            !empty($row[160]) &&
                            !empty($row[161])
                        )
                        {
                            foreach($itemTypesLang as $lang => $itemTypes)
                                if(empty($itemTypes[$row[32]])) $errors['errors'][] = "Cannot find translation for 'type' column value '{$row[32]}' for language code '$lang' at row " .($i+1). " in Misc.txt."; // unsupported user type
                            foreach($tblContentLang as $lang => $tblContent)
                            {
                                if(empty($tblContent[$row[15]])) $errors['errors'][] = "Cannot find the key '{$row[15]}' in any of uploaded .tbl files for language code '$lang'. Key found in Misc.txt in column 'namestr' at row " .($i+1);
                                else
                                {
                                    $namestr[$lang] = $tblContent[$row[15]];
                                    if(in_array(substr($namestr[$lang], 0, 4), ['[ms]','[fs]','[nl]'])) $namestr[$lang] = substr($namestr[$lang], 4);
                                }
                            }
                            if(empty($errors['errors']))
                            {
                                $this->setLangName($row[13], $namestr['en'] ?? null, $namestr['pl'] ?? null);
                                $this->setLangType($row[32], $itemTypesLang['en'][$row[32]] ?? null, $itemTypesLang['pl'][$row[32]] ?? null);

                                $misc[] = [
                                    'code' => $row[13],
                                    'type' => $row[32],
                                    'spelldescstr' => !empty($row[63]) ? $row[63] : null,
                                    'spelldesccalc' => (!empty($row[63]) && strlen($row[64])) ? (int)$row[64] : null,
                                    'level' => (int)$row[5],
                                    'levelreq' => (int)$row[6],
                                    'speed' => strlen($row[9]) ? (int)$row[9] : 0,
                                    'gemsockets' => strlen($row[20]) ? (int)$row[20] : 0,
                                    'TMogType' => $row[27] !== 'xxx' ? $row[27] : null,
                                    'TMogMin' => strlen($row[28]) ? (int)$row[28] : null,
                                    'TMogMax' => strlen($row[29]) ? (int)$row[29] : null,
                                    'minstack' => strlen($row[44]) ? (int)$row[44] : null,
                                    'maxstack' => strlen($row[45]) ? (int)$row[45] : null,
                                    'BetterGem' => $row[68] !== 'non' ? $row[68] : null,
                                    'invfile' => strtolower($row[23]),
                                    'NightmareUpgrade' => $row[160] !== 'xxx' ? $row[160] : null,
                                    'HellUpgrade' => $row[161] !== 'xxx' ? $row[161] : null,
                                    'quest' => (strlen($row[47]) && (int)$row[47] > 0) ? true : false
                                ];
                            }
                        }
                    }
                }
            }
        }
        $duplicateErrors = $this->getDuplicateErrors("Misc.txt", $misc, ['code']);

        $this->setErrors($errors, true);
        $this->setErrors($duplicateErrors, true);
        return $misc;
    }
}