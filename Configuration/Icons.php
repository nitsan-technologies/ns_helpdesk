<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ns_helpdesk-plugin-helpdesk' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_helpdesk/Resources/Public/Icons/ns_helpdesk-plugin-helpdesk.svg',
    ],
    'module-nshelpdesk' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_helpdesk/Resources/Public/Icons/module-nshelpdesk.svg',
    ],
    'parent-module-nshelpdesk' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:ns_helpdesk/Resources/Public/Icons/Extension.svg',
    ],
];