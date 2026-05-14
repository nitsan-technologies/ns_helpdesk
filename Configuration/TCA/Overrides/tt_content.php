<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied');

// Ticket Listing Plugin
$pluginSignatureList = ExtensionUtility::registerPlugin(
    'NsHelpdesk',
    'HelpdeskList',
    'Helpdesk - List View',
    'ns_helpdesk-plugin-helpdesk',
    'plugins'
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;plugin,pi_flexform,',
    $pluginSignatureList,
    'after:subheader',
);
// @extensionScannerIgnoreLine
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:ns_helpdesk/Configuration/FlexForms/NsHelpdesk_ListView.xml',
    $pluginSignatureList
);

// Ticket Submission Plugin
$pluginSignatureTicket = ExtensionUtility::registerPlugin(
    'NsHelpdesk',
    'HelpdeskTicket',
    'Helpdesk - Ticket Submission',
    'ns_helpdesk-plugin-helpdesk',
    'plugins'
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;plugin,pi_flexform,',
    $pluginSignatureTicket,
    'after:subheader',
);
// @extensionScannerIgnoreLine
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:ns_helpdesk/Configuration/FlexForms/NsHelpdesk_TicketSubmission.xml',
    $pluginSignatureTicket
);