<?php

return [
    'superAdmin' => ['admin'],
    'commonController' => ['LoginController'],
    'commonMethod' => ['index', 'login'],
    'commonAccess' => [
       'LoginController@login','UserController@get_date','LoginController@index','AdminController@priListTest'
    ],
];