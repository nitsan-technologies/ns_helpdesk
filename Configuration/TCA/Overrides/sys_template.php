<?php
defined('TYPO3_MODE') || die('Access denied.');

$_EXTKEY = $GLOBALS['_EXTKEY'] = 'ns_helpdesk';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
  $_EXTKEY,
  'Configuration/TypoScript',
  'Helpdesk'
);
