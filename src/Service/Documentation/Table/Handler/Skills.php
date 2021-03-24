<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use App\Service\Enums\ISkills;

final class Skills extends TableTool implements ISkills
{
	public function getSkillTranslationList(string $skillsPath, string $skilldescPath, array $tblContentLang): ?array
	{
		$errors = [];
		$skillTranslationList = null;

		$skillList = $this->getSkillList($skillsPath);
		$skillDescListLang = $this->getSkillDescList($skilldescPath, $tblContentLang);

		if(empty($this->getErrors()['errors']))
		{
			foreach($skillList as $skillData)
			{
				foreach($skillDescListLang as $lang => $skillDescList)
				{
					foreach($skillDescList as $skillDescData)
					{
						if($skillData['skilldesc'] === $skillDescData['skilldesc'])
						{
							$skillTranslationList[$lang][] = ['Id' => $skillData['Id'], 'skill' => $skillData['skill'], 'str name' => $skillDescData['str name'], 'str long' => $skillDescData['str long'], 'charclass' => $skillData['charclass']];
							break;
						}
					}
				}
			}
		}
		
		$this->setErrors($errors);
        $this->setErrors($this->getErrors(), true);
		return $skillTranslationList;
	}

	private function getSkillList(string $skillsPath): ?array
	{
		$errors = [];
		$skillList = null;
		$skillsContent = file($skillsPath);
		$firstRow = str_getcsv($skillsContent[0], "	");

		// todo: reqskill1-3, calc1-4, Param1-8, Elen, ELevLen1-3
		$columnNameErrors = $this->getColumnNameErrors("Skills.txt", $firstRow, 
			[0, 'skill'],
			[1, 'Id'],
			[2, 'charclass'],
			[3, 'skilldesc'],
			[152, 'reqlevel'],
			[172, 'minmana'],
			[173, 'manashift'],
			[174, 'mana'],
			[175, 'lvlmana'],
			[211, 'ToHit'], [212, 'LevToHit'], // it's ok if there is: only 211 || only 212 || both || none (all cases ok)
			[218, 'HitShift'], [219, 'SrcDam'],
			[220, 'MinDam'], [221, 'MinLevDam1'], [222, 'MinLevDam2'], [223, 'MinLevDam3'], [224, 'MinLevDam4'], [225, 'MinLevDam5'],
			[226, 'MaxDam'], [227, 'MaxLevDam1'], [228, 'MaxLevDam2'], [229, 'MaxLevDam3'], [230, 'MaxLevDam4'], [231, 'MaxLevDam5'],
			[233, 'EType'],
			[234, 'EMin'], [235, 'EMinLev1'], [236, 'EMinLev2'], [237, 'EMinLev3'], [238, 'EMinLev4'], [239, 'EMinLev5'], 
			[240, 'EMax'], [241, 'EMaxLev1'], [242, 'EMaxLev2'], [243, 'EMaxLev3'], [244, 'EMaxLev4'], [245, 'EMaxLev5']
		); $this->setErrors($columnNameErrors);
		
		if(empty($columnNameErrors))
		{
			for($i = 1, $c = count($skillsContent); $i < $c; $i++)
            {
                $row = str_getcsv($skillsContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in Skills.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in Skills.txt!";
                else
                {
                    $this->trimRow($row);

					if($row[0] === 'DiabWall' && ctype_digit($row[1]) && !empty($row[3])) { $skillList[] = ['skill' => $row[0], 'Id' => (int)$row[1], 'skilldesc' => $row[3], 'charclass' => "all"]; continue;}

					if(empty($row[0])) $errors['errors'][] = "Found empty value in column 'skill' at row " .($i+1). " in Skills.txt.";
					if(!strlen($row[1]) || !ctype_digit($row[1])) $errors['errors'][] = "Found empty or invalid value in column 'Id' at row " .($i+1). " in Skills.txt.";
					if(in_array((int)$row[1], static::UNUSED)) continue;
					if(!empty($row[0]) && empty($row[3])) $errors['errors'][] = "Found empty value in column 'skilldesc' at row " .($i+1). " in Skills.txt.";
					if(!strlen($row[152]) || !ctype_digit($row[152])) $errors['errors'][] = "Found empty or invalid value in column 'reqlevel' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[172]) || strlen($row[173]) || strlen($row[174]) || strlen($row[175]))
					{
						if(!strlen($row[172]) || !ctype_digit($row[172])) $errors['errors'][] = "Found empty or invalid value in column 'minmana' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[173]) || !ctype_digit($row[173])) $errors['errors'][] = "Found empty or invalid value in column 'manashift' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[174]) || !ctype_digit($row[174])) $errors['errors'][] = "Found empty or invalid value in column 'mana' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[175]) || filter_var($row[175], FILTER_VALIDATE_INT) === false) $errors['errors'][] = "Found empty or invalid value in column 'lvlmana' at row " .($i+1). " in Skills.txt.";
					}
					if(strlen($row[218]) && !ctype_digit($row[218])) $errors['errors'][] = "Found invalid value in column 'HitShift' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[219]) && (!ctype_digit($row[219]) || (int)$row[219] > 128)) $errors['errors'][] = "Found invalid value in column 'SrcDam' at row " .($i+1). " in Skills.txt.";
					if((strlen($row[220]) && ctype_digit($row[220])) || (strlen($row[226]) && ctype_digit($row[226]))) // min is valid or max is valid
					{
						if(!strlen($row[226]) || !ctype_digit($row[226])) $errors['errors'][] = "Found empty or invalid value in column 'MaxDam' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[220]) || !ctype_digit($row[220])) $errors['errors'][] = "Found empty or invalid value in column 'MinDam' at row " .($i+1). " in Skills.txt.";
					}
					if(strlen($row[220]) && !ctype_digit($row[220])) $errors['errors'][] = "Found invalid value in column 'MinDam' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[220]) && ctype_digit($row[220]) && (strlen($row[221]) || strlen($row[222]) || strlen($row[223]) || strlen($row[224]) || strlen($row[225]))) // if MinDam is valid and MinLevDam1-MinLevDam5 are all empty then it's ok (damage per level will be the same) 
					{ 	// check MinLevDam1-MinLevDam5 errors only if one of them is not empty and MinDam has valid value because MinLevDam1-MinLevDam5 are not taken into account if MinDam is empty or invalid
						// then all must be valid or it won't pass ([111111]T, [111 11]F, [110111]F)
						if(!strlen($row[221]) || !ctype_digit($row[221])) $errors['errors'][] = "Found empty or invalid value in column 'MinLevDam1' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[222]) || !ctype_digit($row[222])) $errors['errors'][] = "Found empty or invalid value in column 'MinLevDam2' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[223]) || !ctype_digit($row[223])) $errors['errors'][] = "Found empty or invalid value in column 'MinLevDam3' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[224]) || !ctype_digit($row[224])) $errors['errors'][] = "Found empty or invalid value in column 'MinLevDam4' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[225]) || !ctype_digit($row[225])) $errors['errors'][] = "Found empty or invalid value in column 'MinLevDam5' at row " .($i+1). " in Skills.txt.";
					}
					if(strlen($row[226]) && !ctype_digit($row[226])) $errors['errors'][] = "Found invalid value in column 'MaxDam' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[226]) && ctype_digit($row[226]) && (strlen($row[227]) || strlen($row[228]) || strlen($row[229]) || strlen($row[230]) || strlen($row[231])))
					{
						if(!strlen($row[227]) || !ctype_digit($row[227])) $errors['errors'][] = "Found empty or invalid value in column 'MaxLevDam1' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[228]) || !ctype_digit($row[228])) $errors['errors'][] = "Found empty or invalid value in column 'MaxLevDam2' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[229]) || !ctype_digit($row[229])) $errors['errors'][] = "Found empty or invalid value in column 'MaxLevDam3' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[230]) || !ctype_digit($row[230])) $errors['errors'][] = "Found empty or invalid value in column 'MaxLevDam4' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[231]) || !ctype_digit($row[231])) $errors['errors'][] = "Found empty or invalid value in column 'MaxLevDam5' at row " .($i+1). " in Skills.txt.";
					}
					if(!empty($row[233]) && !in_array($row[233], ['fire','ltng','mag','cold','pois','life','mana','stam','stun','rand','burn','frze'])) $errors['errors'][] = "Found invalid value in column 'EType' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[234]) && !ctype_digit($row[234])) $errors['errors'][] = "Found invalid value in column 'EMin' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[234]) && ctype_digit($row[234]) && (strlen($row[235]) || strlen($row[236]) || strlen($row[237]) || strlen($row[238]) || strlen($row[239])))
					{
						if(!strlen($row[235]) || !ctype_digit($row[235])) $errors['errors'][] = "Found empty or invalid value in column 'EMinLev1' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[236]) || !ctype_digit($row[236])) $errors['errors'][] = "Found empty or invalid value in column 'EMinLev2' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[237]) || !ctype_digit($row[237])) $errors['errors'][] = "Found empty or invalid value in column 'EMinLev3' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[238]) || !ctype_digit($row[238])) $errors['errors'][] = "Found empty or invalid value in column 'EMinLev4' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[239]) || !ctype_digit($row[239])) $errors['errors'][] = "Found empty or invalid value in column 'EMinLev5' at row " .($i+1). " in Skills.txt.";
					}
					if(strlen($row[240]) && !ctype_digit($row[240])) $errors['errors'][] = "Found invalid value in column 'EMax' at row " .($i+1). " in Skills.txt.";
					if(strlen($row[240]) && ctype_digit($row[240]) && (strlen($row[241]) || strlen($row[242]) || strlen($row[243]) || strlen($row[244]) || strlen($row[245])))
					{
						if(!strlen($row[241]) || !ctype_digit($row[241])) $errors['errors'][] = "Found empty or invalid value in column 'EMaxLev1' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[242]) || !ctype_digit($row[242])) $errors['errors'][] = "Found empty or invalid value in column 'EMaxLev2' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[243]) || !ctype_digit($row[243])) $errors['errors'][] = "Found empty or invalid value in column 'EMaxLev3' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[244]) || !ctype_digit($row[244])) $errors['errors'][] = "Found empty or invalid value in column 'EMaxLev4' at row " .($i+1). " in Skills.txt.";
						if(!strlen($row[245]) || !ctype_digit($row[245])) $errors['errors'][] = "Found empty or invalid value in column 'EMaxLev5' at row " .($i+1). " in Skills.txt.";
					}
					if(
						!empty($row[0]) &&
						strlen($row[1]) && ctype_digit($row[1]) &&
						!empty($row[3]) &&
						strlen($row[152]) && ctype_digit($row[152]) &&
						(// [1111]T, [    ]T, else F
							(
								(strlen($row[172]) && ctype_digit($row[172])) &&
								(strlen($row[173]) && ctype_digit($row[173])) &&
								(strlen($row[174]) && ctype_digit($row[174])) &&
								(strlen($row[175]) && filter_var($row[175], FILTER_VALIDATE_INT) !== false)
							) ||
							(!strlen($row[172]) && !strlen($row[173]) && !strlen($row[174]) && !strlen($row[175]))
						) &&
						((strlen($row[218]) && ctype_digit($row[218])) || !strlen($row[218])) &&
						((strlen($row[219]) && ctype_digit($row[219]) && (int)$row[219] <= 128) || !strlen($row[219])) &&
						((strlen($row[220]) && ctype_digit($row[220])) || !strlen($row[220])) &&
						(// [111111]T, [1     ]T, [      ]T, [ 00000]T, [0     ]F, [011111]F
							(
								(strlen($row[220]) && ctype_digit($row[220])) &&
								(strlen($row[221]) && ctype_digit($row[221])) &&
								(strlen($row[222]) && ctype_digit($row[222])) &&
								(strlen($row[223]) && ctype_digit($row[223])) &&
								(strlen($row[224]) && ctype_digit($row[224])) &&
								(strlen($row[225]) && ctype_digit($row[225]))
							) ||
							(strlen($row[220]) && ctype_digit($row[220]) && !strlen($row[221]) && !strlen($row[222]) && !strlen($row[223]) && !strlen($row[224]) && !strlen($row[225])) ||
							!strlen($row[220])
						) &&
						((strlen($row[226]) && ctype_digit($row[226])) || !strlen($row[226])) &&
						(
							(
								(strlen($row[226]) && ctype_digit($row[226])) &&
								(strlen($row[227]) && ctype_digit($row[227])) &&
								(strlen($row[228]) && ctype_digit($row[228])) &&
								(strlen($row[229]) && ctype_digit($row[229])) &&
								(strlen($row[230]) && ctype_digit($row[230])) &&
								(strlen($row[231]) && ctype_digit($row[231]))
							) ||
							(strlen($row[226]) && ctype_digit($row[226]) && !strlen($row[227]) && !strlen($row[228]) && !strlen($row[229]) && !strlen($row[230]) && !strlen($row[231])) ||
							!strlen($row[226])
						) &&
						(in_array($row[233], ['fire','ltng','mag','cold','pois','life','mana','stam','stun','rand','burn','frze']) || empty($row[233])) &&
						((strlen($row[234]) && ctype_digit($row[234])) || !strlen($row[234])) &&
						(
							(
								(strlen($row[234]) && ctype_digit($row[234])) &&
								(strlen($row[235]) && ctype_digit($row[235])) &&
								(strlen($row[236]) && ctype_digit($row[236])) &&
								(strlen($row[237]) && ctype_digit($row[237])) &&
								(strlen($row[238]) && ctype_digit($row[238])) &&
								(strlen($row[239]) && ctype_digit($row[239]))
							) ||
							(strlen($row[234]) && ctype_digit($row[234]) && !strlen($row[235]) && !strlen($row[236]) && !strlen($row[237]) && !strlen($row[238]) && !strlen($row[239])) ||
							!strlen($row[234])
						) &&
						((strlen($row[240]) && ctype_digit($row[240])) || !strlen($row[240])) &&
						(
							(
								(strlen($row[240]) && ctype_digit($row[240])) &&
								(strlen($row[241]) && ctype_digit($row[241])) &&
								(strlen($row[242]) && ctype_digit($row[242])) &&
								(strlen($row[243]) && ctype_digit($row[243])) &&
								(strlen($row[244]) && ctype_digit($row[244])) &&
								(strlen($row[245]) && ctype_digit($row[245]))
							) ||
							(strlen($row[240]) && ctype_digit($row[240]) && !strlen($row[241]) && !strlen($row[242]) && !strlen($row[243]) && !strlen($row[244]) && !strlen($row[245])) ||
							!strlen($row[240])
						)
					)
					{
						if(empty($row[2])) $row[2] = "all";

						$skillList[] = ['skill' => $row[0], 'Id' => (int)$row[1], 'skilldesc' => $row[3], 'charclass' => $row[2]];
					}
				}
			}
		}
		$duplicateErrors = $this->getDuplicateErrors("Skills.txt", $skillList, ['skill', 'Id']);

		$this->setErrors($errors);
		$this->setErrors($duplicateErrors);
		return $skillList;
	}

	private function getSkillDescList(string $skillDescPath, array $tblContentLang): ?array
	{
		$errors = [];
		$skillDescList = null;
		$skillDescContent = file($skillDescPath);
		$firstRow = str_getcsv($skillDescContent[0], "	");

		$columnNameErrors = $this->getColumnNameErrors("SkillDesc.txt", $firstRow, 
			[0, 'skilldesc'],
			[7, 'str name'],
			[9, 'str long'],
			[83, 'dsc3line2'], [84, 'dsc3texta2'], [85, 'dsc3textb2'], [86, 'dsc3calca2'],
			[88, 'dsc3line3'], [89, 'dsc3texta3'], [90, 'dsc3textb3'], [91, 'dsc3calca3'],
			[93, 'dsc3line4'], [94, 'dsc3texta4'], [95, 'dsc3textb4'], [96, 'dsc3calca4'],
			[98, 'dsc3line5'], [99, 'dsc3texta5'], [100, 'dsc3textb5'], [101, 'dsc3calca5'],
			[103, 'dsc3line6'], [104, 'dsc3texta6'], [105, 'dsc3textb6'], [106, 'dsc3calca6'],
			[108, 'dsc3line7'], [109, 'dsc3texta7'], [110, 'dsc3textb7'], [111, 'dsc3calca7']
		); $this->setErrors($columnNameErrors);
		
		if(empty($columnNameErrors))
		{
			for($i = 1, $c = count($skillDescContent); $i < $c; $i++)
            {
                $row = str_getcsv($skillDescContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in SkillDesc.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in SkillDesc.txt!";
                else
                {
                    $this->trimRow($row);

					if(empty($row[0])) $errors['errors'][] = "Found empty value in column 'skilldesc' at row " .($i+1). " in SkillDesc.txt.";
					if(empty($row[7])) $errors['errors'][] = "Found empty value in column 'str name' at row " .($i+1). " in SkillDesc.txt.";
					if(empty($row[9])) $errors['errors'][] = "Found empty value in column 'str long' at row " .($i+1). " in SkillDesc.txt.";
					if(
						!empty($row[0]) &&
						!empty($row[7]) &&
						!empty($row[9])
					)
					{
						foreach($tblContentLang as $lang => $tblContent)
						{
							if(in_array($row[9], ['skillxld7', 'skillxld11', 'skillxld16', 'skillxld21', 'skillxld27', 'skillxld31']) && empty($tblContent[$row[9]])) $row[9] = str_replace('x', '', $row[9]); // fix: Polish language has empty .tbl values
							if(empty($tblContent[$row[7]])) $errors['errors'][] = "Cannot find the key '{$row[7]}' in any of uploaded .tbl files for language code '$lang'. Key found in SkillDesc.txt in column 'str name' at row ".($i+1);
							if(empty($tblContent[$row[9]])) $errors['errors'][] = "Cannot find the key '{$row[9]}' in any of uploaded .tbl files for language code '$lang'. Key found in SkillDesc.txt in column 'str long' at row ".($i+1);
							if(empty($errors['errors']))
							{
								$strLongFix = implode(" ", array_reverse(explode("\n", $tblContent[$row[9]])));
								$skillDescList[$lang][] = ['skilldesc' => $row[0], 'str name' => $tblContent[$row[7]], 'str long' => $strLongFix];
							}
						}
					}
				}
			}
		}
		foreach($skillDescList ?? [] as $lang => $v){ $langUsed = $lang; break;}
		$duplicateErrors = $this->getDuplicateErrors("SkillDesc.txt", $skillDescList[$langUsed] ?? null, ['skilldesc']);

		$this->setErrors($errors);
		$this->setErrors($duplicateErrors);
		return $skillDescList;
	}
}
