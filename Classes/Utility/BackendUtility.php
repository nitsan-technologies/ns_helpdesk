<?php

namespace NITSAN\NsHelpdesk\Utility;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Backend utility functions
 *
 */
class BackendUtility extends AbstractUtility
{
    /**
     * Create an URI to edit any record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createEditUri($tableName, $identifier, $addReturnUrl = true)
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $identifier => 'edit',
                ],
            ],
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return self::getModuleUrl('record_edit', $uriParameters);
    }

    /**
     * Create an URI to new record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createNewUri($tableName, $identifier, $addReturnUrl = true)
    {
        if ($identifier <= 0) {
            $identifier = self::getPidFromBackendPage(self::getReturnUrl());
        }

        $uriParameters = [
            'edit' => [
                $tableName => [
                    $identifier => 'new',
                ],
            ],
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return self::getModuleUrl('record_edit', $uriParameters);
    }

    /**
     * Get return URL from current request
     *
     * @return string
     */
    protected static function getReturnUrl()
    {
        return GeneralUtility::getIndpEnv('REQUEST_URI');
    }

    /**
     * Returns the URL to a given module mainly used for visibility settings or deleting a record via AJAX
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     */
    public static function getModuleUrl($moduleName, $urlParameters = [])
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return $uriBuilder->buildUriFromRoute($moduleName, $urlParameters);
    }

    /**
     * @param string $returnUrl
     * @return int
     */
    public static function getPidFromBackendPage($returnUrl = '')
    {
        $getData = $_GET;
        $postData = $_POST;
        $requestData = array_merge((array)$getData, (array)$postData);
        if (empty($returnUrl)) {
            $returnUrl = $requestData['returnUrl'];
        }
        $urlParts = parse_url($returnUrl);
        parse_str($urlParts['query'], $queryParts);
        return (int) $queryParts['id'];
    }
}
