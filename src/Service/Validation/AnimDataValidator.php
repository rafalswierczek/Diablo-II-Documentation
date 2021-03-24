<?php declare(strict_types=1);

namespace App\Service\Validation;

use App\Service\Utils\NotificationHandler;
use App\Service\Enum\{AnimDataEnum, NotificationEnum};

class AnimDataValidator extends TableValidator
{
    protected NotificationHandler $notificationHandler;

	public function __construct(NotificationHandler $notificationHandler)
	{
        $this->notificationHandler = $notificationHandler;
    }

    public function cofNameValid(string $char, string $mode, string $wclass, int $rowIndex): bool
    {
        if(!in_array($char, AnimDataEnum::CHAR) || !in_array($mode, AnimDataEnum::MODE) || !in_array($wclass, AnimDataEnum::WCLASS))
        {
            $this->notificationHandler->addNotification(NotificationEnum::ERROR, 'column.value.invalid', ['columnName' => 'CofName', 'fileName' => AnimDataEnum::FILE_NAME, 'rowIndex' => $rowIndex]);
            return false;
        }

        return true;
    }
}