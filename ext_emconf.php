<?php

$EM_CONF['ns_helpdesk'] = [
    'title' => 'TYPO3 Helpdesk Extension',
    'description' => 'Easily manage customer support on your TYPO3 site with a ticket-based helpdesk system. Includes dashboard, ticket management, email alerts, file uploads, and user access control.',

    'category' => 'plugin',
    'author' => 'Team T3Planet',
    'author_email' => 'info@t3planet.de',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'version' => '13.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-13.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'classmap' => ['Classes/']
    ]
];
