<?php

declare(strict_types=1);

namespace NITSAN\NsHelpdesk\Domain\Repository;

use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Session\Backend\SessionBackendInterface;
use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class BackendUserRepository extends Repository
{
    /**
     * Finds Backend Users on a given list of uids
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findByUidList(array $uidList)
    {
        $query = $this->createQuery();
        $query->matching($query->in('uid', array_map('intval', $uidList)));
        /** @var QueryResult $result */
        $result = $query->execute();
        return $result;
    }

    /**
     * Find Backend Users matching to Demand object properties
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findDemanded(Demand $demand)
    {
        $constraints = [];
        $query = $this->createQuery();
        $query->setOrderings(['userName' => QueryInterface::ORDER_ASCENDING]);
        // Username
        if ($demand->getUserName() !== '') {
            $searchConstraints = [];
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');
            foreach (['userName', 'realName'] as $field) {
                $searchConstraints[] = $query->like(
                    $field,
                    '%' . $queryBuilder->escapeLikeWildcards($demand->getUserName()) . '%'
                );
            }
            if (MathUtility::canBeInterpretedAsInteger($demand->getUserName())) {
                $searchConstraints[] = $query->equals('uid', (int)$demand->getUserName());
            }
            if (count($searchConstraints) === 1) {
                $constraints[] = reset($searchConstraints);
            } elseif (count($searchConstraints) >= 2) {
                $constraints[] = $query->logicalOr(...$searchConstraints);
            }
        }
        // Only display admin users
        if ($demand->getUserType() === Demand::USERTYPE_ADMINONLY) {
            $constraints[] = $query->equals('admin', 1);
        }
        // Only display non-admin users
        if ($demand->getUserType() === Demand::USERTYPE_USERONLY) {
            $constraints[] = $query->equals('admin', 0);
        }
        // Only display active users
        if ($demand->getStatus() === Demand::STATUS_ACTIVE) {
            $constraints[] = $query->equals('disable', 0);
        }
        // Only display in-active users
        if ($demand->getStatus() === Demand::STATUS_INACTIVE) {
            $constraints[] = $query->equals('disable', 1);
        }
        // Not logged in before
        if ($demand->getLogins() === Demand::LOGIN_NONE) {
            $constraints[] = $query->equals('lastlogin', 0);
        }
        // At least one login
        if ($demand->getLogins() === Demand::LOGIN_SOME) {
            $constraints[] = $query->logicalNot($query->equals('lastlogin', 0));
        }
        // In backend user group
        // @TODO: Refactor for real n:m relations
        if ($demand->getBackendUserGroup()) {
            $constraints[] = $query->logicalOr(
                $query->equals('usergroup', (int)$demand->getBackendUserGroup()),
                $query->like('usergroup', (int)$demand->getBackendUserGroup() . ',%'),
                $query->like('usergroup', '%,' . (int)$demand->getBackendUserGroup()),
                $query->like('usergroup', '%,' . (int)$demand->getBackendUserGroup() . ',%'),
            );
        }
        $query->matching($query->logicalAnd(...$constraints));

        /** @var QueryResult $result */
        $result = $query->execute();
        return $result;
    }

    /**
     * Find Backend Users currently online
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findOnline()
    {
        $uids = [];
        foreach ($this->getSessionBackend()->getAll() as $sessionRecord) {
            if (isset($sessionRecord['ses_userid']) && !in_array($sessionRecord['ses_userid'], $uids, true)) {
                $uids[] = $sessionRecord['ses_userid'];
            }
        }

        $query = $this->createQuery();
        $query->matching($query->in('uid', $uids));
        /** @var QueryResult $result */
        $result = $query->execute();
        return $result;
    }

    /**
     * Overwrite createQuery to don't respect enable fields
     *
     * @return QueryInterface
     */
    public function createQuery()
    {
        $query = parent::createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        return $query;
    }

    protected function getSessionBackend(): SessionBackendInterface
    {
        return GeneralUtility::makeInstance(SessionManager::class)->getSessionBackend('BE');
    }
}
