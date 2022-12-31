<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('moritzebeling/kirby-maintenance', [

    'options' => [
        'ignore' => [],
        'text' => 'This website is currently under maintenance and will be back online soon.'
    ],

    'hooks' => [
        'route:before' => function ($route, $path, $method) {

            if( !option('maintenance', false) ){
                // maintenance mode is off
                return;
            }

            $kirby = kirby();

            $urls = $kirby->urls()->toArray();
            $ignore = array_merge(option('moritzebeling.kirby-maintenance.ignore', []),[
                'assets',
                'api',
                'media',
                'panel',
            ]);

            foreach ($ignore as $i) {
                if( in_array($i,$urls) ){
                    // map ignored paths to kirby ingredient urls
                    $i = $urls[$i];
                }
                if( str_starts_with( $path, $i ) ){
                    // path is ignored or reserved by kirby
                    return;
                }
            }

            if( $kirby->user() ){
                // user is logged in
                return;
            }

            // send 503 status code

            $protocol = $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ? 'HTTP/1.1' : 'HTTP/1.0';
            header( $protocol . ' 503 Service Unavailable', true, 503 );

            echo option('moritzebeling.kirby-maintenance.text');
            exit;
        }
    ]

]);