<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class AppendToArrayViewHelper extends AbstractViewHelper  
{
    public function initializeArguments()
    {
        $this->registerArgument('array', 'array', 'All collected arrays', false, []);
        $this->registerArgument('append', 'array', 'To fill up the container', false, []);
    }

	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	) {
		$array = $arguments['array'];
		$append = $arguments['append'];
		if (!empty($append)) $array[] = $append;
		return $array;
	}
}