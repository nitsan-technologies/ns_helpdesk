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
 * Assignee
 */
class DefaultAssignee extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * ticketType
     *
     * @var int
     */
    protected $ticketType = 0;

    /**
     * assigneeId
     *
     * @var int
     */
    protected $assigneeId = 0;

    /**
    * isDefault
    *
    * @var bool
    */
    protected $isDefault;

    /**
     * Returns the ticketType
     *
     * @return int $ticketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Sets the ticketType
     *
     * @param int $ticketType
     * @return void
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;
    }

    /**
     * Returns the assigneeId
     *
     * @return int $assigneeId
     */
    public function getAssigneeId()
    {
        return $this->assigneeId;
    }

    /**
     * Sets the assigneeId
     *
     * @param int $assigneeId
     * @return void
     */
    public function setAssigneeId($assigneeId)
    {
        $this->assigneeId = $assigneeId;
    }

    /**
     * Returns the isDefault
     *
     * @return bool $isDefault
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Sets the isDefault
     *
     * @param bool $isDefault
     * @return void
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }
}
