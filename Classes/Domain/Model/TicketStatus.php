<?php

namespace NITSAN\NsHelpdesk\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
 * TicketStatus
 */
class TicketStatus extends AbstractEntity
{
    /**
     * statusTitle
     *
     * @var string
     */
    protected string $statusTitle = '';

    /**
     * statusColor
     *
     * @var string
     */
    protected string $statusColor = '';

    /**
     * Returns the statusTitle
     *
     * @return string $statusTitle
     */
    public function getStatusTitle(): string
    {
        return $this->statusTitle;
    }

    /**
     * Sets the statusTitle
     *
     * @param string $statusTitle
     * @return void
     */
    public function setStatusTitle(string $statusTitle)
    {
        $this->statusTitle = $statusTitle;
    }

    /**
     * Returns the statusColor
     *
     * @return string $statusColor
     */
    public function getStatusColor(): string
    {
        return $this->statusColor;
    }

    /**
     * Sets the statusColor
     *
     * @param string $statusColor
     * @return void
     */
    public function setStatusColor(string $statusColor)
    {
        $this->statusColor = $statusColor;
    }
}
