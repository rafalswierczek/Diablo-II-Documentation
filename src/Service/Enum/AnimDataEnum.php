<?php declare(strict_types=1);

namespace App\Service\Enum;

interface AnimDataEnum
{
    const CHAR = ['AM', 'AI', 'NE', 'BA', 'PA', 'SO', 'DZ'];

    const MODE = ['A1', 'TH'];

    const WCLASS = ['1HS', '2HS', '1HT', '2HT', 'HT1', 'BOW', 'XBW', 'STF'];

    /**
     * Header row from FILE_NAME file is composed of column names in specific order from 0 to count(COLUMN_NAMES)-1
     */
    const COLUMN_NAMES = [
        0 => 'CofName',
        1 => 'FramesPerDirection',
        2 => 'AnimationSpeed'
    ];

    const FILE_NAME = 'AnimData.txt';
}