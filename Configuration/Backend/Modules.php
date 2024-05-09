<?php
use NITSAN\NsHelpdesk\Controller\NsConstantEditorController;
use NITSAN\NsHelpdesk\Controller\TicketsController;

return [
    'nitsan_module' => [
        'labels' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/BackendModule.xlf',
        'icon' => 'EXT:ns_helpdesk/Resources/Public/Icons/module-nitsan.svg',
        'iconIdentifier' => 'module-nshelpdesk',
        'position' => ['after' => 'web'],
    ],
    'nitsan_nshelpdeskmodule_configuration' => [
        'parent' => 'nitsan_module',
        'position' => ['before' => 'top'],
        'access' => 'user',
        'path' => '/module/nitsan/NsHelpdeskConfiguration',
        'icon'   => 'EXT:ns_helpdesk/Resources/Public/Icons/module-nshelpdesk.svg',
        'labels' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_helpdesk_conf.xlf',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'extensionName' => 'NsHelpdesk',
        'routes' => [
            '_default' => [
                'target' => NsConstantEditorController::class . '::handleRequest',
            ],
        ],
        'moduleData' => [
            'selectedTemplatePerPage' => [],
            'selectedCategory' => '',
        ],
    ],
    'nitsan_nshelpdeskmodule_dashboard' => [
        'parent' => 'nitsan_module',
        'position' => ['before' => 'top'],
        'access' => 'user',
        'path' => '/module/nitsan/NsHelpdeskDashboard',
        'icon' => 'EXT:ns_helpdesk/Resources/Public/Icons/module-nshelpdesk.svg',
        'labels' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_helpdesk_dashboard.xlf',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'extensionName' => 'NsHelpdesk',
        'controllerActions' => [
            TicketsController::class => [
                'dashboard',
                'list',
                'show',
                'saveConfiguration',
                'saveConstant',
                'closeTicket',
                'reopenTicket',
            ]
        ],
    ],
];
