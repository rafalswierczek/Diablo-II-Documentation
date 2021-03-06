<?php declare(strict_types=1);

namespace App\Service\Documentation\Enum;

interface NotificationEnum
{
    const ERROR = 'errors';

    const NOTICE = 'notices';
    
    const SUCCESS = 'successes';
}