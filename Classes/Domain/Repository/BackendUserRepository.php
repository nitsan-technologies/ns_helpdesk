<?php

declare(strict_types=1);

namespace NITSAN\NsHelpdesk\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class BackendUserRepository extends Repository
{

    /**
     * Overwrite createQuery to don't respect enable fields
     *
     * @return QueryInterface
     */
    public function createQuery()
    {
        $query = parent::createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        return $query;
    }

}
