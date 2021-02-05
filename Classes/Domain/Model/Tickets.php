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
 * Tickets
 */
class Tickets extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * ticketSubject
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $ticketSubject = '';

    /**
     * ticketText
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $ticketText = '';

    /**
     * ticketPostDate
     *
     * @var \DateTime
     */
    protected $ticketPostDate = null;

    /**
     * ticketStatus
     *
     * @var \NITSAN\NsHelpdesk\Domain\Model\TicketStatus
     */
    protected $ticketStatus = null;

    /**
     * assigneeId
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\BackendUser
     */
    protected $assigneeId = null;

    /**
     * userId
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $userId = null;

    /**
     * slug
     *
     * @var string
     */
    protected $slug = '';

    /**
     * ticketRating
     *
     * @var int
     */
    protected $ticketRating = 0;

    /**
     * Returns the ticketSubject
     *
     * @return string $ticketSubject
     */
    public function getTicketSubject()
    {
        return $this->ticketSubject;
    }

    /**
     * Sets the ticketSubject
     *
     * @param string $ticketSubject
     * @return void
     */
    public function setTicketSubject($ticketSubject)
    {
        $this->ticketSubject = $ticketSubject;
    }

    /**
     * Returns the ticketText
     *
     * @return string $ticketText
     */
    public function getTicketText()
    {
        return $this->ticketText;
    }

    /**
     * Sets the ticketText
     *
     * @param string $ticketText
     * @return void
     */
    public function setTicketText($ticketText)
    {
        $this->ticketText = $ticketText;
    }

    /**
     * Returns the ticketPostDate
     *
     * @return \DateTime $ticketPostDate
     */
    public function getTicketPostDate()
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
     * @return \NITSAN\NsHelpdesk\Domain\Model\TicketStatus $ticketStatus
     */
    public function getTicketStatus()
    {
        return $this->ticketStatus;
    }

    /**
     * Sets the ticketStatus
     *
     * @param \NITSAN\NsHelpdesk\Domain\Model\TicketStatus $ticketStatus
     * @return void
     */
    public function setTicketStatus(\NITSAN\NsHelpdesk\Domain\Model\TicketStatus $ticketStatus)
    {
        $this->ticketStatus = $ticketStatus;
    }

    /**
     * Returns the assigneeId
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\BackendUser $assigneeId
     */
    public function getAssigneeId()
    {
        return $this->assigneeId;
    }

    /**
     * Sets the assigneeId
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUser $assigneeId
     * @return void
     */
    public function setAssigneeId(\TYPO3\CMS\Extbase\Domain\Model\BackendUser $assigneeId)
    {
        $this->assigneeId = $assigneeId;
    }

    /**
     * Returns the userId
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the userId
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userId
     * @return void
     */
    public function setUserId(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Returns the slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the slug
     *
     * @param string $slug
     * @return void
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Returns the ticketRating
     *
     * @return int $ticketRating
     */
    public function getTicketRating()
    {
        return $this->ticketRating;
    }

    /**
     * Sets the ticketRating
     *
     * @param int $ticketRating
     * @return void
     */
    public function setTicketRating($ticketRating)
    {
        $this->ticketRating = $ticketRating;
    }
}
