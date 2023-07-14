<?php

namespace NITSAN\NsHelpdesk\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class FrontendUserRepository extends Repository
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
