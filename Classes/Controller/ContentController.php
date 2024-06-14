<?php
namespace JO\JoContent\Controller;

use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

use JO\JoContent\Domain\Repository\FederalstateRepository;
use JO\JoMuseo\Domain\Repository\InstituteRepository;
use JO\JoContent\Domain\Repository\InstitutRepository as ContentInstituteRepository;
use JO\JoContent\Domain\Repository\PersonRepository;
use JO\JoContent\Domain\Repository\FileReferenceRepository;
use JO\JoContent\Domain\Repository\NewsRepository as JoNewsRepository;

/**
 * Class ContentController
 */
class ContentController extends ActionController
{
    protected $orderByAllowed = 'sorting,author,uid,title,teaser,author,tstamp,crdate,datetime,categories.title';

     public $searcharray = []; // Array der Sucheingaben die in einer Sessn zwischengespeichert werden - kommt aus der FEusersession

    /**
     *  @var  \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;

    /**
     *  @var  PersonRepository
     */
    protected $personRepository;

    public function injectPersonRepository(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    /**
     *  @var  FederalstateRepository
     */
    protected $federalstateRepository;

    public function injectFederalstateRepository(FederalstateRepository $federalstateRepository)
    {
        $this->federalstateRepository = $federalstateRepository;
    }

    /**
     *  @var  InstituteRepository
     */
    protected $instituteRepository;

    public function injectInstituteRepository(InstituteRepository $instituteRepository)
    {
        $this->instituteRepository = $instituteRepository;
    }

    /**
     *  @var  ContentInstituteRepository
     */
    protected $contentInstituteRepository;

    public function injectContentInstituteRepository(ContentInstituteRepository $contentInstituteRepository)
    {
        $this->contentInstituteRepository = $contentInstituteRepository;
    }

    /**
     *  @var  FileReferenceRepository
     */
    protected $fileReferenceRepository;

    public function injectFileReferenceRepository(FileReferenceRepository $fileReferenceRepository)
    {
        $this->fileReferenceRepository = $fileReferenceRepository;
    }

    /**
     *  @var  JoNewsRepository
     */
    protected $joNewsRepository;

    public function injectJoNewsRepository(JoNewsRepository $joNewsRepository)
    {
        $this->joNewsRepository = $joNewsRepository;
    }

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function initializeAction()
    {
        if (isset($this->setting['orderByAllowed'])) $this->orderByAllowed = $this->setting['orderByAllowed'];
    }

    /**
      * Initialize the view
      *
      * @return void
      */
    protected function initializeView($view) : void
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $typoscript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT, 'sitepackage');
        
        $langPath = 'LLL:EXT:jo_museo/Resources/Private/Language/locallang.xlf';

        if (isset($typoscript['plugin.']['tx_jomuseo.']['settings.']['langPath'])) {
            $langPath = $typoscript['plugin.']['tx_jomuseo.']['settings.']['langPath'];
        }

