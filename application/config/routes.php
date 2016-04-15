<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'Pages/View';
$route['search/(:any)'] = 'Pages/search/$1';
$route['search'] = 'Pages/search';
$route['search/(:any)/(:any)'] = 'Pages/search/$1/$2';
$route['recent'] = 'Pages/recent';
$route['recent/(:any)'] = 'Pages/recent/$1';
$route['recent/(:any)/(:any)'] = 'Pages/recent/$1/$2';
$route['detail/(:any)'] = 'Pages/detail/$1';
$route['404_override'] = '';
