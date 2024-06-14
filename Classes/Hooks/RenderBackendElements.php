<?php
namespace JO\JoContent\Hooks;

use JO\JoContent\Service\FlexFormCeService;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class RenderBackendElements
 * Hook zur Anzeige des Flexform-Inhalts im Backend
 */
class RenderBackendElements implements PageLayoutViewDrawItemHookInterface
{
    /**
     * Rendering der Backend-Elemente bearbeiten
     *
     * @param PageLayoutView $parentObject
     * @param $drawItem
     * @param $headerContent
     * @param $itemContent
     * @param array $row #
     * @return void
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        $backendTemplateFile = GeneralUtility::getFileAbsFileName('EXT:jo_content/Resources/Private/Templates/Backend/' . ucfirst(str_replace('jocontent_', '', $row['CType'] . '.html')));
        if (strpos($row['CType'], 'jocontent_') === 0 && is_file($backendTemplateFile)) {
            /** @var StandaloneView $fluidTemplate */
            $fluidTemplate = GeneralUtility::makeInstance(StandaloneView::class);
            $fluidTemplate->setTemplatePathAndFilename($backendTemplateFile);

            $flexFormService = GeneralUtility::makeInstance(FlexFormCeService::class);
            $fluidTemplate->assign('ce', $flexFormService->parseFlexForm($row['pi_flexform']));
            $itemContent .= $fluidTemplate->render();
            $drawItem = false;
        }
    }
}
