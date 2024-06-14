<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class ExplodeViewHelper
 *
 * @package JO\JoContent\ViewHelpers
 */
class ExplodeViewHelper extends AbstractViewHelper implements CompilableInterface
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'String to explode', false, null);
        $this->registerArgument('delimiter', 'string', 'Char or string to split the string into pieces. Default is a comma sign(,)', false, ',');
        $this->registerArgument('removeEmpty', 'bool', 'If TRUE empty items will be removed', false, true);
    }

    /**
     * Splits a string to an array.
     *
     * @return array Exploded parts
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return array
     */
    public static function renderStatic(
            array $arguments,
            \Closure $renderChildrenClosure,
            RenderingContextInterface $renderingContext
        ) {
        $subject = $arguments['subject'];
        $delimiter = $arguments['delimiter'];
        $removeEmpty = (bool)$arguments['removeEmpty'];

        if ($subject === null) $subject = $renderChildrenClosure();

        switch ($delimiter) {
            case '\n':
                $delimiter = "\n";
                break;
            case '\r':
                $delimiter = "\r";
                break;
            case '\r\n':
                $delimiter = "\r\n";
                break;
            case '\t':
                $delimiter = "\t";
                break;
        }

        return GeneralUtility::trimExplode($delimiter, $subject, $removeEmpty);

        
    }
}
