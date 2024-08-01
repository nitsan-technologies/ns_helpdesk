<?php

namespace NITSAN\NsHelpdesk\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class AbstractUtility
 *
 */
abstract class AbstractUtility
{
    /**
     * @return BackendUserAuthentication
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

}
