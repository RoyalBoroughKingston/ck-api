<?php

return [

    'pagination_results' => 10,

    'google_api_key' => env('GOOGLE_API_KEY'),

    /*
     * Available drivers: 'stub', 'nominatim', 'google'
     */
    'geocode_driver' => env('GEOCODE_DRIVER', 'stub'),

];
