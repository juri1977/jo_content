<?php
declare(strict_types = 1);
namespace JO\JoContent\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class EidController
 */
class EidController
{

	/**
	 * @var string 
	 */
	protected $name = '';

	/**
	 * @var array
	 */
	protected $responseArray = [
	    'hasErrors' => false,
	    'message' => ''
	];

	/**
     *  @TYPO3\CMS\Extbase\Annotation\Inject
     *  @var  \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;



	/**
	 * @param ServerRequestInterface $request
	 * @return void
	 */
	protected function initializeData(ServerRequestInterface $request)
	{
	    if (!isset($request->getQueryParams()['name']) || !$name = (string)$request->getQueryParams()['name']) throw new \InvalidArgumentException('No message passed!', 1543419903286);
	    $this->name = $name;
	}
	/**
	 * @return void
	 */
	protected function initializeLanguage()
	{
	    if (!isset($GLOBALS['LANG']) || !\is_object($GLOBALS['LANG'])) {
	        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class);
	        $GLOBALS['LANG']->create('default');
	    }
	}

	/**
	 * Add a new comment
	 *
	 * @return void
	 */
	protected function getdatesAction()
	{
		//$cc = $this->newsRepository->findByUid(11);
		$this->responseArray['hasErrors'] = false;
	   // $this->responseArray['message'] = $this->translate('message.could_not_add_comment');
		$this->responseArray['message'] = ["hallo"];
	    
	 
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function translate(string $key): string
	{
	    return $GLOBALS['LANG']->sL('LLL:EXT:tutorial/Resources/Private/Language/locallang.xlf:' . $key);
	}

	/**
	 * @param ResponseInterface $response
	 * @return void
	 */
	protected function prepareResponse(ResponseInterface &$response)
	{
	    $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
	    $response->getBody()->write(\json_encode($this->responseArray));
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function processRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
	    $this->initializeLanguage();
	    $this->initializeData($request);

	    switch (isset($request->getQueryParams()['action']) ? (string)$request->getQueryParams()['action'] : '') {
	        case 'getDates':
	            $this->getdatesAction();
	            break;
	        default:
	            throw new \UnexpectedValueException('No or unknown action passed!', 1543418482439);
	    }
	    $this->prepareResponse($response);
	    return $response;
	}	
}