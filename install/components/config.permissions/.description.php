<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('KOSMOS_CONFIG_PERMISSIONS_DESC_NAME'),
    'DESCRIPTION' => '',
    'PATH' => [
        'ID' => 'kosmos',
        'NAME' => Loc::getMessage('KOSMOS_DESC_GROUP_NAME'),
    ],
    'CACHE_PATH' => 'N',
    'COMPLEX' => 'N',
];