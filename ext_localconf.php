<?php

defined('TYPO3') || die('Access denied.');

use NITSAN\NsHelpdesk\Hooks\DataHandler;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use NITSAN\NsHelpdesk\Controller\TicketsController;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$_EXTKEY = 'ns_helpdesk';

$ticketsController = TicketsController::class;

ExtensionUtility::configurePlugin(
    'NsHelpdesk',
    'HelpdeskList',
    [
        $ticketsController => 'list, show, closeTicket, reopenTicket',
    ],
    // non-cacheable actions
    [
        $ticketsController => 'list, show, closeTicket, reopenTicket',
    ]
);

ExtensionUtility::configurePlugin(
    'NsHelpdesk',
    'HelpdeskTicket',
    [
        $ticketsController => 'new, create, quickPopupTicket',
    ],
    // non-cacheable actions
    [
        $ticketsController => 'new, create, quickPopupTicket',
    ]
);

$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

$icon = [
    'ns_helpdesk-plugin-helpdesk'
    , 'module-nshelpdesk'
    , 'parent-module-nshelpdesk'
];

foreach ($icon as $value) {
    $iconRegistry->registerIcon(
        $value,
        SvgIconProvider::class,
        ['source' => 'EXT:ns_helpdesk/Resources/Public/Icons/' . $value . '.svg']
    );
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['NsHelpdesk'] = [
    'NITSAN\NsHelpdesk\ViewHelpers',
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ns_helpdesk'] =
    DataHandler::class;

