<?php
namespace JO\JoContent\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class NewsRepository extends Repository
{

    /**
     *    Alles News sollen entspechend ihres Ver�ffentlichungsdatums sortiert werden
     */

    protected $defaultOrderings = [
        'datetime' => QueryInterface::ORDER_DESCENDING,
        'pid' => QueryInterface::ORDER_DESCENDING
    ];

    public $AscDesc = null;

    public $varlimit = 0;

    public $fromtoday = false;

    public $thisdate = null;

    public $varoffset = 0;

    public function findAll()
    {
        $query = $this->createQuery();
        if ($this->varlimit > 0) {
            $query->setLimit(intval($this->varlimit));
            if ($this->varoffset > 0) {
                $query->setOffset(intval($this->varoffset));
            }
        }
        if ($this->fromtoday) {
            $query->matching(
                $query->greaterThanOrEqual('datetime', time())
            );
        }
        if (null != $this->thisdate) {
            $query->matching(
                $query->greaterThanOrEqual('datetime', $this->thisdate)
            );
        }
        if (null != $this->AscDesc) {
            $query->setOrderings(['datetime' => QueryInterface::ORDER_ASCENDING]);
        }
        $post = $query->execute();
        return ($post);
    }

    public function findNext($joLimit = 1)
    {
        $query = $this->createQuery();
        $joHeute = time();
        $query->matching(
            $query->greaterThanOrEqual('datetime', $joHeute)
        );
        $joLimit = (int) $joLimit;
        if ($joLimit > 0) {
            $query->setLimit($joLimit);
        }
        $post = $query->execute();
        return ($post);
    }

    /**
     *    Letzten Beitrag ausgeben, ohne Ber�cksichtung des Datums
     */
    public function findLast($joLimit = 1)
    {
        $query = $this->createQuery();
        $joHeute = time();
        $joLimit = (int) $joLimit;
        if ($joLimit > 0) {
            $query->setLimit($joLimit);
        }
        $post = $query->execute();
        return ($post);
    }

    public function getAlle(
        $joNewsKat = [],
        $joLimit = 0,
        $joOffset = 0,
        $joField = "pid"
    ) {

        /**
         *    Storage Id �bermitteln
         */
        if (!empty($joNewsKat)) {
            $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
            $querySettings->setStoragePageIds($joNewsKat);
            $this->setDefaultQuerySettings($querySettings);
        }

        $query = $this->createQuery();

        $joHeute = time();
        $constraints = [];
        $query->matching(
            $query->lessThanOrEqual('datetime', $joHeute)
        );
        if (null != $this->AscDesc) {
            $query->setOrderings(['datetime' => QueryInterface::ORDER_ASCENDING]);
        }
        $joLimit = (int) $joLimit;
        if ($joLimit > 0) {
            $query->setLimit($joLimit);
        }
        $joOffset = (int) $joOffset;
        if ($joOffset > 0) {
            $query->setOffset($joOffset);
        }
        $post = $query->execute();
        return ($post);
    }
}
