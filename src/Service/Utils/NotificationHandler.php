<?php declare(strict_types=1);

namespace App\Service\Utils;

use Symfony\Contracts\Translation\TranslatorInterface;
use App\Exception\ErrorException;
use App\Service\Enum\{NotificationEnum, TranslationDomainEnum};

final class NotificationHandler implements NotificationEnum
{
    private TranslatorInterface $translator;
    private array $notifications = [];

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function addNotification(string $notificationType, string $translationCode, array $translationParameters = [], string $domain = TranslationDomainEnum::VALIDATION)
    {
        $this->notifications[] = new Notification(
            $notificationType,
            $this->translator->trans(
                $translationCode,
                $translationParameters,
                $domain
            )
        );
    }

    public function addNotifications(array $notifications)
    {
        foreach($notifications as $notification)
            $this->notifications[] = $notification;
    }

    public function getNotifications(?string $type = null): array
    {
        if(strlen($type))
        {
            return array_filter($this->notifications, function($notification) use ($type) {
                return $notification->getType() === $type;
            });
        }
        else
            return $this->notifications;
    }

    public function clearNotifications(?string $type = null)
    {
        if(strlen($type))
        {
            foreach($this->notifications as $index => $notification)
                if($notification->getType() === $type)
                    unset($this->notifications[$index]);
        }
        else
            $this->notifications = [];
    }

    public function hasError(): bool
    {
        foreach($this->notifications as $notification)
            if($notification->getType() === NotificationEnum::ERROR)
                return true;

        return false;
    }

    public function throwIfAnyError()
    {
        if($this->hasError())
            throw new ErrorException();
    }
}