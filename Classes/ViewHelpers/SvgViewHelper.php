<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SvgViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('image', 'object', 'a FAL object (SVG)', false);
        $this->registerArgument('path', 'string', 'Path to (SVG)', false);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string|null
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {

        $image = $arguments['image'];
        $path = $arguments['path'];

        $fullPath = '';
        $output = '';

        if ($image && $image->getExtension() == 'svg') {
            $output = $image->getContents();
        } elseif ($path) {
            $fullPath = GeneralUtility::getFileAbsFileName($path);
            
            if ('' != $fullPath) {
                if (pathinfo($fullPath, PATHINFO_EXTENSION) !== 'svg') {
                    return null;
                }

                try {
                    $output = file_get_contents($fullPath);
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        return $output;
    }
}
