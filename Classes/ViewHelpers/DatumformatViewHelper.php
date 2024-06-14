<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class DatumformatViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('string', 'DateTime', '');
        $this->registerArgument('formstring', 'string', '');
    }

    public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure, 
		RenderingContextInterface $renderingContext
	) {
        $string = $arguments['string'];
        $formstring = $arguments['formstring'];
        // $string = $string->getTimestamp();
        $string = strftime($formstring,$string);
        if ($GLOBALS['TSFE']->config['config']['language'] == 'de') {
            $string = str_replace('Sunday', 'Sonntag', $string);
            $string = str_replace('Monday', 'Montag', $string);
            $string = str_replace('Tuesday', 'Dienstag', $string);
            $string = str_replace('Wednesday', 'Mittwoch', $string);
            $string = str_replace('Thursday', 'Donnerstag', $string);
            $string = str_replace('Friday', 'Freitag', $string);
            $string = str_replace('Saturday', 'Samstag', $string);
        }
        return $string;
    }
}  