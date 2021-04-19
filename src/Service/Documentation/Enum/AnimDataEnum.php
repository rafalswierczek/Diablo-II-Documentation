<?php declare(strict_types=1);

namespace App\Service\Documentation\Enum;

interface AnimDataEnum
{
    const CHARACTER_CODE = ['AM', 'AI', 'NE', 'BA', 'PA', 'SO', 'DZ'];

    const ATTACK_MODE = ['A1', 'TH'];

    const WCLASS = ['1HS', '2HS', '1HT', '2HT', 'HT1', 'BOW', 'XBW', 'STF'];

    /**
     * Randomly ordered array of column names from header row from FILE_NAME file
     */
    const COLUMN_NAMES = [
        'CofName',
        'FramesPerDirection',
        'AnimationSpeed'
    ];

    const FILE_NAME = 'AnimData.txt';
}