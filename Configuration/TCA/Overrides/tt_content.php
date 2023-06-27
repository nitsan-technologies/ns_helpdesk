<?php

defined('TYPO3') || die('Access denied');

// Ticket Listing Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NsHelpdesk',
    'HelpdeskList',
    'List View'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nshelpdesk_helpdesklist'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['nshelpdesk_helpdesklist'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'nshelpdesk_helpdesklist',
    'FILE:EXT:ns_helpdesk/Configuration/FlexForms/NsHelpdesk_ListView.xml'
);

// Ticket Submission Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NsHelpdesk',
    'HelpdeskTicket',
    'Ticket Submission'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nshelpdesk_helpdeskticket'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['nshelpdesk_helpdeskticket'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'nshelpdesk_helpdeskticket',
    'FILE:EXT:ns_helpdesk/Configuration/FlexForms/NsHelpdesk_TicketSubmission.xml'
);
