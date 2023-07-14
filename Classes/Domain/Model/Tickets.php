<?php

namespace NITSAN\NsHelpdesk\Domain\Model;

use NITSAN\NsHelpdesk\Domain\Model\BackendUser;
use NITSAN\NsHelpdesk\Domain\Model\FrontendUser;
use NITSAN\NsHelpdesk\Domain\Model\TicketStatus;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use DateTime;

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
 * Tickets
 */
class Tickets extends AbstractEntity
{
    /**
     * ticketSubject
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected string $ticketSubject = '';

    /**
     * ticketText
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected string $ticketText = '';

    /**
     * ticketPostDate
     *
     * @var \DateTime
     */
    protected DateTime $ticketPostDate;

    /**
     * ticketStatus
     *
     * @var TicketStatus
     */
    protected $ticketStatus;

    /**
     * assigneeId
     *
     * @var BackendUser
     */
    protected BackendUser $assigneeId;

    /**
     * userId
     *
     * @var FrontendUser
     */
    protected FrontendUser $userId;

    /**
     * slug
     *
     * @var string
     */
    protected string $slug = '';

    /**
     * ticketRating
     *
     * @var int
     */
    protected int $ticketRating = 0;

    /**
     * Returns the ticketSubject
     *
     * @return string $ticketSubject
     */
    public function getTicketSubject(): string
    {
        return $this->ticketSubject;
    }

    /**
     * Sets the ticketSubject
     *
     * @param string $ticketSubject
     * @return void
     */
    public function setTicketSubject(string $ticketSubject)
    {
        $this->ticketSubject = $ticketSubject;
    }

    /**
     * Returns the ticketText
     *
     * @return string $ticketText
     */
    public function getTicketText(): string
    {
        return $this->ticketText;
    }

    /**
     * Sets the ticketText
     *
     * @param string $ticketText
     * @return void
     */
    public function setTicketText(string $ticketText)
    {
        $this->ticketText = $ticketText;
    }

    /**
     * Returns the ticketPostDate
     *
     * @return \DateTime $ticketPostDate
     */
    public function getTicketPostDate(): DateTime
    {
        return $this->ticketPostDate;
    }

    /**
     * Sets the ticketPostDate
     *
     * @param \DateTime $ticketPostDate
     * @return void
     */
    public function setTicketPostDate(\DateTime $ticketPostDate)
    {
        $this->ticketPostDate = $ticketPostDate;
    }

    /**
     * Returns the ticketStatus
     *
     * @return TicketStatus $ticketStatus
     */
    public function getTicketStatus()
    {
        return $this->ticketStatus;
    }

    /**
     * Sets the ticketStatus
     *
     * @param TicketStatus $ticketStatus
     * @return void
     */
    public function setTicketStatus(TicketStatus $ticketStatus)
    {
        $this->ticketStatus = $ticketStatus;
    }

    /**
     * Returns the assigneeId
     *
     * @return BackendUser $assigneeId
     */
    public function getAssigneeId(): BackendUser
    {
        return $this->assigneeId;
    }

    /**
     * Sets the assigneeId
     *
     * @param BackendUser $assigneeId
     * @return void
     */
    public function setAssigneeId(BackendUser $assigneeId)
    {
        $this->assigneeId = $assigneeId;
    }

    /**
     * Returns the userId
     *
     * @return FrontendUser $userId
     */
    public function getUserId(): FrontendUser
    {
        return $this->userId;
    }

    /**
     * Sets the userId
     *
     * @param FrontendUser $userId
     * @return void
     */
    public function setUserId(FrontendUser $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Returns the slug
     *
     * @return string $slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Sets the slug
     *
     * @param string $slug
     * @return void
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Returns the ticketRating
     *
     * @return int $ticketRating
     */
    public function getTicketRating(): int
    {
        return $this->ticketRating;
    }

    /**
     * Sets the ticketRating
     *
     * @param int $ticketRating
     * @return void
     */
    public function setTicketRating(int $ticketRating)
    {
        $this->ticketRating = $ticketRating;
    }
}
