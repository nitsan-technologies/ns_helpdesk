<?php

namespace NITSAN\NsHelpdesk\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/***
 *
 * This file is part of the "NS Helpdesk" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020
 *
 ***/
/**
 * The repository for Tickets
 */
class TicketsRepository extends Repository
{
    /**
     * @var array<non-empty-string, 'ASC'|'DESC'>
     */
    protected $defaultOrderings = ['crdate' => QueryInterface::ORDER_DESCENDING];

    public function getFromAll()
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
        debug($querySettings);
        die();
    }
    public function fetchTickets($filterData = null)
    {
        $query = $this->createQuery();

        if ($filterData) {
            $constraints = [];

            $filterData['userid'] = isset($filterData['userid']) ? $filterData['userid'] : '';
            if ($filterData['userid']) {
                $filterData['backendUser'] = isset($filterData['backendUser']) ? $filterData['backendUser'] : '';
                $userId = (int)$filterData['userid'];
                if ($filterData['backendUser']) {
                    $constraints[] = $query->equals('assigneeId.uid', $userId);
                } else {
                    $constraints[] = $query->equals('userId.uid', $userId);
                }
            }

            $filterData['ticket_status'] = isset($filterData['ticket_status']) ? $filterData['ticket_status'] : '';
            if ($filterData['ticket_status']) {
                $constraints[] = $query->equals('ticketStatus.uid', (int)$filterData['ticket_status']);
            }

            if ($constraints !== []) {
                $query->matching(
                    count($constraints) === 1 ? $constraints[0] : $query->logicalAnd(...$constraints)
                );
            }
        }

        return $query->execute();
    }

    public function getLanguageIso($langId)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $statement = $queryBuilder
            ->select('language_isocode')
            ->from('sys_language')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($langId))
            )
            ->executeQuery()->fetchAllAssociative();
        return $statement;
    }
    public function getSlug($slug = null)
    {
        $query = $this->createQuery();
        $query->matching($query->like('slug', $slug . '%'));
        return $query->execute();
    }

    public function getCustomerReview()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nshelpdesk_domain_model_tickets');
        $total5Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(5))
            )
            ->executeQuery()
            ->fetchOne();

        $total4Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(4))
            )
            ->executeQuery()
            ->fetchOne();

        $total3Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(3))
            )
            ->executeQuery()
            ->fetchOne();

        $total2Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(2))
            )
            ->executeQuery()
            ->fetchOne();

        $total1Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(1))
            )
            ->executeQuery()
            ->fetchOne();
        return [
            'total5Ratings' => $total5Ratings,
            'total4Ratings' => $total4Ratings,
            'total3Ratings' => $total3Ratings,
            'total2Ratings' => $total2Ratings,
            'total1Ratings' => $total1Ratings
        ];
    }
}
