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
 * Assignee
 */
class DefaultAssignee extends AbstractEntity
{
    /**
     * ticketType
     *
     * @var int
     */
    protected int $ticketType = 0;

    /**
     * assigneeId
     *
     * @var int
     */
    protected int $assigneeId = 0;

    /**
    * isDefault
    *
    * @var bool
    */
    protected bool $isDefault;

    /**
     * Returns the ticketType
     *
     * @return int $ticketType
     */
    public function getTicketType(): int
    {
        return $this->ticketType;
    }

    /**
     * Sets the ticketType
     *
     * @param int $ticketType
     * @return void
     */
    public function setTicketType(int $ticketType)
    {
        $this->ticketType = $ticketType;
    }

    /**
     * Returns the assigneeId
     *
     * @return int $assigneeId
     */
    public function getAssigneeId(): int
    {
        return $this->assigneeId;
    }

    /**
     * Sets the assigneeId
     *
     * @param int $assigneeId
     * @return void
     */
    public function setAssigneeId(int $assigneeId)
    {
        $this->assigneeId = $assigneeId;
    }

    /**
     * Returns the isDefault
     *
     * @return bool $isDefault
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * Sets the isDefault
     *
     * @param bool $isDefault
     * @return void
     */
    public function setIsDefault(bool $isDefault)
    {
        $this->isDefault = $isDefault;
    }
}
