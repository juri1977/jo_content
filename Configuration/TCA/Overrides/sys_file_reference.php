<?php
defined('TYPO3_MODE') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$tempColumns = [
    'description' => [
        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.description',
        'config' => [
            'type' => 'text',
            'enableRichtext' => true
        ]
    ],
    'poster' => [
        'label' => 'Poster',
        'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
            'poster',
            [
                'maxitems' => 1,
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
    ],
    'licence' => [
        'label' => 'Lizenz',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],
    'rightsowner' => [
        'label' => 'Rechteinhaber',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],
    'originator' => [
        'label' => 'Urheber',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns(
    'sys_file_reference',
    $tempColumns,
    1
);

// poster der video palette hinzufügen
ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_reference',
    'videoOverlayPalette',
    '--linebreak--,poster,', 'after:autoplay'
);

ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_reference',
    'imageoverlayPalette',
    '--linebreak--,licence,rightsowner,originator', 'after:title'
);
