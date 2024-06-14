<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

use \JO\JoContent\Service\ConfigurationService as joConfigurationService;

call_user_func(function()
{
    /*
     * @var \JO\JoContent\Service\ConfigurationService $configurationService
     */
    $configurationService = GeneralUtility::makeInstance(joConfigurationService::class);
    $configArr = $configurationService->getConfArray();

    if (!empty($configArr)) {
        foreach ($configArr as $ctype => $element) {
            // '.' aus TypoScript Pfad entfernen
            $ctype = str_replace('.', '', $ctype);

            // Plugin fuer jedes Element registrieren
            ExtensionUtility::registerPlugin('JO.JoContent', $element['pluginName'], $element['pluginTitle']);

            // TCA aus "header"-Element nutzen und an das Element anpassen (subheader entfernen und flexform hinzufuegen)
            $GLOBALS['TCA']['tt_content']['types']['jocontent_' . $ctype]['showitem'] =
                str_replace(';headers,', ';headers,pi_flexform;LLL:typo3conf/ext/jo_content/Resources/Private/Language/locallang.xlf:' . $element['tab'] . ',', $GLOBALS['TCA']['tt_content']['types']['header']['showitem']);

            // Header entfernen, wenn dieser im Element nicht benoetigt wird
            if (!empty($element['removeHeader'])) {
                $GLOBALS['TCA']['tt_content']['types']['jocontent_' . $ctype]['showitem'] =
                    str_replace('--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,', '', $GLOBALS['TCA']['tt_content']['types'][$ctype]['showitem']);
            }
            ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:jo_content/Configuration/FlexForms/' . $ctype . '.xml', 'jocontent_' . $ctype);
        }
    }
});
