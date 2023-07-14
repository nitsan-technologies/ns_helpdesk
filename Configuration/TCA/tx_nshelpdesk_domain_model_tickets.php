<?php

$ll = 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_tickets.';
$corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_tickets',
        'label' => 'ticket_subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'ticket_subject,ticket_text,slug',
        'iconfile' => 'EXT:ns_helpdesk/Resources/Public/Icons/tx_nshelpdesk_domain_model_tickets.svg',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
        'hideTable' => true
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ticket_subject, slug, ticket_text, ticket_post_date, ticket_status, ticket_rating, assignee_id, user_id,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $corell . 'LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => $corell . 'LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => $corell . 'LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'ticket_subject' => [
            'exclude' => true,
            'label' => $ll . 'ticket_subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true
            ],
        ],
        'ticket_text' => [
            'exclude' => true,
            'label' => $ll . 'ticket_text',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                // 'required' => true
            ],

        ],
        'ticket_post_date' => [
            'exclude' => true,
            'label' => $ll . 'ticket_post_date',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'size' => 10,
                'default' => time()
            ],
        ],
        'ticket_status' => [
            'exclude' => true,
            'label' => $ll . 'ticket_status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_nshelpdesk_domain_model_ticketstatus',
                'foreign_table_where' => 'AND {#tx_nshelpdesk_domain_model_ticketstatus}.{#pid} >= 1',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'assignee_id' => [
            'exclude' => true,
            'label' => $ll . 'assignee_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'user_id' => [
            'exclude' => true,
            'label' => $ll . 'user_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [
                    [
                        'label' => $ll . 'selectfeuser',
                        'value' => -1
                    ],
                ],
                'minitems' => 0,
                'maxitems' => 1,
                'required' => true
            ],
        ],
        'slug' => [
            'label' => $ll . 'slug',
            'exclude' => 1,
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,alphanum_x,lower,unique',
                'readOnly' => true
            ]
        ],
        'ticket_rating' => [
            'label' => $ll . 'ticket_rating',
            'exclude' => 1,
            'config' => [
                'type' => 'input',
                'default' => 0,
                'readOnly' => 1
            ],
        ],
    ],
];
