<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Enums\ICharacters;
use App\Service\Utils\ErrorHandler;

final class Strings extends TableTool implements ICharacters
{
	private $hardcoded;
	private $skills;
	private $monStats;
	private $elemTypes;

	public function __construct(ErrorHandler $errorHandler, Hardcoded $hardcoded, Skills $skills, MonStats $monStats, ElemTypes $elemTypes, SessionInterface $session)
	{
		$this->hardcoded = $hardcoded;
		$this->skills = $skills;
		$this->monStats = $monStats;
		$this->elemTypes = $elemTypes;
		parent::__construct($errorHandler, $session);
	}

	public function setPropTranslations(array $pathList, array $tblContentLang, array $itemList, string $fileName): void
	{
		$errors = [];
		
		if(empty($this->getErrors(true)['errors']))
		{
			$propStringFormatListLang = $this->getPropStringFormatList($pathList['properties'], $pathList['itemstatcost'], $pathList['elemtypes'], $tblContentLang);
			$skillTranslationListLang = $this->skills->getSkillTranslationList($pathList['skills'], $pathList['skilldesc'], $tblContentLang);
			$monStatsLang = $this->monStats->getMonStats($pathList['monstats'], $tblContentLang);

			if(empty($this->getErrors()['errors']) && empty($this->getErrors(true)['errors']))
			{
				foreach($itemList as $itemRow) // foreach row
				{
					foreach($itemRow['props'] as $itemRowIndex => $itemPropData) // foreach prop column in that row
					{
						$translatedStringFormats = []; // reset for each iteration

						foreach($propStringFormatListLang as $lang => $propStringFormatList)
						{
							foreach($propStringFormatList as $propStringFormat) // foreach property with string formats
							{
								if($itemPropData['prop'] === $propStringFormat['prop'])
								{
									foreach($propStringFormat['stringFormats'] as $stringFormat) // foreach string format for specific item property
									{
										switch($stringFormat['func'])
										{
											case 1: // +[minmax] [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 2: // [minmax]% [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '', '%'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 3: // [minmax] [string format]
											{
												if(strlen($itemPropData['par']) && !ctype_digit($itemPropData['par'])) $errors['errors'][] = "Invalid 'par' column value for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']);

													if(ctype_digit($itemPropData['par']) && !empty($minmaxData[1])) // if there is only par valid
														$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $itemPropData['par']);
													else if(empty($minmaxData[1])) // if there are valid min and max and no matter if par is valid (par is unused if min and max are valid)
													{
														$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);
														if(empty($this->getErrors()['errors']))
															$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
													}
													else // par is invalid and min and max are invalid
														$errors['errors'][] = "Combination of 'par', 'min' and 'max' column values are invalid for 'prop' {$itemPropData['prop']} in $fileName";
												}
												break;
											}
											case 4: // +[minmax]% [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+', '%'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 5: // [minmax*100/128]% [string format]
											{
												if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													$min = $itemPropData['min']*100/128;
													$max = $itemPropData['max']*100/128;
													$minmaxData = $this->getMinMax($min, $max, $itemPropData['prop'], '', '%');
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												}
												break;
											}
											case 6: // +[int par] [string format] [string format 2]
											{
												$par = '+'.$itemPropData['par'];
												$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $par).' '.$stringFormat['str2'];
												break;
											}
											case 7: // [int par]% [string format] [string format 2]
											{
												$par = $itemPropData['par'].'%';
												$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $par).' '.$stringFormat['str2'];
												break;
											}
											case 8: // +[int par]% [string format] [string format 2]
											{
												$par = '+'.$itemPropData['par'].'%';
												$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $par).' '.$stringFormat['str2'];
												break;
											}
											case 9: // [int par] [string format] [string format 2]
											{
												$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $itemPropData['par']).' '.$stringFormat['str2'];
												break;
											}
											case 10: // [minmax *100/128]% [string format] [string format 2]
											{
												if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													$min = $itemPropData['min']*100/128;
													$max = $itemPropData['max']*100/128;
													$minmaxData = $this->getMinMax($min, $max, $itemPropData['prop'], '', '%');
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]).' '.$stringFormat['str2'];
												}
												break;
											}
											case 11: // printf("%d", 100/$par)
											{
												$stringFormatError = $this->getStringFormatError(['%d'], $stringFormat['str']); $this->setErrors($stringFormatError);
												if(empty($this->getErrors()['errors']))
												{
													if(!ctype_digit($itemPropData['par'])) $errors['errors'][] = "Invalid 'par' column value for 'prop': {$itemPropData['prop']} in $fileName";
													else
													{
														$par = 100/(int)$itemPropData['par'];
														$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $par);
													}
												}
												break;
											}
											case 12: // +[minmax] [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 13: // +[minmax] [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 14: // +[minmax] [string format]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													if(!ctype_digit($itemPropData['par'])) $errors['errors'][] = "Invalid 'par' column value for 'prop': {$propStringFormat['prop']} in $fileName";
													else
														$translatedStringFormats[$lang][] = $minmaxData[0].' '.$stringFormat['str'][$itemPropData['par']];
												}
												break;
											}
											case 15: // printf("%d %d %s", min, max, skill)
											{
												$stringFormatError = $this->getStringFormatError(['%d','%d','%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												if(empty($this->getErrors()['errors']))
												{
													if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "{$itemRow['index']} Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
													else
													{
														foreach($skillTranslationListLang[$lang] as $skillRow)
															if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $skillRow['Id']) || ucwords($itemPropData['par']) === ucwords($skillRow['skill']))
																$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $itemPropData['min'].'%', $itemPropData['max'], $skillRow['str name']);
													}
												}
												break;
											}
											case 16: // printf("%s %s", minmax, $skill)
											{
												$stringFormatError = $this->getStringFormatError(['%s','%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													foreach($skillTranslationListLang[$lang] as $skillRow)
														if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $skillRow['Id']) || ucwords($itemPropData['par']) === ucwords($skillRow['skill']))
															$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $minmaxData[0], $skillRow['str name']);
												}
												break;
											}
											case 17: // +[minmax] [string format] (over time)
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													if($lang === 'pl') $overtime = 'w czasie';
													else if($lang === 'en') $overtime = 'over time';

													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]).' '.$overtime;
												}
												break;
											}
											case 18: // +[minmax]% [string format] (over time)
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '+', '%'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													if($lang === 'pl') $overtime = 'w czasie';
													else if($lang === 'en') $overtime = 'over time';

													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]).' '.$overtime;
												}
												break;
											}
											case 19: // [string format]
											{
												$translatedStringFormats[$lang][] = $stringFormat['str'];
												break;
											}
											case 20: // [minmax*(-1)]% [string format]
											{
												if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													$min = $itemPropData['min'] * -1;
													$max = $itemPropData['max'] * -1;
													$minmaxData = $this->getMinMax($min, $max, $itemPropData['prop'], '', '%');
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												}
												break;
											}
											case 21: // [minmax*(-1)] [string format]
											{
												if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													$min = $itemPropData['min'] * -1;
													$max = $itemPropData['max'] * -1;
													$minmaxData = $this->getMinMax($min, $max, $itemPropData['prop']);
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]);
												}
												break;
											}
											case 22: // [minmax]% [string format] [monster type]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '', '%'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]).' monster type';
												}
												break;
											}
											case 23: // [minmax]% [string format] [monster name]
											{
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop'], '', '%'); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													foreach($monStatsLang[$lang] as $monStatsRow)
														if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $monStatsRow['hcIdx']) || $itemPropData['par'] === $monStatsRow['Id'])
															$translatedStringFormats[$lang][] = $this->setStringFormatPosition($stringFormat['val'], $stringFormat['str'], $minmaxData[0]).' '.$monStatsRow['NameStr'];
												}
												break;
											}
											case 24: // printf("%d %s %d %d", $max, $skill, $min, $min)
											{
												$stringFormatError = $this->getStringFormatError(['%d','%s','%d','%d'], $stringFormat['str']); $this->setErrors($stringFormatError);
												if(empty($this->getErrors()['errors']))
												{
													if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
													else
													{
														foreach($skillTranslationListLang[$lang] as $skillRow)
															if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $skillRow['Id']) || ucwords($itemPropData['par']) === ucwords($skillRow['skill']))
																$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $itemPropData['max'], $skillRow['str name'], $itemPropData['min'], $itemPropData['min']);
													}
												}
												break;
											}
											case 25: // [string format]
											{
												$translatedStringFormats[$lang][] = $stringFormat['str'];
												break;
											}
											case 26: // [string format]
											{
												$translatedStringFormats[$lang][] = $stringFormat['str'];
												break;
											}
											case 27: // printf("%s %s %s", $minmax, $skill, $classOnly)
											{
												$stringFormatError = $this->getStringFormatError(['%s','%s','%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
												{
													foreach($skillTranslationListLang[$lang] as $skillRow)
													{
														if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $skillRow['Id']) || ucwords($itemPropData['par']) === ucwords($skillRow['skill']))
														{
															$classOnlyKey = ucfirst($skillRow['charclass']).'Only';
															if(empty($tblContentLang[$lang][$classOnlyKey])) $errors['errors'][] = "Cannot find the key '$classOnlyKey' in any of uploaded .tbl files for language code '$lang'. Key generated from 'charclass' column value in Skills.txt";
															else
																$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $minmaxData[0], $skillRow['str name'], $tblContentLang[$lang][$classOnlyKey]);
														}
													}
												}
												break;
											}
											case 28: // printf("%s %s", $minmax, $skill)
											{
												$stringFormatError = $this->getStringFormatError(['%s','%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);

												if(empty($this->getErrors()['errors']))
												{
													foreach($skillTranslationListLang[$lang] as $skillRow)
														if((ctype_digit($itemPropData['par']) && (int)$itemPropData['par'] === $skillRow['Id']) || ucwords($itemPropData['par']) === ucwords($skillRow['skill']))
															$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $minmaxData[0], $skillRow['str name']);
												}
												break;
											}
											case 100: //
											{
												if(!isset($itemPropData['min']) || !isset($itemPropData['max'])) $errors['errors'][] = "Found empty 'mix' or 'max' column values for 'prop': {$itemPropData['prop']} in $fileName";
												else
												{
													if(!isset($stringFormat['propVal'])) $errors['errors'][] = "Found empty 'val' column value for 'prop': {$itemPropData['prop']} in Properties.txt";
													else
													{
														$value = $stringFormat['propVal'] <= 1 ? '+'.$stringFormat['propVal'] : '+1-'.$stringFormat['propVal'];

														if($lang === 'en') $randclassskillTranslation = $value.' to ';
														else if($lang === 'pl') $randclassskillTranslation = $value.' do poziomu umiejętności ';

														for($i = $itemPropData['min']; $i <= $itemPropData['max']; $i++)
															$randclassskillTranslation .= self::CHARACTERS_F100[$lang][$i]."|";
														$randclassskillTranslation = rtrim($randclassskillTranslation, "|");

														if($lang === 'en') $randclassskillTranslation .= " skill levels";

														$translatedStringFormats[$lang][] = $randclassskillTranslation;
													}
												}
												break;
											}
											case 101: // printf("%d %s", $par, $classOnly)
											{
												$stringFormatError = $this->getStringFormatError(['%d','%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												if(empty($this->getErrors()['errors']))
												{
													if(!ctype_digit($itemPropData['par'])) $errors['errors'][] = "Invalid 'par' column value for 'prop': {$itemPropData['prop']} in $fileName";
													else
													{
														$charClassList = [];
														for($rowN = $itemPropData['min']; $rowN <= $itemPropData['max']; $rowN++)
														{
															foreach($skillTranslationListLang[$lang] as $skillRow)
																if($skillRow['Id'] === $rowN)
																	$charClassList[] = $skillRow['charclass'];
														}
														if(empty($charClassList)) $errors['errors'][] = "Cannot fetch any skills for property '{$itemPropData['prop']}'";
														else
														{
															if(count(array_unique($charClassList)) > 1) $errors['errors'][] = "Too many unique 'charclass' column values for property '{$itemPropData['prop']}'. Only 1 'charclass' can be assigned based on min-max range.";
															else
															{
																$classOnlyKey = ucfirst($charClassList[0]).'Only';
																if(empty($tblContentLang[$lang][$classOnlyKey])) $errors['errors'][] = "Cannot find the key '$classOnlyKey' in any of uploaded .tbl files. Key generated from 'charclass' column value in Skills.txt";
																else
																	$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $itemPropData['par'], $tblContentLang[$lang][$classOnlyKey]);
															}
														}
													}
												}
												break;
											}
											case 102: // printf("%s", $minmax)
											{
												$stringFormatError = $this->getStringFormatError(['%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);
												if(empty($this->getErrors()['errors']))
													$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $minmaxData[0]);
												break;
											}
											case 103: // printf("%s", $par|$minmax)
											{
												$stringFormatError = $this->getStringFormatError(['%s'], $stringFormat['str']); $this->setErrors($stringFormatError);
												if(empty($this->getErrors()['errors']))
												{
													if(ctype_digit($itemPropData['par']) && (is_int($itemPropData['min']) || is_int($itemPropData['max']))) $errors['errors'][] = "Cannot handle 'par', 'min' and 'max' columns at the same time for 'prop': {$itemPropData['prop']} in $fileName";
													else
													{
														if(ctype_digit($itemPropData['par'])) // only par is valid
															$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $itemPropData['par']);
														else if(is_int($itemPropData['min']) && is_int($itemPropData['max'])) // only min and max are valid
														{
															$minmaxData = $this->getMinMax($itemPropData['min'], $itemPropData['max'], $itemPropData['prop']); $this->setErrors($minmaxData[1]);
															if(empty($this->getErrors()['errors']))
																$translatedStringFormats[$lang][] = sprintf($stringFormat['str'], $minmaxData[0]);
														}
														else // par is valid and min and max are valid OR all invalid
															$errors['errors'][] = "Found invalid combination of 'par', 'min' and 'max' column values for 'prop' {$itemPropData['prop']} in $fileName";
													}
												}
												break;
											}
											default:
												$errors['errors'][] = "Invalid function number for property '{$itemPropData['prop']}' in $fileName";
										}
									}
									
									if(empty($translatedStringFormats[$lang])) $errors['errors'][] = "For unique item index: '{$itemRow['index']}' - cannot match item property '{$itemPropData['prop']}' with proper translations for language code '$lang'!";

									break;
								}
							}
						}
						
						if(empty($errors['errors'])) // prop has been translated properly
						{
							// reset for each prop:
							$countPropTranslations = [];
							$translatedStringFormatRow = [];
							$translatedStringFormatTable = [];

							foreach($translatedStringFormats as $lang => $propTranslations)
								$countPropTranslations[] = count($propTranslations);

							if(count(array_unique($countPropTranslations)) !== 1) $errors['errors'][] = "For unique item index: '{$itemRow['index']}' - there is a quantity difference between translations (missing translation(s)) for property '{$itemPropData['prop']}'. "; // if some-prop% has 3 translations for 'en' and 4 for 'pl' then there is 1 missing 'en' translation
							else // property has translations in all languages
							{
								/*
									transform from 
									[
										'en' => ['e1','e2','e3'],
										'pl' => ['p1','p2','p3']
									]
									to
									[
										0 => ['en' => 'e1', 'pl' => 'p1'],
										1 => ['en' => 'e2', 'pl' => 'p2'],
										2 => ['en' => 'e3', 'pl' => 'p3'],
									]
								*/
								for($transN = 0; $transN < $countPropTranslations[0]; $transN++) // for each number of translations ([0] is for first language which has the same quantity of translations as other languages)
								{
									foreach($translatedStringFormats as $lang => $propTranslations)
										$translatedStringFormatRow[$lang] = $propTranslations[$transN];

									$translatedStringFormatTable[] = $translatedStringFormatRow;
								}
								
								/*
									[
										[
											'uniqueItemId' => 123,
											'propTranslations' => [
												0 => ['en' => 'e1', 'pl' => 'p1'],
												1 => ['en' => 'e2', 'pl' => 'p2'],
												2 => ['en' => 'e3', 'pl' => 'p3'],
											]
										],
										[
											'uniqueItemId' => 124,
											'propTranslations' => [
												0 => ['en' => 'e4', 'pl' => 'p4'],
												1 => ['en' => 'e5', 'pl' => 'p5'],
												2 => ['en' => 'e6', 'pl' => 'p6'],
											]
										],
									]
								*/
								$d2d_langProperties = $this->session->get('d2d_langProperties') ?? [];
								$d2d_langProperties[] = ['uniqueItemId' => $itemRow['id'], 'propTranslations' => $translatedStringFormatTable];
								$this->session->set('d2d_langProperties', $d2d_langProperties);
							}
						}
					}
				} // end of the items file (last row ended)
			}
		}

		$this->setErrors($errors);
        $this->setErrors($this->getErrors(), true);
	}

	private function getMinMax(?int $min, ?int $max, string $prop, string $prefix = '', string $suffix = ''): array
	{
		if(!isset($min) || !isset($max)) return [null, ['errors' => ["Found empty 'mix' or 'max' column values for 'prop': $prop"]]];

		if(abs($min) > $max || $min === $max)
			$value = $min;
		else
		{
			$value = "$min-$max";
			if($min < 0)
			{
				if($max >= 0)
					$value = "$min-($max)";
				else
					$value = $min.$max;
			}
		}

		if($min < 0) $prefix = '';
		return [$prefix.$value.$suffix, []];
	}

	private function setStringFormatPosition(int $val, string $stringFormat, string $minmax): string
	{
		if($val === 0) return $stringFormat;
		if($val === 1) return $minmax.' '.$stringFormat;
		if($val === 2) return $stringFormat.' '.$minmax;
		throw new \Exception('Invalid val number');
	}

	private function getPropStringFormatList(string $propertiesPath, string $itemstatcostPath, string $elemtypesPath,  array $tblContentLang): ?array
	{
		$errors = [];
		$propStringFormatList = null;
		
		foreach($tblContentLang as $lang => $tblContent)
		{
			$propsHardcoded = $this->hardcoded->getPropsHardcoded($tblContent);
			$statsHardcoded = $this->hardcoded->getStatsHardcoded($tblContent);
			$charSkills = $this->hardcoded->getCharSkills($tblContent);
			//$elemTypes = $this->elemTypes->getElemTypes($elemtypesPath);
			
			if(empty($this->getErrors(true)['errors']))
			{
				$propList = $this->getPropList($propsHardcoded, $propertiesPath);
				$statList = $this->getStatList($statsHardcoded, $itemstatcostPath, $tblContent);
				
				if(empty($this->getErrors()['errors']))
				{
					foreach($propList as $propRow)
					{
						$descRow = [];
						$dgrpRow = [];

						foreach($propRow['stats'] as $propStatsRow)
						{
							if($propStatsRow['func'] === 21)
							{
								if($propStatsRow['stat'] === 'item_addclassskills')
								{
									for($charId = 0; $charId < 7; $charId++)
									{
										if($propStatsRow['val'] === $charId)
										{
											$statsWithTranslation[] = $propStatsRow['stat'];
											$descRow[] = ['str' => $charSkills[$charId], 'str2' => null, 'func' => 13, 'val' => 1];
											break;
										}
									}
									continue;
								}
								// else if($propStatsRow['stat'] === 'item_elemskill') // skip it and assign value from isc.txt
								// { 
								// 	foreach($elemTypes as $row)
								// 	{
								// 		if($propStatsRow['val'] === $row['id'])
								// 		{
								// 			$statsWithTranslation[] = $propStatsRow['stat'];
								// 			$descRow[] = Hardcoded in vanilla. Possible implementation for rich modifications.
								// 			break;
								// 		}
								// 	}
								// }
							}
							else if($propStatsRow['func'] === 36 && $propRow['code'] === 'randclassskill')
							{
								$statsWithTranslation[] = $propStatsRow['stat'];
								$descRow[] = ['str' => $propsHardcoded['randclassskill'][$lang], 'str2' => null, 'func' => 100, 'val' => null, 'propVal' => $propStatsRow['val']]; // str is empty because it's completely hardcoded
								break;
							}

							foreach($statList as $statRow)
							{
								if(!empty($propStatsRow) && $propStatsRow["stat"] === $statRow['Stat']) // ignore rows in Properties.txt that have empty 'stat'(1-7) column values && ...
								{
									$statsWithTranslation[] = $propStatsRow['stat'];

									$descRow[] = ['str' => $statRow['descstrpos'], 'str2' => $statRow['descstr2'], 'func' => $statRow['descfunc'], 'val' => $statRow['descval']];
									$dgrpRow[] = ['str' => $statRow['dgrpstrpos'], 'str2' => $statRow['dgrpstr2'], 'func' => $statRow['dgrpfunc'], 'val' => $statRow['dgrpval']];

									break;
								}
							}
						}

						if(!empty($descRow))
						{
							$statsWithoutTranslation = array_diff(array_map(function($propStatsRow){ return $propStatsRow['stat'];}, $propRow['stats']), $statsWithTranslation);
							if(count($statsWithoutTranslation) >= 1)  $errors['notices'][] = "Cannot find properly translated row in ItemStatCost.txt for row in Properties.txt that has 'Code' column value: '{$propRow['code']}' and has specific untranslated 'Stat' column values: '". implode("', '", $statsWithoutTranslation) ."'.";
							if(count($statsWithoutTranslation) === 0) // all 'stat' column values from specific Properties.txt row have properly related ItemStatCost.txt row
							{
								// if ('func' column values from Properties.txt row are in specific order (could be grouped)) && (there are 2 or more 'stat' and 'func' column values in Properties.txt row) && ($dgrpRow(ItemStatCost.txt row data) elements are identical) && ($dgrpRow elements are not empty (have translations))
								if($propRow['groupable'] && count($dgrpRow) > 1 && count(array_unique($dgrpRow, SORT_REGULAR)) === 1 && (!empty($dgrpRow[0]['strpos']) && !empty($dgrpRow[0]['func']))) //  SORT_REGULAR converts $dgrpRow arrays to strings so any logical difference between $dgrpRow elements(arrays) in any key or value will produce count > 1
									$stringFormats = [$dgrpRow[0]];
								else
									$stringFormats = $descRow;

								$propStringFormatList[$lang][] = ['prop' => $propRow['code'], 'stringFormats' => $stringFormats];
							}
						}
					}

					if(empty($propStringFormatList[$lang])) $errors['errors'][] = "Cannot match any row from Properties.txt with any row in ItemStatCost.txt!";
					else
						foreach($propsHardcoded as $prop => $itemPropData)
							if($prop !== 'randclassskill') $propStringFormatList[$lang][] = ['prop' => $prop, 'stringFormats' => [['str' => $itemPropData[$lang], 'str2' => null, 'func' => $itemPropData['descfunc'], 'val' => $itemPropData['descval']]]];
				}
			}
		}

		$this->setErrors($errors);
		return $propStringFormatList;
	}

	private function getPropList(array $propsHardcoded, string $propertiesPath): ?array
	{
		$errors = [];
		$propList = null;
		$propertiesContent = file($propertiesPath);
		$firstRow = str_getcsv($propertiesContent[0], "	");
		
		$columnNameErrors = $this->getColumnNameErrors("Properties.txt", $firstRow, 
			[0, 'code'],
			[3, 'val1'], [4, 'func1'], [5, 'stat1'],
			[7, 'val2'], [8, 'func2'], [9, 'stat2'],
			[11, 'val3'], [12, 'func3'], [13, 'stat3'],
			[15, 'val4'], [16, 'func4'], [17, 'stat4'],
			[19, 'val5'], [20, 'func5'], [21, 'stat5'],
			[23, 'val6'], [24, 'func6'], [25, 'stat6'],
			[27, 'val7'], [28, 'func7'], [29, 'stat7']
		); $this->setErrors($columnNameErrors);
		
		if(empty($columnNameErrors))
		{
			for($i = 1, $c = count($propertiesContent); $i < $c; $i++)
            {
                $row = str_getcsv($propertiesContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in Properties.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in Properties.txt!";
                else
                {
                    $this->trimRow($row);

					if(!empty($propsHardcoded[$row[0]]) && $row[0] !== 'randclassskill') continue; // exclude hardcoded props
					
					if(empty($row[0])) $errors['errors'][] = "Found empty value in column 'code' at row " .($i+1). " in Properties.txt.";
					if(!empty($row[0]) && $row[0] !== "Expansion")
					{
						$stats = [];
						for($statN = 1; $statN <= 7; $statN++)
						{
							$column = 5+($statN-1)*4; // a1+(n-1)*r   math notation: {an ∈ {5,9,13,17,21,25,29}; r: 4; n ∈ [1,7]}
							if(!empty($row[$column]))
							{
								if(strlen($row[$column-2]) && !ctype_digit($row[$column-2])) $errors['errors'][] = "Found invalid value in column 'val$statN' at row " .($i+1). " in Properties.txt."; // $row[$column-2] is set because of empty getColumnNameErrors
								if(!strlen($row[$column-1]) || !ctype_digit($row[$column-1])) $errors['errors'][] = "Found empty or invalid value in column 'func$statN' at row " .($i+1). " in Properties.txt.";
								if(
									((strlen($row[$column-2]) && ctype_digit($row[$column-2])) || !strlen($row[$column-2])) &&
									strlen($row[$column-1]) && ctype_digit($row[$column-1])
								)
									$stats[] = ['stat' => $row[$column], 'func' => (int)$row[$column-1], 'val' => strlen($row[$column-2]) ? (int)$row[$column-2] : null];
							}
						}
						if(!empty($stats))
						{
							$duplicateErrors = $this->getDuplicateErrors("Properties.txt", $stats, ['stat'], $i+1);
							$this->setErrors($duplicateErrors);
						}

						$groupable = false;
						if(!empty($stats) && count($stats) >= 2 && in_array($stats[0]['func'], [1, 15, 16, 17, 21]))
						{
							$groupable = true;
							for($statN = 1; $statN < count($stats); $statN++)
								if(!in_array($stats[$statN]['func'], [3, 15, 16, 17])) // supported group functions
									$groupable = false;
						}

						$propList[] = ['code' => $row[0], 'stats' => $stats, 'groupable' => $groupable];
					}
				}
			}
		}
		$duplicateErrors = $this->getDuplicateErrors("Properties.txt", $propList, ['code']);
		
		$this->setErrors($errors);
		$this->setErrors($duplicateErrors);
		return $propList;
	}

	private function getStatList(array $statsHardcoded, string $itemStatCostPath, array $tblContent): ?array
	{
		$errors = [];
		$statList = null;
		$itemStatCostContent = file($itemStatCostPath);
		$firstRow = str_getcsv($itemStatCostContent[0], "	");

		$columnNameErrors = $this->getColumnNameErrors("ItemStatCost.txt", $firstRow, 
			[0, 'Stat'],
			[40, 'descfunc'],
			[41, 'descval'],
			[42, 'descstrpos'],
			[44, 'descstr2'],
			[46, 'dgrpfunc'],
			[47, 'dgrpval'],
			[48, 'dgrpstrpos'],
			[50, 'dgrpstr2']
		); $this->setErrors($columnNameErrors);
		
		if(empty($columnNameErrors))
		{
			for($i = 1, $c = count($itemStatCostContent); $i < $c; $i++)
            {
                $row = str_getcsv($itemStatCostContent[$i], "	");
                if(empty($row)) $errors['errors'][] = "Failed to read row with index " .($i+1). " in ItemStatCost.txt.";
                else if(count($firstRow) !== count($row)) $errors['errors'][] = "Found invalid number of columns at row " .($i+1). " in ItemStatCost.txt!";
                else
                {
                    $this->trimRow($row);

					foreach($statsHardcoded as $statHardcoded)
					{
						if($statHardcoded['Stat'] === $row[0]) // if there is supported hardcoded row in ItemStatCost.txt that has not empty 'descstrpos' column value
						{
							if(!empty($row[42])) $errors['notices'][] = "In ItemStatCost.txt at row ".($i+1).", 'descstrpos' column translation value '{$row[42]}' has been replaced with hardcoded string format: '{$statHardcoded['descstrpos']}'"; // some stats from isc.txt that don't have 'descstrpos' value are hardcoded in Hardcoded.php and so nobody can change these stats by inserting values inside 'descstrpos' column because they will be overwritten anyway and additional notice will be displayed.
							
							$statList[] = $statHardcoded;
							continue 2;
						}
					}

					if(empty($row[0])) $errors['errors'][] = "Found empty value in column 'Stat' at row " .($i+1). " in ItemStatCost.txt.";
					else
					{
						if(strlen($row[40]) && !empty($row[42]))
						{
							if(!ctype_digit($row[40])) $errors['errors'][] = "Found invalid value in column 'descfunc' at row ".($i+1)." in ItemStatCost.txt. Set value from 1 to 28. See https://d2mods.info/forum/kb/viewarticle?a=448 at DescFunc to check 'descfunc' column behavior.";
							if(empty($tblContent[$row[42]])) $errors['errors'][] = "Cannot find the key '{$row[42]}' in any of uploaded .tbl files. Key found in ItemStatCost.txt in column 'descstrpos' at row " .($i+1);
							if(ctype_digit($row[40]) && !empty($tblContent[$row[42]]))
							{
								$descstrpos = $tblContent[$row[42]];
								$descval = null;
								$descstr2 = null;
								$dgrpstrpos = null;
								$dgrpfunc = null;
								$dgrpval = null;

								if(strlen($row[41]))
									if(!ctype_digit($row[41]) || (int)$row[41] < 0 || (int)$row[41] > 3) $errors['errors'][] = "Found invalid value in column 'descval' at row ".($i+1)." in ItemStatCost.txt. Set value to 0, 1, 2 or leave it empty.";
									else $descval = (int)$row[41];

								if(!empty($row[44]))
									if(empty($tblContent[$row[44]])) $errors['errors'][] = "Cannot find the key '{$row[44]}' in any of uploaded .tbl files. Key found in ItemStatCost.txt in column 'descstr2' at row " .($i+1);
									else $descstr2 = " ".$tblContent[$row[44]];

								if(strlen($row[46] && !empty($row[48])))
								{
									if(!ctype_digit($row[46])) $errors['errors'][] = "Found invalid value in column 'dgrpfunc' at row ".($i+1)." in ItemStatCost.txt.";
									if(empty($tblContent[$row[48]])) $errors['errors'][] = "Cannot find the key '{$row[48]}' in any of uploaded .tbl files. Key found in ItemStatCost.txt in column 'dgrpstrpos' at row " .($i+1);
									if(ctype_digit($row[46]) && !empty($tblContent[$row[48]]))
									{
										$dgrpstrpos = $tblContent[$row[48]];

										$dgrpfunc = (int)$row[46];

										if(strlen($row[47]))
											if(!ctype_digit($row[47]) || (int)$row[47] < 0 || (int)$row[47] > 3) $errors['errors'][] = "Found invalid value in column 'dgrpval' at row ".($i+1)." in ItemStatCost.txt. Set value to 0, 1, 2 or leave it empty.";
											else $dgrpval = (int)$row[47];

										if(!empty($row[50]))
											if(empty($tblContent[$row[50]])) $errors['errors'][] = "Cannot find the key '{$row[50]}' in any of uploaded .tbl files. Key found in ItemStatCost.txt in column 'dgrpstr2' at row " .($i+1);
											else $dgrpstr2 = " ".$tblContent[$row[50]];
									}
								}

								$statList[] = ['Stat' => $row[0], 'descstrpos' => $descstrpos, 'descstr2' => $descstr2 ?? null, 'descfunc' => (int)$row[40], 'descval' => $descval ?? null, 'dgrpstrpos' => $dgrpstrpos ?? null, 'dgrpstr2' => $dgrpstr2 ?? null, 'dgrpfunc' => $dgrpfunc ?? null, 'dgrpval' => $dgrpval ?? null];
							}
						}
					}
				}
			}
		}
		$duplicateErrors = $this->getDuplicateErrors("ItemStatCost.txt", $statList, ['Stat']); 

		$this->setErrors($errors);
		$this->setErrors($duplicateErrors);
		return $statList;
	}
}