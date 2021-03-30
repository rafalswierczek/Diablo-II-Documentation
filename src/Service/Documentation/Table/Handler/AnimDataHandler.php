<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use App\Entity\Documentation\AttackSpeed;
use App\Entity\Table\AnimData;
use App\Service\Utils\NotificationHandler;
use App\Service\Validation\AnimDataValidator;
use App\Service\DB\Documentation\AttackSpeedIterator;

final class AnimDataHandler extends TableHandler
{
    private AnimDataValidator $animDataValidator;
    private AttackSpeedIterator $attackSpeedIterator;

	public function __construct(
        AnimDataValidator $animDataValidator,
        NotificationHandler $notificationHandler,
        AttackSpeedIterator $attackSpeedIterator
    )
	{
        $this->animDataValidator = $animDataValidator;
        $this->attackSpeedIterator = $attackSpeedIterator;

        parent::__construct($notificationHandler);
    }

    public function getWeaponsAttackSpeed(string $animdataPath, array $commonWeapons, int $skillIAS = 0, int $gearIAS = 0, int $startFrame = 0): bool
    {
        if($this->notificationHandler->hasError())
        {
            $this->notificationHandler->addNotification(NotificationHandler::ERROR, 'attack_speed.cannot');
            return false;
        }
        
        $animData = $this->getAnimData($animdataPath);
        
        foreach($animData as $animDataRow)
        {
            foreach($commonWeapons as $weaponRow)
            {
                $animDataRow['wclass'] = strtoupper($animDataRow['wclass']);
                $weaponRow['wclass'] = strtoupper($weaponRow['wclass']);
                $weaponRow['2handedwclass'] = strtoupper($weaponRow['2handedwclass']);
                
                if($weaponRow['wclass'] === $animDataRow['wclass'] || $weaponRow['2handedwclass'] === $animDataRow['wclass'])
                {
                    $wclass = $weaponRow['wclass'] === $animDataRow['wclass'] ? $weaponRow['wclass'] : $weaponRow['2handedwclass'];

                    $attackSpeed = 25/(ceil(256 * ($animDataRow['fpa'] - $startFrame) / floor($animDataRow['frames'] * (100 + $skillIAS + floor(120 * $gearIAS / (120 + $gearIAS)) - $weaponRow['speed']) / 100)) - 1);
                    
                    $attackSpeedTable[] = (new AttackSpeed())
                        ->setWeaponCode($weaponRow['code'])
                        ->setCharacter($animDataRow['char'])
                        ->setAttackMode($animDataRow['mode'])
                        ->setWclass($wclass)
                        ->setAttackSpeed($attackSpeed);
                }
            }
        }

        $this->attackSpeedIterator->setTable($attackSpeedTable ?? []);

        return true;
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