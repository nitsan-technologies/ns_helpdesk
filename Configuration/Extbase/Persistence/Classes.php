<?php

declare(strict_types=1);

return [
    \NITSAN\NsHelpdesk\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \NITSAN\NsHelpdesk\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
        'properties' => [
            'userName' => [
                'fieldName' => 'username',
            ],
            'isAdministrator' => [
                'fieldName' => 'admin',
            ],
            'isDisabled' => [
                'fieldName' => 'disable',
            ],
            'realName' => [
                'fieldName' => 'realName',
            ],
            'startDateAndTime' => [
                'fieldName' => 'starttime',
            ],
            'endDateAndTime' => [
                'fieldName' => 'endtime',
            ],
            'lastLoginDateAndTime' => [
                'fieldName' => 'lastlogin',
            ],
            'allowedLanguages' => [
                'fieldName' => 'allowed_languages',
            ],
            'fileMountPoints' => [
                'fieldName' => 'file_mountpoints',
            ],
            'dbMountPoints' => [
                'fieldName' => 'db_mountpoints',
            ],
            'backendUserGroups' => [
                'fieldName' => 'usergroup',
            ],
        ],
    ],
    \NITSAN\NsHelpdesk\Domain\Model\FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
];
