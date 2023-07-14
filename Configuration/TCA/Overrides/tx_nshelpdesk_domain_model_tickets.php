<?php

$GLOBALS['TCA']['tx_nshelpdesk_domain_model_tickets']['columns']['slug']['config'] = [
    'type' => 'slug',
    'size' => 50,
    'generatorOptions' => [
        'fields' => ['ticket_subject'],
        'replacements' => [
            '/' => '-'
        ],
    ],
    'fallbackCharacter' => '-',
    'eval' => 'uniqueInSite',
    'default' => ''
];
