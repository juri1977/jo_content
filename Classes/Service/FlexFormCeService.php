<?php
namespace JO\JoContent\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormCeService
 */
class FlexFormCeService
{
    /**
     * bereinigtes FlexFormArray
     *
     * @var array
     */
    protected $contentElement = [];

    /**
     * flexForm-Datenbankfeld parsen
     *
     * @param string $flexForm
     * @return array
     */
    public function parseFlexForm($flexForm)
    {
        $flexFormArray = GeneralUtility::xml2array($flexForm);
        $this->recursiveFlexFormArray($flexFormArray);

        return $this->contentElement;
    }

    /**
     * rekursives parsen des FlexForm Arrays
     *
     * @TODO: Tabs in FlexForms
     *
     * @param array $flexFormArray
     * @param string $arrayKey
     * @param int $inlinePosition
     */
    private function recursiveFlexFormArray($flexFormArray, $arrayKey = null, $inlinePosition = null)
    {
        if (is_array($flexFormArray)) {
            foreach ($flexFormArray as $key => $entry) {
                if (strpos($key, 'settings.')) {
                    $key = str_replace('settings.', '', $key);
                }
                if (isset($entry['vDEF'])) {
                    if (null == $arrayKey) {
                        $this->contentElement[$key] = $entry['vDEF'];
                    } else {
                        $this->contentElement[$arrayKey][$inlinePosition][$key] = $entry['vDEF'];
                    }
                } else {
                    if (isset($entry['el'])) {
                        $i = 1;
                        foreach ($entry['el'] as $inlineEntry) {
                            if (is_array($inlineEntry['data']['el'])) $this->recursiveFlexFormArray($inlineEntry['data']['el'], $key, $i);
                            $i++;
                        }
                    } else {
                        if (is_array($entry)) $this->recursiveFlexFormArray($entry, $arrayKey);
                    }
                }
            }
        }
    }
}
