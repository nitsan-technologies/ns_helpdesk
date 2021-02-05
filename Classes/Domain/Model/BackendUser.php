<?php

namespace NITSAN\NsHelpdesk\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class BackendUser extends AbstractEntity
{
    /**
     * avatar
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $avatar = null;

    /**
     * Returns the $avatar
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Sets the $avatar
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $avatar
     * @return void
     */
    public function setAvatar(\TYPO3\CMS\Extbase\Domain\Model\FileReference $avatar)
    {
        $this->avatar = $avatar;
    }
}
