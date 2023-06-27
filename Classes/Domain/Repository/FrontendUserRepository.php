<?php

namespace NITSAN\NsHelpdesk\Domain\Repository;

class FrontendUserRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findByUserId($uid)
    {
        $query = $this->createQuery();
        $query->equals('uid', $uid);
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->logicalAnd(
            $query->equals('uid', $uid)
        ));

        return $query->execute();
    }

}
