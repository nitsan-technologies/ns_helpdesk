<?php
namespace NITSAN\NsHelpdesk\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * The repository for Helpdesk
 */
class HelpdeskRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function checkApiData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nshelpdesk_domain_model_apidata');
        $queryBuilder
            ->select('*')
            ->from('tx_nshelpdesk_domain_model_apidata');
        $query = $queryBuilder->execute();
        return $query->fetch();
    }
    public function insertNewData($data)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nshelpdesk_domain_model_apidata');
        $row = $queryBuilder
            ->insert('tx_nshelpdesk_domain_model_apidata')
            ->values($data);

        $query = $queryBuilder->execute();
        return $query;
    }
    public function curlInitCall($url)
    {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curlSession);
        curl_close($curlSession);

        return $data;
    }
    public function deleteOldApiData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nshelpdesk_domain_model_apidata');
        $queryBuilder
            ->delete('tx_nshelpdesk_domain_model_apidata')
            ->where(
                $queryBuilder->expr()->comparison('last_update', '<', 'DATE_SUB(NOW() , INTERVAL 1 DAY)')
            )
            ->execute();
    }
}
