<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use App\Entity\Table\AnimData;
use App\Service\Utils\NotificationHandler;
use App\Service\Validation\AnimDataValidator;
use App\Service\Table\Tools\{TableHandler, TableContainer};

final class AnimDataHandler extends TableHandler
{
    private array $animDataTable;
    private AnimDataValidator $animDataValidator;

	public function __construct(AnimData $animData, AnimDataValidator $animDataValidator, NotificationHandler $notificationHandler, TableContainer $tableContainer)
	{
        $this->animData = $animData;
        $this->animDataValidator = $animDataValidator;
        parent::__construct($notificationHandler, $tableContainer);
    }

    public function getWeaponsAttackSpeed(string $animdataPath, array $commonWeapons, int $skillIAS = 0, int $gearIAS = 0, int $startFrame = 0): ?array
    {
        $attackSpeedTable = [];
        $animData = $this->getAnimData($animdataPath);

        if(!$this->errorHandler->hasErrors())
        {
            foreach($commonWeapons as $weaponRow)
            {
                foreach($animData as $animDataRow)
                {
                    $weaponRow['wclass'] = strtoupper($weaponRow['wclass']);
                    $weaponRow['2handedwclass'] = strtoupper($weaponRow['2handedwclass']);
                    
                    if($weaponRow['wclass'] === $animDataRow['wclass'] || $weaponRow['2handedwclass'] === $animDataRow['wclass'])
                    {
                        $wclass = $weaponRow['wclass'] === $animDataRow['wclass'] ? $weaponRow['wclass'] : $weaponRow['2handedwclass'];
                        $attackSpeed = 25/(ceil(256 * ($animDataRow['fpa'] - $startFrame) / floor($animDataRow['frames'] * (100 + $skillIAS + floor(120 * $gearIAS / (120 + $gearIAS)) - $weaponRow['speed']) / 100)) - 1);
                        $attackSpeedTable[] = ['code' => $weaponRow['code'], 'char' => $animDataRow['char'], 'mode' => $animDataRow['mode'], 'wclass' => $wclass, 'attackSpeed' => $attackSpeed];
                    }
                }
            }
        }
        if(empty($attackSpeedTable)) $errors['errors'][] = "Cannot get weapons attack speed due to previous errors or 'wclass' values from Weapons.txt do not match any values from the third part of 'CofName' column value from AnimData.txt.";

        $this->setErrors($errors);
        $this->setErrors($this->getErrors(), true);
        return $attackSpeedTable;
    }

    private function getAnimData(string $animdataPath): ?AnimData
    {
        $this->processTable($animdataPath, AnimData::FILE_NAME, AnimData::COLUMN_NAMES,
            function(array $row, int $rowIndex, array $columnsData)
            {
                $errors = [];

                foreach(AnimData::COLUMN_NAMES as $columnName)
                    $errors[] = $this->animDataValidator->checkEmptyRowValue($row[$columnsData[$columnName]], 'AnimData.txt', $columnName, ($rowIndex+1));

                $errors[] = $this->animDataValidator->columnValueIntInRange($row[$columnsData['FramesPerDirection']], 0, 256, 'AnimData.txt', 'FramesPerDirection', ($rowIndex+1));
                $errors[] = $this->animDataValidator->checkValid($row[$columnsData['AnimationSpeed']], 0, 1024, 'AnimData.txt', 'AnimationSpeed', ($rowIndex+1));
                
                if(empty($errors))
                {
                    $classCode = substr($row[$columnsData['CofName']], 0, 2);
                    $attackMode = substr($row[$columnsData['CofName']], 2, 2);
                    $wclass = substr($row[$columnsData['CofName']], 4, 3);

                    if($this->animDataValidator->checkCofName($classCode, $attackMode, $wclass, ($rowIndex+1)))
                    {
                        $this->animDataTable[] = (new AnimData)
                            ->setClassCode($classCode)
                            ->setAttackMode($attackMode)
                            ->setWclass($wclass)
                            ->setFramesPerDirection((int)$row[$columnsData['FramesPerDirection']])
                            ->setAnimationSpeed((int)$row[$columnsData['AnimationSpeed']]);
                    }
                }
                else
                    $this->notificationHandler->addNotifications(NotificationHandler::ERROR, $errors);
            }
        );
        
        if($this->animDataValidator->checkEmptyTable($this->animData, 'AnimData.txt'))
        {
            $cofNames = array_map(function($row){ return $row['char'].$row['mode'].$row['wclass'];}, $animData);
            $duplicates = $this->getDuplicateValues($cofNames);

            if(!empty($duplicates))
                $this->errorHandler->setError(['code' => 'table.column.duplicates', 'params' => ['fileName' => 'AnimData.txt', 'columnName' => 'CofName', 'duplicates' => implode(', ', $duplicates)]]);
        }

        return $this->animData;
    }
}