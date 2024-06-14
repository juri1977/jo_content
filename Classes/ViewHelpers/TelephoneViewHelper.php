<?php
namespace JO\JoContent\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class TelephoneViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('tel', 'string', 'The tel number that will be put in the href attribute of the rendered link tag', true);
        $this->registerUniversalTagAttributes();
    }

    /**
     * @return string Rendered link
     */
    public function render()
    {
        $tel = $this->arguments['tel'];

        $search = [' ', '-', '|', '/'];
        $tel = str_replace($search, '', $tel);

        $href = 'tel:' . $tel;

        $this->tag->addAttribute('href', $href);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
