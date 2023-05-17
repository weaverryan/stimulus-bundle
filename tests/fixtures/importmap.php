<?php

return [
    'app' => [
        'path' => 'app.js',
        'preload' => true,
    ],
    '@hotwired/stimulus' => [
        'url' => 'https://ga.jspm.io/npm:@hotwired/stimulus@3.2.1/dist/stimulus.js',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => __DIR__.'/../../assets/loader.js',
    ],
];
