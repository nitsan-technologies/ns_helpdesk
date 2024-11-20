<?php

return [
    // required import configurations of other extensions,
    // in case a module imports from another package
    'dependencies' => ['backend'],
    'imports' => [
        // recursive definiton, all *.js files in this folder are import-mapped
        // trailing slash is required per importmap-specification
        '@nitsan/ns-helpdesk/' => 'EXT:ns_helpdesk/Resources/Public/JavaScript/',
    ],
];
