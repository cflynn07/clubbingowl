#!/bin/sh
php /home/dotcloud/current/post_deploy_setup.php
rm -f /home/dotcloud/current/post_deploy_setup.php

php /home/dotcloud/current/index.php assets css base
php /home/dotcloud/current/index.php assets css admin_base
php /home/dotcloud/current/index.php assets css facebook_app_base


php /home/dotcloud/current/index.php assets js base
php /home/dotcloud/current/index.php assets js facebook_sdk
php /home/dotcloud/current/index.php assets js facebook_sdk_facebook
php /home/dotcloud/current/index.php assets js facebook_sdk_admin
php /home/dotcloud/current/index.php assets js facebook_app_base
php /home/dotcloud/current/index.php assets js admin_base
php /home/dotcloud/current/index.php assets js admin_base hosts
php /home/dotcloud/current/index.php assets js admin_base promoters
php /home/dotcloud/current/index.php assets js admin_base managers
php /home/dotcloud/current/index.php assets js admin_base super_admins
php /home/dotcloud/current/index.php assets js ejs_templates en
php /home/dotcloud/current/index.php assets js ejs_templates ja 
php /home/dotcloud/current/index.php assets js ejs_templates de



php /home/dotcloud/current/index.php assets js ejs_templates_admin_promoters en
php /home/dotcloud/current/index.php assets js ejs_templates_admin_managers en
php /home/dotcloud/current/index.php assets js ejs_templates_admin_hosts en

