Pathology Lab System


This project is a pathology lab reporting system, which can be used to publish medical test result reports to patients.

Functional Specifications

Create a pathology lab reporting web application where medical test result reports can be published to the patients:Operator users should be able to log in to the system to perform following privileged tasks. Patients cannot access these pages. Reports CRUD (Multiple tests and results in each report) Patients CRUD (including pass code) Lab sends a text message to the patient with a pass code to log in (out of scope). Patient user could log in using his name (auto complete field) and pass code sent to him. And then can do the following
Display list of his reports Display a report details as a page Export a report as PDF Mail a report as PDF

Pre-Requirements ----------------- 

1. PHP version 5.4 or newer is recommended.      
(It should work on 5.2.4 as well, but I strongly advise you NOT to run such old versions of PHP, because of potential security and performance issues, as well as missing features.)      

2. MySQL (5.1+) via the mysql (deprecated), mysqli and pdo drivers.      

3. PHP OpenSSL extension for PHPMailer SSL login.      

4. PHP CURL extension for third party SMS service.      

5. PDF plugin in browser to view PDF files.       

How To Install The application    ------------------------------


   1. Copy Source files into web server document directory ( Ex. project/src/ to /var/www/htdocs/path)
   
   2. Enter Project base url in config file. (path: /application/config/config.php   ex. $config['base_url'] =             'http://localhost/path';)
   
   3. Enter MySQL DB database credentials in database config file (path: /application/config/database.php)
   
   4. Import pathology_lab_121216.sql file (path: /database/pathology_lab_121216.sql) to desired database
   
   5. Enter Email configuration deatail in email confir file (path: /application/config/email.php)
   
   6. Optional Twilio API SMS configration can be done in helper file   (path: /application/helper/twilio_helper.php)
   
   7. Thats it! you can open patient report area like http://localhost/projectpath/  and admin area http://localhost/projectpath/admin         ( username: admin and password: johnVer)
   
   
   
   Feedback to Improve -------------------    
   
   1. Patient name and passcode authendication will not suits for all situation. There is chance to have the same name and same    password. However i have managed to prevent dublicate But better to give unique username instead of Full Name.    