        $this->view->assign('langPath', $langPath);
    }

    public function setSessionName()
    {
        // Content Objekt ermitteln und aktuelle Page UID ermitteln
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObj = $configurationManager->getContentObject();
        $content_element_uid = $contentObj->data['uid'];
        $this->joSessionvarName = "museosearch_" . $GLOBALS['TSFE']->id . "_" . $content_element_uid;
        return $this;
    }

    public function buildSearchFilters($joSearcharraycomplete = [])
    {
        $joSearcharraycomplete = $this->deleteSearcharray($joSearcharraycomplete);
        return $joSearcharraycomplete;
    }

    public function addValue($session_name = NULL, $session_value = NULL)
    {
        $session_items = [];
        if ($session_name != NULL && $session_value != NULL) {
            $session_items = $this->getSessionValues($session_name);
            $session_items = array_filter($this->joAddToArrayAndMakeUnique($session_items, $session_value));
            $_SESSION[$session_name] = $session_items;
        }
        return $session_items;
    }
    
    public function removeValue($session_name = NULL, $session_value = NULL)
    {
        $session_items = [];
        if ($session_name != NULL && $session_value != NULL) {
            $session_items = $this->getSessionValues($session_name);
            $session_items = array_filter($this->joEliminateArrayValueAndKey($session_items, $session_value));
            if (count($session_items) > 0) {
                $_SESSION[$session_name] = $session_items;
            } else {
                 $this->emptySession($session_name);
            }
        }
        return $session_items;
    }

    /**
     *  Entfernt einen spezifischen Wert aus einem gegebenen Array
     *
     *  @var array $joArray -> Array aus dem der entsprechenden Wert entfernt werden soll
     *  @var string $joElementToDelete -> Element, das entfernt werden soll
     *
     *  @return Array -> bereinigtes Array
     */
    public function joEliminateArrayValueAndKey($joArray = [], $joElementToDelete = "")
    {
        if (!empty($joArray) && !empty($joElementToDelete)) {
            $joKeyToDelete = array_search($joElementToDelete, $joArray);
            if ($joKeyToDelete !== FALSE) {
                unset($joArray[$joKeyToDelete]);
                $joArray = array_values($joArray);
            }
        }
        return $joArray;
    }
    
    /**
     *  Fügt einem gegebenen Array einen Wert hinzu und macht es unique
     *
     *  @var array $joArray -> Array aus dem der entsprechenden Wert hinzugefügt werden soll
     *  @var string $joElementToAdd -> Element, das hinzugefügt werden soll
     *
     *  @return Array -> bereinigtes Array
     */
    public function joAddToArrayAndMakeUnique($joArray = [], $joElementToAdd = "")
    {
        if (empty($joArray)) {
            $joArray = [];
        }
        if (!empty($joElementToAdd)) {
            array_push($joArray, $joElementToAdd);
            $joArray = array_unique($joArray);
            $joArray = array_values($joArray);
        }
        return $joArray;
    }

    public function getSessionValues($session_name = null)
    {
        $session_items = [];
        if ($session_name != null) {
            if (!session_id()) @ session_start();
            if ($_SESSION[$session_name]) {
                $session_items = $_SESSION[$session_name];
            }
        }
        return $session_items;
    }
    
    public function emptySession($session_name = null)
    {
        if ($session_name != null) {
            if (!session_id()) @ session_start();
            unset($_SESSION[$session_name]);
        }
    }
    
    public function replaceAllValues($session_name = null, $session_value = null)
    {
        if ($session_name != null) {
            if (!session_id()) @ session_start();
            $_SESSION[$session_name] = $session_value;
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function getFileObject($name)
    {
        $cObj = $this->configurationManager->getContentObject();
        $language = $this->request->getAttribute('language');
        $language_uid = $language->getLanguageId();
        $uid = (int) $cObj->data['uid'];
        if($language_uid > 0){
             $uid = (int) $cObj->data['_LOCALIZED_UID'];
        }
        $castName = $name;
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $fileObjects = $fileRepository->findByRelation('tt_content', $castName, $uid);

        return $fileObjects;
    }

    public function getAssets($uidarray = [])
    {
        $return = [];
        if ($uidarray && !empty($uidarray)) {
            /** @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            foreach ($uidarray as $value) {
                try {
                    if ($resourceFactory->getFileReferenceObject($value)) {
                        $fileReference = $resourceFactory->getFileReferenceObject($value);
                        $return[] = $fileReference->getProperties();
                    }
                } catch (\Exception $e) {}
            }
        }
        return $return;
    }


    /**
     *    Anzahl der Treffer an das Template geben
     *    Pagination ermitteln
     */
    public function MakePagination($numberfound, $limit, $PaginatePagesShow, $extbaseaction, $paginatecenter)
    {
        $joPaginateDataArray = [];
        if ($limit > 0) {
            $joAnzahlPages = ceil($numberfound / $limit);
            $joStartSequenz = 1; //    An welcher Stelle beginnt die Pagination
            $joEndSequenz = $joAnzahlPages; //    Letzte Paginatorseite
            $joPaginatePagesShowHalf = $PaginatePagesShow / 2; //    Die Hälfte der anzuzeigenden Paginationsdaten

            if ($PaginatePagesShow < $joAnzahlPages) {
                $joStartSequenz = $paginatecenter - $joPaginatePagesShowHalf;
                $joEndSequenz = $paginatecenter + $joPaginatePagesShowHalf;

                if ($joStartSequenz <= 0) {
                    $joStartOffset = abs($joStartSequenz);
                    $joStartSequenz = 1;
                }
                if ($joEndSequenz > $joAnzahlPages) {
                    $joEndOffset = abs($joAnzahlPages - $joEndSequenz);
                    $joEndSequenz = $joAnzahlPages;
                }
                $joStartSequenz = $joStartSequenz - $joEndOffset;
                $joEndSequenz = $joEndSequenz + $joStartOffset;
            }

            /**
             *    Relevante Daten sammeln und an Template geben
             */
            $joRangeArray = range($joStartSequenz, $joEndSequenz);
            $joPaginateDataArray["action"] = $extbaseaction;
            $joPaginateDataArray["range"] = $joRangeArray;
            $joPaginateDataArray["aktiv"] = $paginatecenter;

            $joPaginateDataArray["links"] = $paginatecenter - 1;
            if ($joPaginateDataArray["links"] < 1) {
                $joPaginateDataArray["links"] = 1;
            }

            $joPaginateDataArray["rechts"] = $paginatecenter + 1;
            if ($joPaginateDataArray["rechts"] > $joEndSequenz) {
                $joPaginateDataArray["rechts"] = $joEndSequenz;
            }

            if (1 != $joRangeArray[0]) {
                $joPaginateDataArray["first"] = 1;
            }

            if ($joRangeArray[(count($joRangeArray) - 1)] != $joAnzahlPages) {
                $joPaginateDataArray["last"] = $joAnzahlPages;
            }

            $joPaginateDataArray["numbershownfrom"] = $paginatecenter * $limit;
            $joPaginateDataArray["numbershowntill"] = ($paginatecenter * $limit) + $limit;
            if ($joPaginateDataArray["numbershowntill"] > $numberfound) {
                $joPaginateDataArray["numbershowntill"] = $numberfound;
            }

            $joPaginateDataArray["numbershown"] = $joPaginateDataArray["numbershowntill"] - $joPaginateDataArray["numbershownfrom"];
        }
        return $joPaginateDataArray;
    }

    /**
     *    Ausfruf erfolgt hierbei im controlleraction
     *    $this->addHeaderFile('jo.ce001.css');
     *    $this->addHeaderFile('jo.ce001.js');
     * @param string $fileName
     * @param string $publicPath
     * @return void
     */
    public function addHeaderFile($fileName, $publicPath = 'Resources/Public/')
    {
        $filepath = '';

        $asset = GeneralUtility::makeInstance(AssetCollector::class);
        $extkey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionKey());

        $extPath = ExtensionManagementUtility::extPath($extkey) . $publicPath;
        // $extPath = GeneralUtility::getFileAbsFileName('EXT:' . $extkey . '/' . $publicPath);
        // $extPath = PathUtility::getAbsoluteWebPath($extPath);

        // Dateipfad je nach Endung setzen
        $pi = pathinfo($fileName);
        switch (true) {
            case (strtolower($pi['extension']) == 'css'):
                $filepath = ('Resources/Public/' != $publicPath) ? $extPath . $fileName : $extPath . 'Css/' . $fileName;
                //if (is_file($filepath)) {
                // $pageRender->addCssFile($filepath, 'stylesheet', 'all', '', false, false, '', true);
                $asset->addStyleSheet($fileName, $filepath);
                //}

                break;
            case (strtolower($pi['extension']) == 'js'):
                $filepath = ('Resources/Public/' != $publicPath) ? $extPath . $fileName : $extPath . 'JavaScript/' . $fileName;
                //if (is_file($filepath)) {
                // $pageRender->addJsFooterFile($filepath, '', false, false, '', true);
                $asset->addJavaScript($fileName, $filepath);
                //}

                break;
        }
    }

    public function getTreeList($id, $depth, $begin = 0, $permClause = '')
    {
        $depth = (int) $depth;
        $begin = (int) $begin;
        $id = (int) $id;
        if ($id < 0) $id = abs($id);
        $theList = ($begin === 0) ? $id : '';

        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                )
                ->orderBy('uid');
            if ($permClause !== '') $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permClause));
            $statement = $queryBuilder->execute();
            while ($row = $statement->fetchAssociative()) {
                if ($begin <= 0) $theList .= ',' . $row['uid'];
                if ($depth > 1) {
                    $theSubList = $this->getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) $theList .= ',';
                    $theList .= $theSubList;
                }
            }
        }
        return $theList;
    }

    /**
     * ce001 action
     */
    public function ce001Action()
    {
        $this->view->assign('images', $this->getFileObject('image'));
        $this->addHeaderFile('jo.ce001.css');
    }

    /**
     * c01 action
     */
    public function c01Action()
    {
        $this->view->assign('images', $this->getFileObject('header_image'));
    }

    /**
     * c02 action
     */
    public function c02Action()
    {
        $this->addHeaderFile('ol.min.css');
        $this->addHeaderFile('ol.min.js');
        $this->addHeaderFile('map.js');
        $this->addHeaderFile('jo.ce02.css');

        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $fileReference = $resourceFactory->getFileReferenceObject($this->settings['datafile']);
        $i = 0;

        $features = [];
        $coordline = [];

        $handle = fopen('fileadmin/' . $fileReference->getOriginalFile()->getIdentifier(), "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $values = explode(';', $line);
                $coord = explode(',', $values[4]);
                $coordinates = [
                    floatval($coord[1]),
                    floatval($coord[0]),
                ];
                $coordline[$i] = $coordinates;
                $geometry = (object) [
                    "type" => "Point",
                    "coordinates" => $coordinates
                ];
                $feature = [
                    "type" => "Feature",
                    "geometry" => $geometry,
                    "properties" => (object) [
                        "name" => "" . htmlentities($values[1]) . ", " . htmlentities($values[2]) . " (" . htmlentities($values[3]) . ")"
                    ]
                ];
                $features[$i] = $feature;
                $i++;
            }
        }

        $point_array = [
            "type" => "FeatureCollection",
            "features" => $features
        ];
        fclose($handle);
        $linefeatures = [];
        $linegeometry = [
            "type" => "LineString",
            "coordinates" => $coordline
        ];
        $linefeature = [
            "type" => "Feature",
            "geometry" => $linegeometry
        ];
        $linefeatures[0] = $linefeature;
        $line_array = [
            "type" => "FeatureCollection",
            "features" => $linefeatures
        ];
        $this->response->addAdditionalHeaderData(
            "<script>
            var lines = '" . json_encode($line_array) . "';
            var clusters = '" . json_encode($point_array) . "';
            var linecolor = '" . $this->settings['linecolor'] . "';
            var linewidth = '" . $this->settings['linewidth'] . "';
            var pointcolor = '" . $this->settings['pointcolor'] . "';
            var clustercolor = '" . $this->settings['clustercolor'] . "';
            </script>"
        );
    }

    /**
     * c03 action
     */
    public function c03Action() {}

    /**
     * c04 action
     */
    public function c04Action()
    {
        $imgList = (isset($this->settings['images'])) ? $this->settings['images'] : '';
        $imgArray = explode(',', $imgList);
        $fileArray = [];
        $col = "";
        $count = 0;
        if ($imgArray && !empty($imgArray)) {
            /** @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            foreach ($imgArray as $value) {
                /*
                echo "<pre>";
                print_r(get_class_methods($resourceFactory));
                echo "</pre>";
                 */
                try {
                    if ($resourceFactory->getFileReferenceObject($value)) {
                        $fileReference = $resourceFactory->getFileReferenceObject($value);
                        $fileArray[] = $fileReference->getProperties();
                        $count++;
                    }
                } catch (\Exception $e) {}

                //\TYPO3\CMS\Core\Utility\DebugUtility::debug($fileReference);
            }
        }
        if ($count > 0) {
            switch ($count) {
                case 2:
                    $col = "col-md-6 fsTeaserImage";
                    break;
                case 3:
                    $col = "col-md-4 fsTeaserImage";
                    break;
                case 4:
                    $col = "col-md-3 fsTeaserImage";
                    break;
                default:
                    $col = "col-md-12 fsTeaserImage";
            }
        }

        $textlayout = (isset($this->settings['textlayout'])) ? $this->settings['textlayout'] : '';
        $textlayoutclass = "";

        if (!empty($textlayout)) {
            switch ($textlayout) {
                case 2:
                    $textlayoutclass = "fsBottomRight";
                    break;
                case 3:
                    $textlayoutclass = "fsTopLeft";
                    break;
                case 4:
                    $textlayoutclass = "fsTopRight";
                    break;
                default:
                    $textlayoutclass = "fsBottomLeft";
            }
        }

        $textblock = (isset($this->settings['textblock'])) ? $this->settings['textblock'] : '';
        $textposition = (isset($this->settings['textposition'])) ? $this->settings['textposition'] : '';
        $textabstand = (isset($this->settings['textabstand'])) ? $this->settings['textabstand'] : '';
        $textpadding = "";

        if (!empty($textabstand)) {
            switch ($textabstand) {
                case 2:
                    $textpadding = "fsTextblock_medium";
                    break;
                case 3:
                    $textpadding = "fsTextblock_large";
                    break;
                default:
                    $textpadding = "fsTextblock_small";
            }
        }

        $data = [
            "imagesnew" => $fileArray,
            "col" => $col,
            "layoutclass" => $textlayoutclass,
            "textblock" => $textblock,
            "textposition" => $textposition,
            "textpadding" => $textpadding
        ];

        $this->view->assign('data', $data);
    }

    /**
     * Münzkabinett
     */
    public function c05Action()
    {
        
    }

    /**
     * c06 action
     */
    public function c06Action()
    {

    }

    /**
     * c07 action
     */
    public function c07Action()
    {
        $this->addHeaderFile('jo.ce07.css');
    }

    /**
     * c08 action
     */
    public function c08Action()
    {
        // klaus Karte
        // echo "sss";
    }

    /**
     * c09 action
     */
    public function c09Action()
    {
        if ($this->settings['image']) {
            $field = 'image';
            $images = $this->getFileObject($field);
            $this->view->assign('images', $images);
        }
    }

    public function getContentElementData(){
        // Content Objekt ermitteln und aktuelle Page UID ermitteln
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObj = $configurationManager->getContentObject();
        return $contentObj;
    }

    /**
     *  c10 action - stable
     *  Events, die auf Basis der JSON-LD/Schema.org - API ausgelesen werden
     */
    public function c10Action()
    {        
        // Initial wird die Liste der Events ausgegeben
        $subaction = 'list';
        // Der Tag vor dem aktuellen Tag - notwendig für die Archivfunktion
        $yesterday = null;
        // Standardmäßig kann man die Monate durchklicken
        $shownext = true;
        // Veranstaltungsfilter
        $orgother = [];
        // Zeitfilter
        $timeother = [];
        $assign = [];
        $requestparams = [];
        $content = [];
        $activefilter = [];
        $currentday = time();
        $today = time();
        // Daten des Contentelements ermitteln
        $ce = $this->getContentElementData();
        $assign['ce'] = $ce->data;
        switch ($this->settings['mode']) {
            // Archiv
            case '2':
                $yesterday = strtotime("-1 days");
                $subaction = 'archiv';
                break;
        }

        if ($this->request->hasArgument('startdate')) {
            $today = intval($this->request->getArgument('startdate'));
        }

        if ($this->request->hasArgument('actyear')) {
            $activefilter['actyear'] = intval($this->request->getArgument('actyear'));
        }

        if ($this->request->hasArgument('tabact')) {
            $activefilter['tabact'] = intval($this->request->getArgument('tabact'));
        }

        $lastdayofthemonth = date('t', $today);
        // Wenn die Archivfunktion gewünscht ist, soll nur bis zum gestrigen Tag gescrollt werden können
        if($yesterday != null){
            // der Pfeil nach rechts wird nicht angezeigt - wenn das Archiv aktiviert ist, wird nur bis zum gestrigen Tag alles angezeigt und man kann nicht in den nächsten Monat scrollen
            if(!$this->settings['currentyear'] && strftime('%m', $today) == strftime('%m', $yesterday)){
                $shownext = false;
                $today = $yesterday;
                $lastdayofthemonth = date('d', $today);
            }
        }

        $startdate = strftime('%Y-%m-01', $today);
        $startdatesearch = strftime('%Y%m01', $today);
        $startdatenominus = strftime('%Y%m01', $today);
        $monthnumber = strftime('%m', $today);
        $monthnumbernotrailingzero = date('n', $today);
        $startyear = strftime('%Y', $today);
        $nextyear = $startyear;
        $prevyear = $startyear;
        $prevmonthname = strftime('%B', strtotime('-1 month', $today));
        $startmonthname = strftime('%B', $today);
        $nextmonthname = strftime('%B', strtotime('+1 month', $today));
        $prevmonthnameshort = strftime('%b', strtotime('-1 month', $today));
        $startmonthnameshort = strftime('%b', $today);
        $nextmonthnameshort = strftime('%b', strtotime('+1 month', $today));
        $nextmonthnumber = $monthnumbernotrailingzero + 1;
        $prevmonthnumber = $monthnumbernotrailingzero - 1;
        if (12 == $monthnumbernotrailingzero) {
            $nextyear = $startyear + 1;
            $nextmonthnumber = 1;
        }
        if (1 == $monthnumbernotrailingzero) {
            $prevyear = $startyear - 1;
            $prevmonthnumber = 12;
        }
        $nextmonthtimestamp = mktime(0, 0, 0, $nextmonthnumber, 1, $nextyear);
        $prevmonthtimestamp = mktime(0, 0, 0, $prevmonthnumber, 1, $prevyear);
        $thismonthtimestamp = mktime(0, 0, 0, $monthnumbernotrailingzero, 1, $startyear);

        $dateconfig = [
            'startdate' => $startdate,
            'startdatesearch' => $startdatesearch,
            'currentday' => strftime('%Y%m%d', $currentday),
            'enddate' => strftime('%Y-%m-' . $lastdayofthemonth, $today),
            'enddatesearch' => strftime('%Y%m' . $lastdayofthemonth, $today),
            'days' => $lastdayofthemonth,
            'startyear' => $startyear,
            'nextyear' => $nextyear,
            'prevyear' => $prevyear,
            'currentyear' => strftime('%Y', $currentday),
            'prevmonthname' => $prevmonthname,
            'startmonthname' => $startmonthname,
            'nextmonthname' => $nextmonthname,
            'prevmonthnameshort' => $prevmonthnameshort,
            'startmonthnameshort' => $startmonthnameshort,
            'nextmonthnameshort' => $nextmonthnameshort,
            'prevmonthtimestamp' => $prevmonthtimestamp,
            'thismonthtimestamp' => $thismonthtimestamp,
            'nextmonthtimestamp' => $nextmonthtimestamp,
            'currentmonthnumber' => $monthnumbernotrailingzero,
            'startdatenominus' => $startdatenominus,
            'yesterday' => $yesterday,
            'shownext' => $shownext
        ];
        // Request-Uri bauen
        $requestparams = [
            'apiKey' => $this->settings['apikey'],
            'type' => '2328',
            'style' => 'json-ld',
            'search' => 'performance'
        ];

        if ($this->settings['currentyear']) $dateconfig['currentyear'] = $this->settings['currentyear'];

        // Suche nach einem Event zu einer bestimmten Spielzeit
        if ($this->request->hasArgument('detail')) $subaction = 'detail';

        if ($this->request->hasArgument('orgother')){
            $orgother =  $this->request->getArgument('orgother');
            if (!is_array($orgother)) $orgother = array($orgother);
        }
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $orgother );
        if ($this->request->hasArgument('orgid')){
            $orgid = intval($this->request->getArgument('orgid'));
            $activefilter['veranstalter']['clicked'] = $orgid;
            if(!is_array($orgid)) $orgid = array($orgid);
            $orgother = array_merge($orgother, $orgid);
        }
        if ($this->request->hasArgument('removeorgid')){
            $removeorgid = intval($this->request->getArgument('removeorgid'));
            $orgother = array_diff($orgother, array($removeorgid));
        }
        
        $orgother = filter_var_array($orgother, FILTER_SANITIZE_NUMBER_INT);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $orgid );
        $requestparams['by_veranstalter_ids'] = $orgother;

        
        
        $activefilter['veranstalter']['connected'] = $orgother;
        
       // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $activefilter );

        switch ($subaction) {
            case 'list':
                $requestparams['start_date'] = $dateconfig['startdate'];
                $requestparams['end_date'] = $dateconfig['enddate'];
                break;

            case 'archiv':
                $requestparams['start_date'] = $dateconfig['startdate'];
                $requestparams['end_date'] = $dateconfig['enddate'];
                break; 

            case 'detail':
                $requestparams['by_spielzeit_ids'] = [intval($this->request->getArgument('detail'))];
                break;
        }
        $requestparamsConcat = http_build_query($requestparams);

        $url = "?" . $requestparamsConcat;
        if ($this->settings['url']) $url = $this->settings['url'] . $requestparamsConcat;

        $additionalOptions = [
            'allow_redirects' => true,
            'auth' => [
                'user',
                'justorange'
            ]
        ];
        $response = $this->requestFactory->request($url, 'GET', $additionalOptions);
        if ($response->getStatusCode() === 200) {
            if ($response->getHeaderLine('Content-Type') === 'application/json') {
                $content = $response->getBody()->getContents();
                $content = json_decode($content, true);
                $content = $this->reorderDates($content);
                $content['configuration']['dateconfig'] = $dateconfig;
                $assign['items'] = $content;
            }
        }
        // Zusätzliche Filter für die Archivfunktion hinzufügen: $this->settings['mode']: 2
        if ($this->settings['mode'] == 2) {
            if ($this->settings['filter']) {
                $filter_array = explode(',', $this->settings['filter']);
                if (!empty($filter_array)) {
                    $filter = [];
                    foreach ($filter_array as $val) {
                        if ($val == 1) {
                            $u = '?type=2328&search=veranstalter';
                            $filter =  json_decode(file_get_contents($u),true);
                        }
                    }
                }
            }
            if($this->settings['years']){
                $years_array = range($dateconfig['currentyear'] - intval($this->settings['years']), $dateconfig['currentyear']);
                if(!empty($years_array)){
                    $filter['year'] = [];
                    foreach($years_array as $val){
                        $filter['year'][] = [
                            'id' => mktime(0, 0, 0, 1, 1,$val),
                            'name' => $val
                        ];
                    }
                }
            }
            $assign['filter'] = $filter;
        }
        
        $assign['activefilter'] = $activefilter;
        $assign['subaction'] = $subaction;
        $this->view->assignMultiple($assign);
        $this->addHeaderFile('jo.ce10.js');
        $this->addHeaderFile('jo.ce10.css');
    }

    public function reorderDates($result = [])
    {
        $return = $result;
        if (!empty($result)) {
            $reordered = [];
            $classifications = [];
            date_default_timezone_set('Europe/Berlin');
            foreach ($result as $item) {
                // Classifications
                if (!empty($item['identifier'])) {
                    foreach ($item['identifier'] as $identifier) {
                        if (!is_array($identifier['value'])) {
                            $id = $identifier['value'];
                            if (isset($identifier['identifier'])) $id = $identifier['identifier'];

                            $classifications[$identifier['name']][] = [
                                'name' => $identifier['value'],
                                'identifier' => $id
                            ];
                            $classifications[$identifier['name']] = array_unique($classifications[$identifier['name']], SORT_REGULAR);
                        }
                    }
                }
                $datetime = new \DateTime($item['startDate']);
                $key = $datetime->format('d-m');
                $reordered['dates'][$key]['items'][] = $item;
                $reordered['dates'][$key]['info'] = [
                    'day' => $datetime->format('d'),
                    'month' => $datetime->format('m'),
                    'year' => $datetime->format('Y'),
                    'monthname' => strftime('%B', $datetime->getTimestamp()),
                    'dayname' => strftime('%A', $datetime->getTimestamp()),
                    'daynameshort' => strftime('%a', $datetime->getTimestamp())
                ];
            }
            $reordered['configuration']['classifications'] = $classifications;
            $return = $reordered;
        }
        return $return;
    }

    /**
     * c11 action
     * Darstellug von Sammlungen und anderen Entitäten
     */
    public function c11Action()
    {
        $assign = [];
        $classification = [];
        $type = [];
        $topic = [];
        $keywords = [];
        $timerange = [];
        $demand = [];
        $pidList = 0;
        $view = $this->settings['view'];
        $pageuid = $GLOBALS['TSFE']->id;
        if (isset($this->settings['link'])) $pageuid = $this->settings['link'];
        $assign['pageuid'] = $pageuid;
        // Storagepid ermitteln
        if (isset($this->settings['startingpoint'])) {

            // Definiton der Templates für die Projektübersicht
            if ($this->request->hasArgument('tp')) {
                $view = filter_var($this->request->getArgument('tp'), FILTER_SANITIZE_STRING);
            }
           //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $view );
            $assign['tp'] = $view;
            $recursiveStoragePids = '';
            $tmppidList = explode(',', $this->settings['startingpoint']);
            $recursive = 3;
            $pid_list = [];
            foreach ($tmppidList as $startPid) {
                $startPid = intval($startPid);
                if ($startPid >= 0) {
                    $pid_list[] = $startPid;
                    $pids = $this->getTreeList($startPid, $recursive, 0, 1);
                    if (strlen($pids) > 0) $pid_list[] = $pids;
                }
            }
            $recursiveStoragePids = implode(',', $pid_list);
            // complett pid list
            $tmppids = GeneralUtility::uniqueList($recursiveStoragePids);
            $pidList = explode(',', $tmppids);
            $querySettings = $this->instituteRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds($pidList);
            $this->instituteRepository->setDefaultQuerySettings($querySettings);

            // Session für die Suchfilter
            $this->setSessionName();
            $this->joSessionvarName;
            $searcharray = $this->getSessionValues($this->joSessionvarName);  
            $selected_item = null;
            if ($this->request->hasArgument('topicdb')) {
                $selected_item = filter_var($this->request->getArgument('topicdb'), FILTER_SANITIZE_STRING);
                $searcharray['content']['topicdb'] = $this->joAddToArrayAndMakeUnique($searcharray['content']['topicdb'], $selected_item);
            }
            if ($this->request->hasArgument('removetopicdb')) {
                $selected_item = filter_var($this->request->getArgument('removetopicdb'), FILTER_SANITIZE_STRING);
                $searcharray['content']['topicdb'] = $this->joEliminateArrayValueAndKey($searcharray['content']['topicdb'], $selected_item);
            }

            if ($this->request->hasArgument('times')) {
                $selected_item = filter_var($this->request->getArgument('times'), FILTER_SANITIZE_STRING);
                $searcharray['content']['times'] = $this->joAddToArrayAndMakeUnique($searcharray['content']['times'], $selected_item);
            }
            if ($this->request->hasArgument('removetimes')) {
                $selected_item = filter_var($this->request->getArgument('removetimes'), FILTER_SANITIZE_STRING);
                $searcharray['content']['times'] = $this->joEliminateArrayValueAndKey($searcharray['content']['times'], $selected_item);
            }

            if ($this->request->hasArgument('keywords')) {
                $selected_item = filter_var($this->request->getArgument('keywords'), FILTER_SANITIZE_STRING);
                $searcharray['content']['keywords'] = $this->joAddToArrayAndMakeUnique($searcharray['content']['keywords'], $selected_item);
            }
            if ($this->request->hasArgument('removekeywords')) {
                $selected_item = filter_var($this->request->getArgument('removekeywords'), FILTER_SANITIZE_STRING);
                $searcharray['content']['keywords'] = $this->joEliminateArrayValueAndKey($searcharray['content']['keywords'], $selected_item);
            }

            if ($this->request->hasArgument('classfication')) {
                $selected_item = filter_var($this->request->getArgument('classfication'), FILTER_SANITIZE_STRING);
                $searcharray['content']['classfication'] = $this->joAddToArrayAndMakeUnique($searcharray['content']['classfication'], $selected_item);
            }
            if ($this->request->hasArgument('removeclassfication')) {
                $selected_item = filter_var($this->request->getArgument('removeclassfication'), FILTER_SANITIZE_STRING);
                $searcharray['content']['classfication'] = $this->joEliminateArrayValueAndKey($searcharray['content']['classfication'], $selected_item);
            }
            if ($this->request->hasArgument('active')) {
                $selected_item = filter_var($this->request->getArgument('active'), FILTER_SANITIZE_STRING);
                $searcharray['content']['active'] = $this->joAddToArrayAndMakeUnique($searcharray['content']['active'], $selected_item);
            }
            if ($this->request->hasArgument('removeactive')) {
                $selected_item = filter_var($this->request->getArgument('removeactive'), FILTER_SANITIZE_STRING);
                $searcharray['content']['active'] = $this->joEliminateArrayValueAndKey($searcharray['content']['removeactive'], $selected_item);
            }
            if ($this->request->hasArgument('del')) {
                $searcharray = [];
                $selected_item = 'delete_all';
                $this->emptySession($this->joSessionvarName);
            } 
            if ($selected_item != null) {
                if (isset($searcharray['content']) && is_array($searcharray['content'])){
                    $searcharray['content'] = array_filter($searcharray['content']);    
                }
                if (is_array($searcharray) && !empty($searcharray)){
                    $searcharray = array_filter($searcharray);
                }
                $this->replaceAllValues($this->joSessionvarName, $searcharray);
            }

            $assign['searcharray'] = $searcharray;
            if (!empty($searcharray) && isset($searcharray['content'])) {
                foreach ($searcharray['content'] as $key => $value) {
                    if (!empty($value)) $demand['data'][$key] = $value; 
                }
            }

            $allObjects = $this->instituteRepository->findDemanded($demand);
            // Reihenfolge der Objekte zwischenspeichern - kann optimiert werden
            if ($allObjects) {
                $datarow = [];
                $datatitle = [];
                $datatitleshort = [];
               
                foreach ($allObjects as $key => $value) {
                    $datarow[$value->getUid()] = 0;
                    $datatitle[$value->getUid()] = $value->getTitle();
                    $datatitleshort[$value->getUid()] = $value->getShorttitle();
                    if ($value->getClassfication()) {
                        foreach ($value->getClassfication() as $skey => $svalue) {
                            $classification[$svalue->getUid()] = $svalue->getTitle();
                        }
                    }
                    if ($value->getKeywords()) {
                        foreach ($value->getKeywords() as $skey => $svalue) {
                            $keywords[$svalue->getUid()] = $svalue->getTitle();
                        }
                    }
                    if ($value->getTopicdb()) {
                        foreach ($value->getTopicdb() as $skey => $svalue) {
                            $topic[$svalue->getUid()] = $svalue->getTitle();
                        }
                    }
                    if ($value->getTimes()) {
                        foreach ($value->getTimes() as $skey => $svalue) {
                            $timerange[$svalue->getUid()] = $svalue->getTitle();
                        }
                    }
                }
                $assign['allinstitution'] = $allInstitution;
            }
             
            if ($this->request->hasArgument('detailview') && $this->request->getArgument('detailview') > 0) {
                $detail = 1;
                if ($this->request->hasArgument('detail') && $this->request->getArgument('detail') > 0) {
                    $detail = $this->request->getArgument('detail');
                }
                $singleObject = $this->instituteRepository->findByUid(intval($this->request->getArgument('detailview')));
                $singleUid = $singleObject->getUid();
                if (!empty($datarow) && isset($datarow[$singleUid])) {
                    $keys = array_keys($datarow);
                    $current = array_search($singleUid, $keys);
                    if (isset($keys[$current + 1])) $datarow[$keys[$current + 1]] = 'next';
                    if (isset($keys[$current - 1])) $datarow[$keys[$current - 1]] = 'prev';
                    $datarow[$singleUid] = 'current';
                    $datarow = array_flip($datarow);
                    if (isset($datarow['prev'])) {
                        $datarow['prevname'] = $datatitle[$datarow['prev']];
                        $datarow['prevnameShort'] = $datatitleshort[$datarow['prev']];
                    } 
                    
                    if (isset($datarow['next'])) {
                        $datarow['nextname'] = $datatitle[$datarow['next']];
                        $datarow['nextnameShort'] = $datatitleshort[$datarow['next']];
                    }

                    if (isset($datarow['current'])) $datarow['currentname'] = $datatitle[$datarow['current']];
                }

                $allObjects = [$singleObject];

                $assign['detail'] = $detail; 
                // Verknüpfte Datenobjekte auslesen
                if (count($allObjects) > 0) {
                    $relatedDataPid = $allObjects[0]->getRelateditems();
                    if ($relatedDataPid) {
                        $querySettings->setStoragePageIds([$relatedDataPid]);
                        $this->instituteRepository->setDefaultQuerySettings($querySettings);
                        $this->instituteRepository->setDefaultOrderings(['sorting' => QueryInterface::ORDER_DESCENDING]);
                        $subobjects = $this->instituteRepository->findAll();
                        $assign['subobjects'] = $subobjects;
                    }
                }
                /*
                if (count($allObjects) > 0) {
                    $relatedDataPid = $allObjects[0]->getDatastorage();
                    if ($relatedDataPid) {
                        $newsRepository = $this->objectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
                        // Storagepid ermitteln
                        $querySettings->setStoragePageIds([$relatedDataPid]);
                        $newsRepository->setDefaultQuerySettings($querySettings);
                        $newsRepository->setDefaultOrderings(['datetime' => QueryInterface::ORDER_DESCENDING]);
                        $objects = $newsRepository->findAll();
                        $this->view->assign('objects', $objects);
                    }
                }
                */
                // Verknüpfte Objekte finden
                //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $objects );
            } 
            $assign['selected_item'] = $selected_item;
            $assign['classfication'] = $classification;
            $assign['keywords'] = $keywords;
            $assign['topicdb'] = $topic;
            $assign['times'] = $timerange;
            $assign['objectlist'] = $datarow;
            $assign['items'] = $allObjects;
            $assign['demand'] = $demand; 
            $this->addHeaderFile('jo.ce11.css');
            $this->addHeaderFile('jo.ce11.js');
            $this->view->assignMultiple($assign);

            // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $assign );
        }
        
    }

    public function getTypoImages($derivate = [])
    {
        $return = [];
        if (!empty($derivate)) {
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            foreach ($derivate as $item) {
                if ('' != $item) $return[] = $resourceFactory->getFileReferenceObject($item);
            }
        }
        return $return;
    }

    /**
     * c12 action
     */
    public function c12Action()
    {
        //$imgArray = explode(',', $this->settings['images']);
        //$return = $this->getTypoImages($imgArray);
        //$this->view->assign('images', $return);
        $this->view->assign('images', $this->getFileObject('image'));
        $gutters = $this->settings['gutters'];
        if ('1' != $gutters) $gutters = "no-gutters";

        $this->view->assign('gutters', $gutters);
        $frame = $this->settings['frame'];
        if ('1' != $frame) $frame = "no-frame";

        $this->view->assign('frame', $frame);
        $this->addHeaderFile('jo.ce12.css');
    }

    /**
     * c13 action - Call to action Buttons
     */
    public function c13Action() {
        $this->addHeaderFile('jo.ce13.css');
    }

    /**
     * c14 action
     */
    public function c14Action() {}

    /**
     * c15 action - Darstellung von Logos mit und ohne Link
     */
    public function c15Action()
    {
        $this->view->assign('images', $this->getFileObject('image'));
        $this->addHeaderFile('jo.ce15.css');
    }

    /**
     * c16 -> before c03 Kacheleinstieg CTA - Button
     */
    public function c16Action()
    {
        if ($this->settings['image']) {
            $field = 'image';
            $images = $this->getFileObject($field);
            $this->view->assign('images', $images);
            $this->addHeaderFile('jo.ce16.css');
        }
    }

    /**
     * c17 action
     */
    public function c17Action()
    {
        $data = GeneralUtility::makeInstance(ConfigurationManager::class)->getContentObject()->data;
        $this->view->assign('data', $data);

        $galerieItems = [];
        $imageperslide = 1;
        if($this->settings['imageperslide']) $imageperslide = $this->settings['imageperslide'];
        if ($this->settings['igalerie']) {
            $field = 'image';
            $images = $this->getFileObject($field);
            if(!empty($images)){
                $n = 0;
                $i = 0;
                foreach ($images as $key => $ref) {
                    try {
                        $uid = $ref->getProperty('uid');

                        // workaround, konnte die poster für videos nicht kriegen, mit eigenen repository jetzt hohlen
                        $tmpItemRef = $this->fileReferenceRepository->findByUid($uid);

                        $galerieItems[$n][$i]= [
                            'title' => $ref->getProperty('title'),
                            'description' => $ref->getProperty('description'),
                            'contenttitle' => $ref->getProperty('alternative'),
                            'css' => $ref->getProperty('inlinecss'),
                            'uid' => $ref->getProperty('uid'),
                            'file' => $tmpItemRef,
                            'link' => $ref->getProperty('link')
                        ];
                        $i++;
                        if ($i == $imageperslide) {
                            $i = 0;
                            $n = $n + 100;
                        }
                    } catch (\Exception $e) {}
                }
            }
        }
        ksort($galerieItems); 
        $this->view->assign('galerieItems', $galerieItems);
        $this->addHeaderFile('jo.ce17.css');
        $this->addHeaderFile('jo.ce17.js');
    }
    
    /*
    protected function buildGalerieItems($item, $fileRepository, $detail_page)
    {
        $imguid = $item->getImage()->toArray()[0]->getUid();
        $ref = $fileRepository->findFileReferenceByUid($imguid);
        $uri = null;
        if ($ref->getProperty('link')) {
            $linkService = GeneralUtility::makeInstance(LinkService::class);
            $linkdata_array = $linkService->resolve($ref->getProperty('link'));
            if (!empty($linkdata_array)) {
                if ('page' == $linkdata_array['type']) {
                    $renderingContext = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext');
                    $uriBuilder = $renderingContext->getUriBuilder();
                    $uriBuilder->reset();
                    $uriBuilder->setTargetPageUid($linkdata_array['pageuid']);
                    $uri = $uriBuilder->build();
                }
                if ('url' == $linkdata_array['type']) $uri = $linkdata_array['url'];
            }
        } else {
            $renderingContext = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext');
            $uriBuilder = $renderingContext->getUriBuilder();
            $uriBuilder->reset();
            $uriBuilder->setTargetPageUid($detail_page);
            //$uriBuilder->setArguments(['uid' => $item->getUid()]);
            //@all - Elements Controller und jomuseo extension und pi1010 irgendwie variabel machen
            $uriBuilder->uriFor(null, ['uid' => $item->getUid()], 'Elements', 'jomuseo', 'pi1010');
            $uri = $uriBuilder->build();
        }

        return [
            'title' => $item->getTitle(),
            'description' => $item->getDescription(),
            'contenttitle' => $item->getSubtitle(),
            'css' => '',
            'uid' => $imguid,
            'link' => $uri
        ];
    }
    */

    /**
     * c18 action - empty - moved to c24 - modenews12
     */
    public function c18Action()
    {
        // Gridlayout
        $listgrid = [
            'img' => 3,
            'txt' => 9
        ];

        if ($this->settings['listgrid']) {
            $grid_settings = explode('_', $this->settings['listgrid']);
            $listgrid = [
                'img' => $grid_settings[1],
                'txt' => $grid_settings[2]
            ];
            $this->view->assign('listgrid', $listgrid);
        }

        // Placeholder
        if ($this->settings['images']) {
            $uidarray = explode(',', $this->settings['images']);
            $images = $this->getAssets($uidarray);
            $this->view->assign('placeholder', $images);
        }
        $newsRepository = GeneralUtility::makeInstance('GeorgRinger\News\Domain\Repository\NewsRepository');

        // Storagepid ermitteln
        if ($this->settings['startingpoint']) {
            $pidList = GeneralUtility::intExplode(',', $this->settings['startingpoint'], true);
            $querySettings = $newsRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds($pidList);
            $newsRepository->setDefaultQuerySettings($querySettings);
        }

        // if ('modenews1' == $this->settings['modenews']) {
        $objects = null;
        $categoryRepository = GeneralUtility::makeInstance('GeorgRinger\News\Domain\Repository\CategoryRepository');
        if ($this->request->hasArgument('detail')) {
            $objects = $newsRepository->findByUid(intval($this->request->getArgument('detail')));
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($objects);
            /*
            $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Content/C18_detail.html'
            ));
            */
            $this->view->setTemplate('C18_detail');
        } else {
            $newsRepository->setDefaultOrderings(['datetime' => QueryInterface::ORDER_DESCENDING]);
            $objects = $this->settings['limit'] ? $newsRepository->findAll()->getQuery()->setLimit(intval($this->settings['limit']))->execute() : $newsRepository->findAll();
        }
        $this->view->assign('objects', $objects);
        $this->addHeaderFile('jo.ce18.css');
        $this->addHeaderFile('jquery.hoverdir.min.js');
        $this->addHeaderFile('jo.ce18.js');
    }

    /**
     *  downloadAction
     *
     * @param string $downloadfile
     * @return void
     */
    public function downloadAction()
    {
        if ($this->request->hasArgument('downloadfile')) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $fileObjects = $fileRepository->findFileReferenceByUid(intval($this->request->getArgument('downloadfile')));

            if ($fileObjects) {
                $joDownloadFile = 'fileadmin' . $fileObjects->getProperty('identifier');
                //    Downloaddetails identifiziren
                $file_info_temp = explode('/', $joDownloadFile);
                $file_name = array_pop($file_info_temp);
                $file_info_array = [
                    'fullpath' => $joDownloadFile,
                    'size' => filesize($joDownloadFile),
                    'name' => $file_name,
                    'type' => strtolower(array_pop(explode('.', $file_name)))
                ];
                switch ($file_info_array['type']) {
                    case 'mp3':
                        $cType = 'audio/mpeg';
                        break;
                    case 'pdf':
                        $cType = 'application/pdf';
                        break;
                    case 'wav':
                        $cType = 'audio/x-wav';
                        break;
                    case 'mpeg':
                    case 'mpg':
                    case 'mpe':
                        $cType = 'video/mpeg';
                        break;
                    case 'mov':
                        $cType = 'video/quicktime';
                        break;
                    case 'avi':
                        $cType = 'video/x-msvideo';
                        break;
                    //  Verbotene Dateitypen
                    case 'txt':
                    case 'exe':
                    case 'zip':
                    case 'doc':
                    case 'xls':
                    case 'ppt':
                    case 'gif':
                    case 'png':
                    case 'jpeg':
                    case 'jpg':
                    case 'inc':
                    case 'conf':
                    case 'sql':
                    case 'cgi':
                    case 'htaccess':
                    case 'php':
                    case 'php3':
                    case 'php4':
                    case 'php5':
                        exit;
                    default:
                        $cType = 'application/force-download';
                        break;
                }
                header('Pragma: no-cache');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Description: File Download');
                header('Content-Type:' . $cType);
                header('Content-Length: ' . $file_info_array['size']);
                header('Content-Transfer-Encoding: binary');
                header('Content-Disposition: attachment; filename=' . $file_info_array['name']);
                $this->readfile_chunked($file_info_array['fullpath']);
                exit;
            }
        }
    }

    public function readfile_chunked($filename, $retbytes = true)
    {
        $chunksize = 1 * (1024 * 1024);
        $buffer = '';
        $cnt = 0;
        $handle = fopen($filename, 'rb');
        if (false === $handle) return false;
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) $cnt += strlen($buffer);
        }
        $status = fclose($handle);
        if ($retbytes && $status) return $cnt;

        return $status;
    }

    /**
     * c19 action
     */
    public function c19Action()
    {
        $newsRepository = GeneralUtility::makeInstance('GeorgRinger\News\Domain\Repository\NewsRepository');
        // Storagepid ermitteln
        if ($this->settings['startingpoint']) {
            $pidList = GeneralUtility::intExplode(',', $this->settings['startingpoint'], true);
            $querySettings = $newsRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds($pidList);
            $newsRepository->setDefaultQuerySettings($querySettings);
        }

        $newsRepository->setDefaultOrderings(['datetime' => QueryInterface::ORDER_DESCENDING]);
        $objects = $newsRepository->findAll();
        $this->view->assign('objects', $objects);
        $this->addHeaderFile('jo.ce19.css');
    }
    /*
    public function joColumSplitNew($joSortObject = null, $spalten = 2)
    {
        if (!is_array($joSortObject)) $joSortObject = $joSortObject->toArray();

        if (null != $joSortObject) {
            $i = 0;
            $index = 0;
            $joSpalten = $spalten;
            $joModulo = count($joSortObject) % $joSpalten;
            $joArrayMinSize = (count($joSortObject) - $joModulo) / $joSpalten;

            if (!empty($joSortObject)) {
                $joTempObjects = [];
                for ($x = 0; $x < $joSpalten; $x++) {
                    $joAdditionalElement = 0;
                    if ($joModulo > 0) {
                        $joAdditionalElement = 1;
                        $joModulo--;
                    }
                    $joTempObjects[$x] = array_splice($joSortObject, 0, $joArrayMinSize + $joAdditionalElement);
                    foreach ($joTempObjects[$x] as $key => $value) {
                        $joColumSplitBase = $value; //  Split bezieht sich auf den Arrayvalue
                        if ("key" == $this->joGetKeyOrValue) $joColumSplitBase = $key;

                        $valueArray = [];
                        if ($this->joDelimiter) $valueArray = explode($this->joDelimiter, $joColumSplitBase);
                        //  Uid und Wert voneinander trennen
                        $joItemsResultModified[$x][] = $joColumSplitBase;
                    }
                }
            }
            return $joItemsResultModified;
        }
    }
    */
    public function c20Action()
    {
        $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
        $galerieItems = [];
        $igalerie = explode(',', $this->settings['igalerie']);
        if (!empty($igalerie)) {
            foreach ($igalerie as $key => $value) {
                $ref = $fileRepository->findFileReferenceByUid($value);
                $galerieItems[] = [
                    'title' => $ref->getProperty('title'),
                    'discription' => $ref->getProperty('description'),
                    'uid' => $ref->getProperty('uid'),
                    'link' => $ref->getProperty('link'),
                    'width' => 100 / count($igalerie)
                ];
            }
        }
        $this->view->assign('galerieItems', $galerieItems);
        $this->addHeaderFile('jo.ce20.css');
    }


    

    public function getPrevNext($array = array(), $key = null){
        if(!empty($array) && $key != null){
            $array_keys = array_keys($array); 
            $array_indices = array_flip($array_keys); 
            $i = $array_indices[$key]; 
            if ($i > 0) $prev = $array[$array_keys[$i - 1]]; 
            if ($i < count($array_keys) - 1) $next = $array[$array_keys[$i + 1]]; 
            return array($prev,$next);
        }
    }
    /**
     *  Contentelement zur Darstellung des Vorgängers und des Nachfolgers im Seitenbaum
     *  stable
     *  
     */
    public function c21Action()
    {
        $position = array();
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $uid = $GLOBALS['TSFE']->id;
        $pid = $GLOBALS['TSFE']->page['pid'];
        if ($pid) {
            $tree = $pageRepository->getMenu($pid); 
            if (!empty($tree) && isset($tree[$uid])) {
                $prevnext = $this->getPrevNext($tree,$uid);
                if (is_array($prevnext) && count($prevnext) == 2) {
                    $position['prev'] = $prevnext[0];
                    $position['next'] = $prevnext[1];
                }
            }
        }
        $this->addHeaderFile('jo.ce21.css');
        $this->view->assign('position', $position); 
    }

    public function c22Action()
    {
        $this->addHeaderFile('jo.ce22.css');
        $view = 'time';
        if ($this->settings['startview']) $view = filter_var($this->settings['startview'], FILTER_SANITIZE_STRING);

        if (GeneralUtility::_GP('view') && $this->settings['viewsupport']) $view = filter_var(GeneralUtility::_GP('view'), FILTER_SANITIZE_STRING);

        $this->view->assign('view', $view);

        if ($this->settings['viewsupport']) $this->view->assign('viewsupport', $this->settings['viewsupport']);

        $newsRepository = $this->objectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        // Storagepid ermitteln
        if ($this->settings['startingpoint']) {
            $pidList = GeneralUtility::intExplode(',', $this->settings['startingpoint'], true);
            $querySettings = $newsRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds($pidList);
            $newsRepository->setDefaultQuerySettings($querySettings);
        }
        if ($this->request->hasArgument('news')) {
            $news = $newsRepository->findByUid(intval($this->request->getArgument('news')));
            $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Content/Detaildom.html'
            ));
            $this->view->assign('news', $news);
        } else {
            $newsRepository->setDefaultOrderings(['datetime' => QueryInterface::ORDER_DESCENDING]);
            if ($this->settings['categoriesList']) {
                $demand = $this->objectManager->get(\GeorgRinger\News\Domain\Model\Dto\NewsDemand::class);
                $cat = ['20'];
                if ($this->settings['categoriesList']) $cat = GeneralUtility::trimExplode(',', $this->settings['categoriesList'], true);

                $demand->setCategories($cat);
                $catConj = 'OR';
                if ($this->settings['categoriesConj']) $catConj = filter_var($this->settings['categoriesConj'], FILTER_SANITIZE_STRING);

                $demand->setCategoryConjunction($catConj);
                if ($this->settings['startingpoint']) $demand->setStoragePage($this->settings['startingpoint']);

                $items = $newsRepository->findDemanded($demand);
            } else {
                $items = $newsRepository->findAll();
            }
            // Sortierung und Aufbereitung für eine Timeline
            if (!empty($items)) {
                $temp_items = [];
                foreach ($items as $value) {
                    if ('kat' == $this->settings['modus']) {
                        $kat = $value->getCategories()->toArray();
                        if (is_array($kat) && !empty($kat)) {
                            $lenght = count($kat);
                            foreach ($kat as $k => $val) {
                                if ($val->getTitle() != 'Aktionen') {
                                    $tmptitle = $val->getTitle() ? $val->getTitle() : 'Unbekannt';
                                    $temp_items[$val->getTitle()][] = $value;
                                    break;
                                } elseif (($lenght - 1) == $k) {
                                    $temp_items['Unbekannt'][] = $value;
                                }
                            }
                        } else {
                            $temp_items['Unbekannt'][] = $value;
                        }
                    } else {
                        $temp_items[$value->getDatetime()->format('d.m.Y')][] = $value;
                    }
                }
                $this->view->assign('objects', $temp_items);
            }
        }
    }

    public function c23Action()
    {
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $galerieItems = [];
        $igalerie = explode(',', $this->settings['igalerie']);
        if (!empty($igalerie)) {
            foreach ($igalerie as $key => $value) {
                $ref = $fileRepository->findFileReferenceByUid($value);
                $galerieItems[] = [
                    'title' => $ref->getProperty('title'),
                    'discription' => $ref->getProperty('description'),
                    'uid' => $ref->getProperty('uid'),
                    'link' => $ref->getProperty('link'),
                    'width' => 100 / count($igalerie)
                ];
            }
        }
        $this->view->assign('galerieItems', $galerieItems);
        $this->addHeaderFile('jo.ce20.css');
    }

    public function getStoragePids($pidlist = null, $recursive = 1){
        $tmppidList = GeneralUtility::intExplode(',', $pidlist, true);
        foreach ($tmppidList as $startPid) {
            if ($startPid >= 0) {
                $pids = $this->getTreeList($startPid, $recursive, 0, 1);
                if (strlen($pids) > 0) $recursiveStoragePids .= ',' . $pids;
            }
        }
        $tmppids = GeneralUtility::uniqueList($recursiveStoragePids);
        $pidList = GeneralUtility::intExplode(',', $tmppids, true);
        return $pidList;
    }

    public function c24Action()
    {
        $pidList = 0;
        $assign = [];
        $joPaginateNumber = 1;
        $resultsPerPage = 100;
        $view = 'modenews1'; // Standardansicht der News-Listendarstellung

        // Content Objekt ermitteln und aktuelle Page UID ermitteln
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObj = $configurationManager->getContentObject();
        $pageID = $contentObj->data['pid'];
        $assign['aktuellepid'] = $pageID;

        // Paginatordaten ermitteln
        if ($this->request->hasArgument('jopaginatepage')) $joPaginateNumber = intval($this->request->getArgument('jopaginatepage'));

        // Standardeinstellung überschreiben, wenn eine Auswahl im Backend gertoffen wurde
        if ($this->settings["modenews"]) $view = $this->settings["modenews"];
        // Detailansicht laden, wenn der User sie abfordert
        if ($this->request->hasArgument('joNewsDetail')) $view = 'detail';

        $assign['view'] = $view;
        
        // wenn eine Referer-ID übergeben wurde  wird diese hier registriert
        if ($this->request->hasArgument('joRefererId')) {
            $joRefererArray = [intval($this->request->getArgument('joRefererId'))];
            $assign['joReferer'] = $joRefererArray;
        }
        
        // Storagepid ggf. rekursiv ermitteln und in die Default-Querysettings überschreiben
        if ($this->settings['startingpoint']) {
            $pidList = $this->getStoragePids($this->settings['startingpoint'], 3);
            $querySettings = $this->joNewsRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds($pidList);
            $this->joNewsRepository->setDefaultQuerySettings($querySettings);
        }

        // Anzahl der Resultate pro Seite aus Flexform holen
        if ($this->settings["paginator"]) $resultsPerPage = intval($this->settings["paginator"]);

        // Reihenfolge aus Flexform holen
        if ($this->settings["ASCDESC"]) $this->joNewsRepository->AscDesc = "ASC";

        // Offset der Pagination berechnen
        $offset = ($joPaginateNumber - 1) * $resultsPerPage;

        switch ($view) {
            case "modenews1":
            case "modenews4":
            case "modenews5":
            case "modenews9":
            case "modenews10":
            case "modenews11":
            case "modenews12":
            case "modenews13":
                $katdata = $this->joNewsRepository->findAll()->getQuery()->setLimit($resultsPerPage)->setOffset($offset)->execute();
                if (!empty($katdata)) {
                    $temp_objects = [];
                    if($view == 'modenews13'){
                        foreach ($katdata as $value){
                            $temp_objects[$value->getDatetime()->format('Y')][] = $value;
                        }
                        $this->view->assign('objects', $temp_objects);
                    }
                }
                $newscount = $this->joNewsRepository->countAll();
                $paginator = $this->MakePagination($newscount, $resultsPerPage, 6, $extbaseaction, $joPaginateNumber);
                $assign['paginationdata'] = $paginator;
                break;
            /*  Objekt des Monats - nicht geupdated - todo */
            case "modenews3":
                /*
                //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($paginator);
                $limit = 100; // Sicherheitshalber ist die maximale Anzahl auszulesender Datensätze begrenzt
                if ($this->settings['anzahl']) $limit = intval($this->settings['anzahl']);

                $katdatatmp = $this->joNewsRepository->findAll()->getQuery()->setLimit($limit)->execute()->toArray();
                $tmparr = [];
                if (!empty($katdatatmp)) {
                    foreach ($katdatatmp as $value) {
                        if (sizeof($value->getFalMedia()) > 0) {
                            // Aspect Ratio ermitteln
                            $ar = 'landscape';
                            $img_properties = $value->getFalMedia()->current()->getOriginalResource()->getProperties();
                            if ($img_properties['height'] && $img_properties['width']) {
                                if ($img_properties['height'] > $img_properties['width']) {
                                    $ar = 'portrait';
                                }
                            }
                            $tmparr[] = [
                                'value' => $value,
                                'ar' => $ar
                            ];
                        }
                    }
                }
                $katdata = $tmparr;
                */
                break;

            /*  Nächtes Element in der Zukunft - Eutiner LB - nicht geupdated - todo  */
            case "modenews6":
                /*
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' .
                    $this->request->getControllerExtensionKey() .
                    '/Resources/Private/Templates/Content/C24Nextnews.html'
                ));
                $katdata = $this->joNewsRepository->findNext();
                */
                break;
              /*  News auf Karte - nicht geupdated - todo  */
            case "modenews7":
                /*
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' .
                    $this->request->getControllerExtensionKey() .
                    '/Resources/Private/Templates/Content/C24ListMapnews.html'
                ));
                $instRepository = $this->objectManager->get('JO\JoMuseo\Domain\Repository\InstituteRepository');
                if ($this->settings['startingpoint']) {
                    $querySettings = $instRepository->createQuery()->getQuerySettings();
                    $querySettings->setStoragePageIds($pidList);
                    $instRepository->setDefaultQuerySettings($querySettings);
                }
                $joPlaces = $instRepository->findAll()->toArray();
                $joIndexUtil = $this->objectManager->get('JO\JoSolrsystem\Utility\Fuzzysearchutils\Jomakeindex');
                $joPlacesArray = [];
                $tmp = [];
                foreach ($joPlaces as $key => $value) $tmp[strtoupper($value->getTitle()[0])][] = $value;
                $joIndexUtil->joSpalten = 3;
                $joIndexUtil->joSimpleSplit = true;
                $joPlacesArray = $joIndexUtil->joColumSplit($tmp);

                $this->view->assign('joPlaces', $joPlacesArray);

                $katdata = '';
                */
                break;
            /*  Letzte News als Teaserelement - nicht geupdated - todo */
            case "modenews8":
                /*
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' .
                    $this->request->getControllerExtensionKey() .
                    '/Resources/Private/Templates/Content/C24Teaser.html'
                ));
                $katdata = $this->joNewsRepository->findAll()->getQuery()->setLimit(1)->execute();
                */
                break;
            case "detail":
                $joNewsDetail = intval($this->request->getArgument('joNewsDetail'));
                $newsdetail = $this->joNewsRepository->findByUid($joNewsDetail);
                $katdata = [$newsdetail];
                break;
        }
        $assign['newsByCategories'] = $katdata;
        
        $this->view->assignMultiple($assign);
        $this->addHeaderFile('jo.ce24.css');
    }

    public function c25Action()
    {
        $this->addHeaderFile('jo.ce25.css');
        // $this->addHeaderFile('jo.ce24.js');
        /*
        $content_array = array(
        0 => array(
        'headline' => 'Vertretungsplan',
        'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod invidunt.',
        'linkuid' => 78
        ),
        1 => array(
        'headline' => 'Unterrichtsmaterial',
        'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod invidunt.',
        'linkuid' => 66
        ),
        2 => array(
        'headline' => 'Projekte',
        'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod invidunt.',
        'linkuid' => 75
        ),
        3 => array(
        'headline' => 'Formulare',
        'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod invidunt.',
        'linkuid' => 70
        ),

        );
        $this->view->assign('items',$content_array);
         */

        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump('debug');
    }

    /**
     * Build c26Action
     * Bearbeitet Institut und Person Model und erstellt eine Liste
     * welche über Axjax detail abfragen ermöglicht
     *
     * @return void
     */
    public function c26Action()
    {
        $this->addHeaderFile('jo.ce26.css');
        $this->addHeaderFile('jo.ce26.js');

        if ($this->settings['startingpoint']) {
            if ($this->request->hasArgument('pers_id') || $this->request->hasArgument('inst_id')) {

                $pidList = GeneralUtility::intExplode(',', $this->settings['startingpoint'], true);
                $querySettings = $this->contentInstituteRepository->createQuery()->getQuerySettings();
                $querySettings->setStoragePageIds($pidList);

                $this->contentInstituteRepository->setDefaultQuerySettings($querySettings);

                $persObjects = null;
                if ($this->request->hasArgument('pers_id')) {
                    $pers_id = filter_var($this->request->getArgument('pers_id'), FILTER_SANITIZE_NUMBER_INT);
                    $persObjects = $this->contentInstituteRepository->findByEmployee($pers_id)[0];
                }

                if ($this->request->hasArgument('inst_id')) {
                    $inst_id = filter_var($this->request->getArgument('inst_id'), FILTER_SANITIZE_NUMBER_INT);
                    $persObjects = $this->contentInstituteRepository->findByUid($inst_id);
                    $this->view->assign('inst_detail', true);
                }

                if (null != $persObjects) $this->view->assign('items', $persObjects);

                //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($persObjects);
                $this->view->assign('detail', true);
            } else {
                $pidList = GeneralUtility::intExplode(',', $this->settings['startingpoint'], true);

                // Personen
                if (1 == $this->settings['output'] || 3 == $this->settings['output']) {
                    $querySettings = $this->personRepository->createQuery()->getQuerySettings();
                    $querySettings->setStoragePageIds($pidList);
                    $this->personRepository->setDefaultQuerySettings($querySettings);
                    $persObjects = $this->personRepository->findAll();
                    $output_arr = [];
                    foreach ($persObjects as $key => $value) {
                        if ($value->getDepartment() && $value->getDepartment() != '') $output_arr[$value->getDepartment()][] = $value;
                    }
                    $this->view->assign('items', $output_arr);
                }

                // Institutionen
                if (2 == $this->settings['output'] || 3 == $this->settings['output']) {
                    $querySettings = $this->contentInstituteRepository->createQuery()->getQuerySettings();
                    $querySettings->setStoragePageIds($pidList);
                    $this->contentInstituteRepository->setDefaultOrderings(['city' => QueryInterface::ORDER_ASCENDING]);
                    $this->contentInstituteRepository->setDefaultQuerySettings($querySettings);
                    $all_inst = $this->contentInstituteRepository->findAll();
                    $querySettings = $this->contentInstituteRepository->createQuery()->getQuerySettings();
                    $querySettings->setStoragePageIds($pidList);
                    $this->federalstateRepository->setDefaultOrderings(['name' => QueryInterface::ORDER_ASCENDING]);
                    $this->federalstateRepository->setDefaultQuerySettings($querySettings);
                    $all_fedstate = $this->federalstateRepository->findAll();

                    $all_typ = [];
                    foreach ($all_inst as $key => $value) {
                        $typ = $value->getTyp();
                        if ($typ && '' != $typ && !in_array($typ, $all_typ)) $all_typ[] = $typ;
                    }

                    if (!empty($all_typ)) $this->view->assign('all_typ', $all_typ);

                    $this->view->assignMultiple(
                        [
                            'all_inst' => $all_inst,
                            'all_fedstate' => $all_fedstate
                        ]
                    );
                }
            }
        }
    }

    /**
     * Build c30Action
     *
     *
     *
     * @return void
     */
    public function c30Action()
    {
        $this->addHeaderFile('jo.ce30.css');
        $this->addHeaderFile('jo.ce30.js');

        $data = GeneralUtility::makeInstance(ConfigurationManager::class)->getContentObject()->data;
        $this->view->assign('data', $data);

        /**
         *    Limit festlegen für Aufrufe ohne Parameter
         *    wenn kein Parameter hinterlegt wurde, werden $joLimit Datensätze ausgewählt
         */
        $detailid = false;
        $view = 1; // Intitial wird die Slideshow als darstellungsform verwendet
        $joLimit = 50;
        $joStart = 0;
        $selectedtimestamp = time();
        $today = time();
        $joDatum = [];
        $joDatum["ajaxSend"] = 0;
        $archive = [];
        if ($this->settings["anzahl"] > 0) $joLimit = intval($this->settings["anzahl"]);

        if ($this->request->hasArgument('existingsItems')) $joStart = $this->request->getArgument('existingsItems') + 0;

        if ($this->settings["layout"]) $view = intval($this->settings["layout"]);

        if ($this->request->hasArgument('ajaxSend')) $joDatum["ajaxSend"] = $this->request->getArgument('ajaxSend');

        if ($this->request->hasArgument('loadDate')) $selectedtimestamp = (int) $this->request->getArgument('loadDate');

        if ($this->request->hasArgument('detail')) {
            $detailid = (int) $this->request->getArgument('detail');
            $view = 10;
        }
        $joDatum["selectedtimestamp"] = $selectedtimestamp;
        $joDatum["selectedmonatname"] = strftime("%B", $selectedtimestamp);
        $joDatum["selectedtagname"] = strftime("%A", $selectedtimestamp);
        $joDatum["selectedtagedesmonats"] = date("t", $selectedtimestamp);
        $joDatum["selectedmonatnumber"] = date("m", $selectedtimestamp);
        $joDatum["selectedtagnumber"] = date("d", $selectedtimestamp);
        $joDatum["selectedjahrnumber"] = date("Y", $selectedtimestamp);
        // Prev and Nextdates
        $joDatum["nextjahr"] = $joDatum["selectedjahrnumber"] + 1;
        $joDatum["prevjahr"] = $joDatum["selectedjahrnumber"] - 1;
        $joDatum["nextmonth"] = $joDatum["selectedmonatnumber"] + 1;
        $joDatum["prevmonth"] = $joDatum["selectedmonatnumber"] - 1;
        $joDatum["jahrofnextmonth"] = $joDatum["selectedjahrnumber"];
        $joDatum["jahrofprevmonth"] = $joDatum["selectedjahrnumber"];
        if (12 == $joDatum["selectedmonatnumber"]) {
            $joDatum["nextmonth"] = 1;
            $joDatum["jahrofnextmonth"] = $joDatum["jahrofnextmonth"] + 1;
        }
        if (1 == $joDatum["selectedmonatnumber"]) {
            $joDatum["prevmonth"] = 12;
            $joDatum["jahrofprevmonth"] = $joDatum["jahrofprevmonth"] - 1;
        }
        $joDatum["prevmonthtimestamp"] = mktime(0, 0, 0, $joDatum["prevmonth"], 1, $joDatum["jahrofprevmonth"]);
        $joDatum["nextmonthtimestamp"] = mktime(0, 0, 0, $joDatum["nextmonth"], 1, $joDatum["jahrofnextmonth"]);
        // Startsequenz der Eventauslese
        $joDatum["selecteventsfromdaynumber"] = 1;

        // Aktuelles Datum als Referenz übernehmen
        $joDatum["todaytag"] = strftime("%d", $today);
        $joDatum["todaymonat"] = strftime("%m", $today);
        $joDatum["todayjahr"] = strftime("%y", $today);
        $joDatum["todayjahrbig"] = strftime("%Y", $today);
        $joDatum["todaytimestamp"] = mktime(0, 0, 0, $joDatum["todaymonat"], $joDatum["todaytag"], $joDatum["todayjahr"]);

        // Wenn die Daten nur ab heute angezeigt werden sollen, ist ein rückblättern in die Vergangenheit nicht möglich und es werden auch nur Events ab heute ausgegeben, sonst wird der erste des Monats berücksichtigt
        if (1 == $this->settings["showfromtoday"] &&
            $joDatum["selectedmonatnumber"] == $joDatum["todaymonat"] &&
            $joDatum["selectedjahrnumber"] == $joDatum["todayjahrbig"]
        ) {
            $joDatum["selecteventsfromdaynumber"] = $joDatum["selectedtagnumber"];
            unset($joDatum["prevmonth"]);
            unset($joDatum["prevjahr"]);
        }

        // Tage des selektierten Monats ermitteln
        $daysofthemonth = [];
        for ($i = 1; $i <= $joDatum["selectedtagedesmonats"]; $i++) {
            $today = 0;
            $passed = 0;
            $generatedday = mktime(0, 0, 0, $joDatum["selectedmonatnumber"], $i, $joDatum["selectedjahrnumber"]);
            if ($generatedday == $joDatum["todaytimestamp"]) $today = 1;

            if ($generatedday < $joDatum["todaytimestamp"]) $passed = 1;

            $currentdaytimestamp = strftime("%A", $generatedday);
            $daysofthemonth[$i] = [
                'events' => 0,
                'firstletter' => substr($currentdaytimestamp, 0, 1),
                'today' => $today,
                'passed' => $passed
            ];
        }
        // Monatsanfang und Ende ermitteln
        //  $monthstart = mktime(0, 0, 0, (int) $joDatum["selectedmonatnumber"], (int) $joDatum["selecteventsfromdaynumber"], (int) $joDatum["selectedjahrnumber"]);
        //  $monthend = mktime(0, 0, 0, (int) $joDatum["selectedmonatnumber"], (int) $joDatum["selectedtagedesmonats"], (int) $joDatum["selectedjahrnumber"]);
        $monthstart = (int) $joDatum["selectedjahrnumber"] . '-' . (int) $joDatum["selectedmonatnumber"] . '-' . (int) $joDatum["selecteventsfromdaynumber"];
        $monthend = (int) $joDatum["selectedjahrnumber"] . '-' . (int) $joDatum["selectedmonatnumber"] . '-' . (int) $joDatum["selectedtagedesmonats"];

        $filter = [
            'start_date' => $monthstart,
            'end_date' => $monthend,
        ];
        if ($detailid) {
            $filter = [
                'by_spielzeit_ids' => [$detailid]
            ];
        }

        $uri = '?&search=performance&type=2344&style=json-ld&' . http_build_query($filter);
        // die($uri);
        $events = file_get_contents($uri);
        // Wenn im aktuellen Monat keine Veranstaltungen kommen oder weniger als drei, dann werden alle kommenden ausgegeben ohne das Ende des Monats zu berücksichtigen
        //if (is_array($events) && count($events) < 3) {
        // $uri = '?&search=performance&type=2344&style=json-ld';
        // $events = file_get_contents($uri);
        //}
        $eventsarray = [];
        if ($events) {
            $eventsarray = json_decode($events, true);
            $eventsarray = $this->explodeEvents($eventsarray);
            if (!empty($eventsarray)) {
                foreach ($eventsarray as $k => $e) {
                    $date = new \DateTime($k);
                    $daysofthemonth[$date->format('j')]['events'] = 1;
                }
            }
            $mapdata = $this->getGeoData($eventsarray);
            $jsencoded = json_encode($mapdata);
            $this->response->addAdditionalHeaderData('<script>geojson=' . $jsencoded . ';</script>');

            $this->view->assignMultiple(
                [
                    'dddd' => $uri,
                    'geojson' => $jsencoded
                ]
            );

            // Pagination der Resultate
            /*
        $paginationdata = $this->makePagination(
        count($eventsarray['theater'][0]['spielzeit']),
        $limit,
        $pagecount,
        $page
        );
         */
        }

        $result = [
            'feusers' => $feusers,
            'searcharray' => $searcharray,
            'veranstalterarray' => $veranstalterarray,
            'spielortearray' => $spielortearray,
            'eventsarray' => $eventsarray,
            'paginationdata' => $paginationdata,
            'daysofthemonth' => $daysofthemonth,
            'datum' => $joDatum,
            'veranstaltungen' => $eventsarray,
            'view' => $view,
            'month' => $month
        ];

        //
        $this->view->assignMultiple($result);
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($feusers);
    }

    /**
     * Video Loader
     *
     */
    public function c31Action()
    {
        $this->addHeaderFile('jo.ce31.js');
        $this->addHeaderFile('jo.ce31.css');

        $video = $this->getFileObject('video');
        $previewImage = $this->getFileObject('preview_image');
        
        if (!empty($video)) $this->view->assign('video', $video[0]);
        if (!empty($previewImage)) $this->view->assign('previewImage', $previewImage[0]);  
    }

    /**
     * Vorher Nachher Bilder
     *
     */
    public function c32Action()
    {
        $this->addHeaderFile('twentytwenty.css', 'Resources/Public/Css/Libs/twentytwenty/');
        $this->addHeaderFile('jquery.event.move.js', 'Resources/Public/JavaScript/Libs/twentytwenty/');
        $this->addHeaderFile('jquery.twentytwenty.js', 'Resources/Public/JavaScript/Libs/twentytwenty/');
        $this->addHeaderFile('jo.ce32.js');

        $vorherImage = $this->getFileObject('vorher_image');
        $nachherImage = $this->getFileObject('nachher_image');

        $this->view->assignMultiple(
            [
                'vorherImage' => $vorherImage,
                'nachherImage' => $nachherImage
            ]
        );
    }

    /**
     * RUHR Start Gallery
     *
     */
    public function c33Action()
    {
        $this->addHeaderFile('jo.ce33.js');
        $this->addHeaderFile('jo.ce33.css');

        $bild1 = $this->getFileObject('bild1');
        $bild2 = $this->getFileObject('bild2');
        $bild3 = $this->getFileObject('bild3');
        $bild4 = $this->getFileObject('bild4');
        $bild5 = $this->getFileObject('bild5');
        $bild6 = $this->getFileObject('bild6');
        $bild7 = $this->getFileObject('bild7');

        $vorschau = $this->getFileObject('vorschau');

        $this->view->assignMultiple(
            [
                'bild1' => $bild1,
                'bild2' => $bild2,
                'bild3' => $bild3,
                'bild4' => $bild4,
                'bild5' => $bild5,
                'bild6' => $bild6,
                'bild7' => $bild7,
                'vorschau' => $vorschau
            ]
        );
    }

    /**
     * Iframe mit Poster, welches man wegklicken kann
     *
     */
    public function c34Action()
    {
        $this->addHeaderFile('jo.ce34.js');
        $this->addHeaderFile('jo.ce34.css');

        $poster = $this->getFileObject('poster');

        $this->view->assignMultiple(
            [
                'poster' => $poster,
                'iframe_encoded' => json_encode($this->settings['iframe'])
            ]
        );
    }

    /**
     * c35 action Video Player DBT
     */
    public function c35Action()
    {
        $this->addHeaderFile('video-js.css');
        $this->addHeaderFile('video-i18.min.js');
        $this->addHeaderFile('video.js');
        $this->addHeaderFile('jo.ce35.js');
        $this->addHeaderFile('jo.ce35.css');
    }

    public function explodeEvents($events = [])
    {
        $return = [];
        if (!empty($events)) {
            foreach ($events as $k => $e) {
                if (!empty($e['eventSchedule'])) {
                    foreach ($e['eventSchedule'] as $se) {
                        $events[$k]['startDate'] = $se['startDate'];
                        $events[$k]['endDate'] = $se['endDate'];
                        if (!empty($se['identifier'])) {
                            foreach ($se['identifier'] as $sse) {
                                if ('ID' == $sse['name']) $events[$k]['id'] = $sse['value'];
                            }
                        }
                        $return[$se['startDate']] = $events[$k];
                    }
                }
            }
        }
        ksort($return);
        return $return;
    }

    public function getGeonames($schema_array = [], $key = '')
    {
        $return = false;
        if (!empty($schema_array && '' != $key)) {}

        return $return;
    }

    public function getGeoData($schema_array = [])
    {
        $return = false;
        if (!empty($schema_array)) {
            $coordinates = [];
            foreach ($schema_array as $value) {
                $description = '';
                $title = '';
                if (array_key_exists('about', $value)) $description = $value['about'];

                if (array_key_exists('name', $value)) $title = $value['name'];

                $lng = 0;
                $lat = 0;

                if (array_key_exists('location', $value)) {
                    foreach ($value['location'] as $loc_v) {
                        if (array_key_exists('geo', $loc_v)) {
                            if (0 != $loc_v['geo']['latitude'] && 0 != $loc_v['geo']['longitude']) {
                                $lng = $loc_v['geo']['longitude'];
                                $lat = $loc_v['geo']['latitude'];
                                break;
                            }
                        }
                    }
                }

                if (0 != $lat && 0 != $lng) {
                    $geom_array = [];
                    $geom_array["type"] = 'Feature';
                    $geom_array["properties"] = ['t' => $title, 'd' => $description];
                    $geom_array["geometry"]["type"] = 'Point';
                    $geom_array["geometry"]["coordinates"] = [(float) $lng, (float) $lat];
                    $coordinates[] = $geom_array;
                }
            }
            // $return = $coordinates;

            $joGeoJSON = [];
            if (!empty($coordinates)) {
                $joGeoJSON['points'] = [
                    "type" => "FeatureCollection",
                    "features" => $coordinates
                ];
            }
            return $joGeoJSON;
        }
        return $return;
    }
};
