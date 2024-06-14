<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IsarrayViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('value', 'mixed', 'checkt if it is array');
    }

    public function render()
    {
        $return = false;
        $value = $this->arguments['value'];
        if (is_array($value)) {
            $return = true;
        }

        return $return;
    }
}
