<?php
defined('TYPO3_MODE') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$fields = [
    'showheadline' => [
        'label' => 'Headline Ausrichtung',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['Headline ausblenden', 0],
                ['Headline mittig einblenden', 1],
                ['Headline oben einblenden', 2],
            ]
        ]
    ],
    'datetimeend' => [
        'label' => 'Ende der Veranstaltung',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'size' => 16,
            'eval' => 'datetime,int',
            'default' => 0,
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ]
    ],
    'locationname' => [
        'label' => 'Mit der News oder Veranstaltung verbundener Ort',
        'config' => [
            'type' => 'input',
            'size' => 60,
            'max' => 255,
        ]
    ],
    'showsmalldate' => [
        'label' => 'Datum des Beitrags oben rechts ausblenden',
        'description' => 'Blendet das Datum aus, welches oben rechts vom Bild angezeigt wird',
        'config' => [
            'type' => 'check'
        ]
    ]
];

ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $fields);
ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'showheadline', '', 'after:title');
ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'locationname', '', 'after:showheadline');
ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'datetimeend', '', 'after:datetime');
ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'showsmalldate', '', 'after:datetimeend');
