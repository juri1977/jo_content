<?php 
namespace JO\JoContent\Domain\Model;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * @scope prototype
 * @entity
 */
class News extends \GeorgRinger\News\Domain\Model\News
{

    /** 
    * @var string
    */
    protected $showheadline;

    /** 
     * @var string
     */
    protected $showsmalldate;

    /** 
    * @var string
    */
    protected $locationname;
	
	/** 
	 *	Name des Ordners auf dem der Newsbeitrag liegt
     *	@var string
     */
    protected $storagename;

    /**
     * @var \DateTime
     */
    protected $datetimeend;

    public function getShowsmalldate()
    {
        return $this->showsmalldate;
    }

    public function setShowsmalldate($showsmalldate)
    {
        $this->showsmalldate = $showsmalldate;
    }

    public function getLocationname()
	{
        return $this->locationname;
    }

    public function getDatetimeend()
	{
        return $this->datetimeend;
    }

    public function getShowheadline()
	{
        return $this->showheadline;
    }
 
	public function getStoragename()
	{
		$joPageObject = NULL;
		$joLanguage = GeneralUtility::makeInstance(Context::class)->getAspect('language');
		if ($this->pid > 0){
			$pageSelect = GeneralUtility::makeInstance('TYPO3\CMS\Core\Domain\Repository\PageRepository');
			$joPageObject = ($joLanguage != 0) ? $pageSelect->getPageOverlay($this->pid, $joLanguage) : $pageSelect->getPage($this->pid);
		} 
        return $joPageObject;
    }
	
	
}