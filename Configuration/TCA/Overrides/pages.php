<?php
defined('TYPO3_MODE') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::registerPageTSConfigFile(
    'jo_content',
    'Configuration/TSconfig/rte_custom.tsconfig',
    'RTE custom settings'
);
