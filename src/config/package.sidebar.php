<?php

return [
    'srp'     => [
        [
            'name'  => 'Request',
            'permission' => 'can submit srp requests',
            'route' => 'srp.request',
            'viewbox' => '0 0 24 24',
            'content' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />',
        ],
        [
            'name'  => 'Admin',
            'permission' => 'accept or reject srp requests',
            'route' => 'review.srp.requests',
            'viewbox' => '0 0 24 24',
            'content' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />',
        ]
    ]
];
