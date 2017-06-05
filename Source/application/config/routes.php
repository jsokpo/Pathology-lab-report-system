<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['default_controller'] = 'patient/index';
$route['404_override'] = '';

/*patient*/
$route['patient'] = 'patient/index';

/*admin*/
$route['admin'] = 'user/index';
$route['admin/signup'] = 'user/signup';
$route['admin/create_member'] = 'user/create_member';
$route['admin/login'] = 'user/index';
$route['admin/logout'] = 'user/logout';
$route['admin/login/validate_credentials'] = 'user/validate_credentials';
$route['admin/dashboard'] = 'user/dashboard';

$route['admin/patients'] = 'admin_patients/index';
$route['admin/patients/api'] = 'admin_patients/api';
$route['admin/patients/add'] = 'admin_patients/add';
$route['admin/patients/update'] = 'admin_patients/update';
$route['admin/patients/update/(:any)'] = 'admin_patients/update/$1';
$route['admin/patients/delete/(:any)'] = 'admin_patients/delete/$1';
$route['admin/patients/sms/(:any)'] = 'admin_patients/sms/$1'; //$1 = page number
$route['admin/patients/(:any)'] = 'admin_patients/index/$1'; //$1 = page number


$route['admin/reports'] = 'admin_reports/index';
$route['admin/reports/email/(:any)'] = 'admin_reports/email/$1';
$route['admin/reports/add'] = 'admin_reports/add';
$route['admin/reports/update'] = 'admin_reports/update';
$route['admin/reports/update/(:any)'] = 'admin_reports/update/$1';
$route['admin/reports/delete/(:any)'] = 'admin_reports/delete/$1';
$route['admin/reports/pdf/(:any)'] = 'admin_reports/pdf/$1';
$route['admin/reports/view_pdf/(:any)'] = 'admin_reports/view_pdf/$1';
$route['admin/reports/(:any)'] = 'admin_reports/index/$1'; //$1 = page number



/* End of file routes.php */
/* Location: ./application/config/routes.php */