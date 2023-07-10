<?php

declare(strict_types=1);

use NITSAN\NsHelpdesk\Domain\Model\BackendUser;
use NITSAN\NsHelpdesk\Domain\Model\FrontendUser;
use NITSAN\NsHelpdesk\Domain\Model\FrontendUserGroup;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    BackendUser::class => [
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
    FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
];
