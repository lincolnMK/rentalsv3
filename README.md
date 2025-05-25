# rentalsv3
rental management system for department of housing
To set it up on your local machine using XAMPP or WAMP:

downloaad the code or folder https://github.com/lincolnmk/rentalsv3 
Place the rentalsv3 folder inside your XAMPP or WAMP htdocs directory.

Database Setup:
Locate the latest database dump file inside the "assets/database scripts/"directory
Import the database dump into your local MySQL server using phpMyAdmin or the MySQL command line.
configure "rentalsv3/db_connection.php" accordingly

Access the Application:
Open your browser and navigate to:
http://localhost/rentalsv3/index.php

admin user:  superadmin
admin pass:  amtheone



Adding a module 
1. Modify the index file to create a page route /index.php
2. Modify the permissions array /index.php   eg  view_maientance =>['module' => 'Maintenance', 'action' => 'can_view']
3. Modify the sidebar at /assets/templates/sidebar.php
4. add the module in the module db table  : INSERT INTO module (module_name) VALUES ('modulename');

