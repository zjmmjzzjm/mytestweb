<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'Pages/View';
$route['search/(:any)'] = 'Pages/search/$1';
$route['search/(:any)/(:any)'] = 'Pages/search/$1/$2';
$route['detail/(:any)'] = 'Pages/detail/$1';
$route['404_override'] = '';
