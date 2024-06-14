<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class MaxplodeViewHelper extends AbstractViewHelper 
{
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'String to explode', false, null);
        $this->registerArgument('delimiter', 'string', 'Char or string to split the string into pieces. Default is a comma sign(,)', false, ',');
    }

    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    public static function renderStatic(
            array $arguments,
            \Closure $renderChildrenClosure,
            RenderingContextInterface $renderingContext
        ) {
        $subject = $arguments['subject'];
        $delimiter = $arguments['delimiter'];
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
                
        $d = explode($delimiter, $subject);
        $array = [];             
        if (!empty($d)) {
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            foreach($d as $key => $v) {                              
                $b = $resourceFactory->getFileReferenceObject($v);                                
                $array[] = $b;
            }
        }   
        return $array;               
    }
}