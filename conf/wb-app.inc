<?php

	// define constants
	define("G_APP_NAME", "website builder");
    define("G_APP_TAGLINE", "websites for everyone!");
    define("G_CODE_ROOT", "/Users/rjha/code/github");

	define("APP_LIB_PATH", 
	            G_CODE_ROOT."/webgloo/lib/ext/sendgrid-php,". 
	            G_CODE_ROOT."/webgloo/lib/ext,". 
	            G_CODE_ROOT."/webgloo/lib,". 
	            G_CODE_ROOT."/website/lib") ;

	define("APP_CLASS_LOADER", G_CODE_ROOT."/webgloo/lib/com/indigloo/class_loader.inc") ;

	define("APP_CONFIG_PATH", "/var/www/apps/wb_config.ini");
	define("APP_WEB_DIR", "/var/www/htdocs") ;
	define("WEBGLOO_LIB_ROOT", G_CODE_ROOT."/webgloo/lib");
?>
