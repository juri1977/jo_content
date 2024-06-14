<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class TimestampconvertViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('formarray', 'array', 'The email address to resolve the gravatar for', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure, 
        RenderingContextInterface $renderingContext
    ) {
        $string = mktime(0, 0, 0, (int) $arguments['formarray']["monat"], (int) $arguments['formarray']["tag"], (int) $arguments['formarray']["jahr"]);
        return $string;
    }
}