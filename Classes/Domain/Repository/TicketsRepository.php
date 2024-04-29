<?php
namespace NITSAN\NsHelpdesk\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 *
 * This file is part of the "Helpdesk" Extension for TYPO3 CMS.
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
class TicketsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    public function getFromAll()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
    public function fetchTickets($filterData = null)
    {
        $query = $this->createQuery();
        $constraints = [];
        if ($filterData) {
            if ($filterData['userid']) {
                $filterData['backendUser'] = isset($filterData['backendUser']) ? $filterData['backendUser'] : '';
                if ($filterData['backendUser']) {
                    $constraints[] = $query->equals('assignee_id', $filterData['userid']);
                } else {
                    $constraints[] = $query->equals('user_id', $filterData['userid']);
                }
            }
            $filterData['ticket_status'] = isset($filterData['ticket_status']) ? $filterData['ticket_status'] : '';
            $filterData['sword'] = isset($filterData['sword']) ? $filterData['sword'] : '';
            if ($filterData['ticket_status']) {
                $constraints[] = $query->in('ticket_status', $filterData['ticket_status']);
            }
            if (is_string($filterData['sword']) && strlen($filterData['sword']) > 0) {
                $constraints[] = $query->logicalOr(
                    [
                        $query->like('ticket_subject', '%' . $filterData['sword'] . '%'),
                        $query->like('ticket_text', '%' . $filterData['sword'] . '%')
                    ]
                );
            }
        }
        if ($filterData) {
            $query->matching($query->logicalAnd($constraints));
        }
        return $query->execute();
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
        $totalTickets = $queryBuilder
            ->count('uid')
            ->from('tx_nshelpdesk_domain_model_tickets')
            ->execute()
            ->fetchColumn(0);

        $total5Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(5, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchColumn(0);

        $total4Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(4, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchColumn(0);

        $total3Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(3, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchColumn(0);

        $total2Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(2, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchColumn(0);

        $total1Ratings = $queryBuilder->count('uid')->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchColumn(0);
        $totalAvg = ($total5Ratings * 5 + $total4Ratings * 4 + $total3Ratings * 3 + $total2Ratings * 2 + $total1Ratings * 1);

        $totalRatings = 0;
        if ($totalAvg > 0) {
            $totalRatings = $totalAvg / ($total5Ratings + $total4Ratings + $total3Ratings + $total2Ratings + $total1Ratings);
        }
        return $totalRatings;
    }
}
