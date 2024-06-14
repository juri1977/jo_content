<?php
if (!defined('TYPO3_MODE')) die('Access denied.');

call_user_func(function ()
{
    // $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:jo_content/Configuration/RTE/Default.yaml';
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['jo_custom'] = 'EXT:jo_content/Configuration/RTE/JoCustom.yaml';

     $configArr = [];
    // Content Configuration auslesen
    /*
     * @var \JO\JoContent\Service\ConfigurationService $configurationService
     */
    $configurationService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\JO\JoContent\Service\ConfigurationService::class);
    $configArr = $configurationService->getConfArray();
    // Plugin fuer jedes Element konfigurieren
    if (is_array($configArr)) {
        foreach ($configArr as $key => $element) {
            $uncached_action =  $element['pluginAction'] . ',download';
            $cached_action =  $element['pluginAction'] . ',download';

            if (!isset($element['uncached_action'])) {
                $uncached_action = '';
            }

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
                'JO.JoContent',
                $element['pluginName'],
                [\JO\JoContent\Controller\ContentController::class => $cached_action],
                [\JO\JoContent\Controller\ContentController::class => $uncached_action],
                \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
            );
        }
    }

    // Rendering hook of content elements in backend
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = \JO\JoContent\Hooks\RenderBackendElements::class;

    /*
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:jo_content/Configuration/PageTS/setup.tsconfig">'
    );
    */

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import \'EXT:jo_content/Configuration/TSconfig/setup.tsconfig\'');

    // NEWS EID
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['retrievedata'] = \JO\JoContent\Controller\EidController::class . '::processRequest';

    // $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News']['jo_content'] = 'jo_content';
    // $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'jo_content';
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(\GeorgRinger\News\Domain\Model\News::class, \JO\JoContent\Domain\Model\News::class);

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'jo_content-extension',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:jo_content/Resources/Public/Icons/Extension.svg']
    );

    // tx_jomuseo_pi1009[joDetailView],tx_jomuseo_pi1009[jopaginatepage],tx_jomuseo_pi1009[startfrom],tx_jomuseo_pi1009[h]

    
    $custom_list = [
        /*
        'tx_jocontent_c11[detail]',
        'tx_jocontent_c11[detailview]',
        'tx_jocontent_c11[tp]',
        '^tx_jomuseo_pi1009[',
        */
        '^tx_jomuseo_pi2011[',
        '^tx_jomuseo_pi1091[',
        '^tx_jomuseo_pi1010[',
        '^tx_jomuseo_pi2010[',
        '^tx_jomuseo_api[',
        '^tx_jomuseo_soloshow[',
        '^tx_web2pdf_pi1[',
        'ceid',
        'v',
        'oa',
        'ret',
        'add',
        'rs',
        'del',
        'gnd'
    ];
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'], $custom_list);
    
    
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class, \JO\JoContent\Domain\Model\FileReference::class);
    
    /*
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
        ->registerImplementation(
            \TYPO3\CMS\Extbase\Domain\Model\FileReference::class,
            \JO\JoMuseo\Domain\Model\FileReference::class
        );
        */

});
