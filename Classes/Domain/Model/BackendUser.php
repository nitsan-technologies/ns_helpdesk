<?php

namespace NITSAN\NsHelpdesk\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class BackendUser extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $userName = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var bool
     */
    protected bool $isAdministrator = false;

    /**
     * @var bool
     */
    protected bool $isDisabled = false;

    /**
     * @var \DateTime|null
     */
    protected \DateTime|null $startDateAndTime;

    /**
     * @var \DateTime|null
     */
    protected \DateTime|null $endDateAndTime;

    /**
     * @var string
     */
    protected string $email = '';

    /**
     * @var string
     */
    protected string $realName = '';

    /**
     * @var \DateTime|null
     */
    protected \DateTime|null $lastLoginDateAndTime;

    /**
     * Gets the user name.
     *
     * @return string the user name, will not be empty
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Sets the user name.
     *
     * @param string $userName the user name to set, must not be empty
     */
    public function setUserName(string $userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Checks whether this user is an administrator.
     *
     * @return bool whether this user is an administrator
     */
    public function getIsAdministrator(): bool
    {
        return $this->isAdministrator;
    }

    /**
     * Sets whether this user should be an administrator.
     *
     * @param bool $isAdministrator whether this user should be an administrator
     */
    public function setIsAdministrator(bool $isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;
    }

    /**
     * Checks whether this user is disabled.
     *
     * @return bool whether this user is disabled
     */
    public function getIsDisabled(): bool
    {
        return $this->isDisabled;
    }

    /**
     * Sets whether this user is disabled.
     *
     * @param bool $isDisabled whether this user is disabled
     */
    public function setIsDisabled(bool $isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }

    /**
     * Returns the point in time from which this user is enabled.
     *
     * @return \DateTime|null the start date and time
     */
    public function getStartDateAndTime(): \DateTime|null
    {
        return $this->startDateAndTime;
    }

    /**
     * Sets the point in time from which this user is enabled.
     *
     * @param \DateTime|null $dateAndTime the start date and time
     */
    public function setStartDateAndTime(\DateTime|null $dateAndTime = null)
    {
        $this->startDateAndTime = $dateAndTime;
    }

    /**
     * Returns the point in time before which this user is enabled.
     *
     * @return \DateTime|null the end date and time
     */
    public function getEndDateAndTime(): \DateTime|null
    {
        return $this->endDateAndTime;
    }

    /**
     * Sets the point in time before which this user is enabled.
     *
     * @param \DateTime|null $dateAndTime the end date and time
     */
    public function setEndDateAndTime(\DateTime|null $dateAndTime = null)
    {
        $this->endDateAndTime = $dateAndTime;
    }

    /**
     * Gets the e-mail address of this user.
     *
     * @return string the e-mail address, might be empty
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the e-mail address of this user.
     *
     * @param string $email the e-mail address, may be empty
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns this user's real name.
     *
     * @return string the real name. might be empty
     */
    public function getRealName(): string
    {
        return $this->realName;
    }

    /**
     * Sets this user's real name.
     *
     * @param string $realName the user's real name, may be empty.
     */
    public function setRealName(string $realName)
    {
        $this->realName = $realName;
    }

    /**
     * Checks whether this user is currently activated.
     *
     * This function takes the "disabled" flag, the start date/time and the end date/time into account.
     *
     * @return bool whether this user is currently activated
     */
    public function isActivated(): bool
    {
        return !$this->getIsDisabled() && $this->isActivatedViaStartDateAndTime() && $this->isActivatedViaEndDateAndTime();
    }

    /**
     * Checks whether this user is activated as far as the start date and time is concerned.
     *
     * @return bool whether this user is activated as far as the start date and time is concerned
     */
    protected function isActivatedViaStartDateAndTime()
    {
        if ($this->getStartDateAndTime() === null) {
            return true;
        }
        $now = new \DateTime('now');
        return $this->getStartDateAndTime() <= $now;
    }

    /**
     * Checks whether this user is activated as far as the end date and time is concerned.
     *
     * @return bool whether this user is activated as far as the end date and time is concerned
     */
    protected function isActivatedViaEndDateAndTime()
    {
        if ($this->getEndDateAndTime() === null) {
            return true;
        }
        $now = new \DateTime('now');
        return $now <= $this->getEndDateAndTime();
    }

    /**
     * Gets this user's last login date and time.
     *
     * @return \DateTime|null this user's last login date and time, will be NULL if this user has never logged in before
     */
    public function getLastLoginDateAndTime(): \DateTime|null
    {
        return $this->lastLoginDateAndTime;
    }

    /**
     * Sets this user's last login date and time.
     *
     * @param \DateTime|null $dateAndTime this user's last login date and time
     */
    public function setLastLoginDateAndTime(\DateTime|null $dateAndTime = null)
    {
        $this->lastLoginDateAndTime = $dateAndTime;
    }
}
