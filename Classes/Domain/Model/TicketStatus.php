<?php
namespace NITSAN\NsHelpdesk\Domain\Model;

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
class TicketStatus extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * statusTitle
     *
     * @var string
     */
    protected $statusTitle = '';

    /**
     * statusColor
     *
     * @var string
     */
    protected $statusColor = '';

    /**
     * Returns the statusTitle
     *
     * @return string $statusTitle
     */
    public function getStatusTitle()
    {
        return $this->statusTitle;
    }

    /**
     * Sets the statusTitle
     *
     * @param string $statusTitle
     * @return void
     */
    public function setStatusTitle($statusTitle)
    {
        $this->statusTitle = $statusTitle;
    }

    /**
     * Returns the statusColor
     *
     * @return string $statusColor
     */
    public function getStatusColor()
    {
        return $this->statusColor;
    }

    /**
     * Sets the statusColor
     *
     * @param string $statusColor
     * @return void
     */
    public function setStatusColor($statusColor)
    {
        $this->statusColor = $statusColor;
    }
}
