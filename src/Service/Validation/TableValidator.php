<?php declare(strict_types=1);

namespace App\Service\Validation;

use App\Entity\Table\TableInterface;
use App\Service\Utils\NotificationHandler;
use App\Service\Enum\NotificationEnum;

class TableValidator
{
    protected NotificationHandler $notificationHandler;

	public function __construct(NotificationHandler $notificationHandler)
	{
        $this->notificationHandler = $notificationHandler;
    }

    public function tableNotEmpty(TableInterface $table): bool
    {die('TableValidator tableNotEmpty'); // sprawdzić czy działa poprawnie
        if(empty($table))
        {
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'table.empty', ['fileName' => $table->getFileName()]);
            return false;
        }

        return true;
    }

    public function columnValueNotEmpty(string $columnValue, string $columnName, string $fileName, int $rowIndex): bool
    {
        if(empty($columnValue))
        {
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'column.value.empty', ['columnName' => $columnName, 'fileName' => $fileName, 'rowIndex' => $rowIndex]);
            return false;
        }

        return true;
    }

    /**
     * Check for empty keys in .tbl content passed by first argument
     * 
     * @param array $tblContent all .tbl files data array
     * @param string ...$keys list of .tbl file keys
     * 
     * @return bool return `false` if at least 1 key is not found
     */
    public function keysInTblContent(array $tblContent, string $lang, string ...$keys): bool
    {
        foreach($keys as $key)
            if(empty($tblContent[$key]))
                $noFoundKeys[] = $key;

        if(!empty($noFoundKeys))
        {
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'tblKeys.notFound', ['keys' => implode(', ', $noFoundKeys), 'lang' => $lang]);
            return false;
        }
        
        return true;
    }

    public function columnValueIntInRange(string $columnValue, int $minValue, int $maxValue, string $columnName, string $fileName, int $rowIndex): bool
    {
        $isInt = $this->isInt($columnValue, $columnName, $fileName, $rowIndex);

        if($isInt)
        {
            if($columnValue >= $minValue && $columnValue <= $maxValue)
                return true;
            else
            {
                $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'column.value.outOfRange', ['range' => "$minValue - $maxValue", 'columnName' => $columnName, 'fileName' => $fileName, 'rowIndex' => $rowIndex]);
                return false;
            }
        }
        else
            return false;
    }

    protected function isInt(string $columnValue, string $columnName, string $fileName, int $rowIndex): bool
    {
        if(!ctype_digit($columnValue))
        {
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'column.value.notInt', ['columnName' => $columnName, 'fileName' => $fileName, 'rowIndex' => $rowIndex]);
            return false;
        }

        return true;
    }

    protected function checkFormInputs(array $data, string ...$formNames): ?string
    {
        if(array_diff($formNames, array_keys($data))) // if form has not all necessary fields
            return $this->translator->trans('service.request.missingName');
        return null;
    }

    protected function translateValidationErrors(ConstraintViolationList $constraintViolationList): array
    {
        foreach($constraintViolationList as $error)
            $errors[] = $this->translator->trans($error->getMessage());

        return $errors;
    }
}