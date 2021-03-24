<?php declare(strict_types = 1);

namespace App\Service\Enum;

interface AnimDataEnum
{
    const CHAR = ['AM', 'AI', 'NE', 'BA', 'PA', 'SO', 'DZ'];

    const MODE = ['A1', 'TH'];

    const WCLASS = ['1HS', '2HS', '1HT', '2HT', 'HT1', 'BOW', 'XBW', 'STF'];

    const COLUMN_NAMES = [
        'CofName',
        'FramesPerDirection',
        'AnimationSpeed'
    ];

    const FILE_NAME = 'AnimData.txt';
}