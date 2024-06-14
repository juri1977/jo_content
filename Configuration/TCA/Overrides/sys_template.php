<?php
defined('TYPO3_MODE') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile('jo_content', 'Configuration/TypoScript', 'JO CMS (Basis)');
ExtensionManagementUtility::addStaticFile('jo_content', 'Configuration/TypoScript/T_38', 'JO CMS - T-38 (Zusatzdaten für Template 38)');
ExtensionManagementUtility::addStaticFile('jo_content', 'Configuration/TypoScript/T_39', 'JO CMS - T-39 (Zusatzdaten für Template 39)');
