<?php

namespace NITSAN\NsHelpdesk\Domain\Repository;

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
 * The repository for Tickets
 */
class TicketStatusRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function getFromAll()
    {
        $querySettings = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
}
