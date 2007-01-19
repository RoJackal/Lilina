<?php
/******************************************
		Lilina: Simple PHP Aggregator
File:		admin.php
Purpose:	Administration page
Notes:		Need to move all crud to plugins
Style:		**EACH TAB IS 4 SPACES**
Licensed under the GNU General Public License
See LICENSE.txt to view the license
******************************************/
//Stop hacking attempts
defined('LILINA') or die('Restricted access');

function lilina_check_installed() {
	if(@file_exists('./conf/settings.php')) {
		return true;
	}
	return false;
}

function lilina_install_err($error = 0, $args = '') {
	switch($error) {
		//0: Page not found
		case 0:
			echo 'I couldn\'t find the page you asked for. Report the following to the <a href="http://lilina.cubegames.net/wordpress/vanilla/">Lilina forums</a>:
			<pre>Err 0; Page ' . $args . '</pre>';
			break;
		//1: settings.php found, must already be installed
		case 1:
			echo 'Lilina is already installed on this server. <a href="index.php">Return to Lilina</a>.';
			break;
		//2: Failed opening file
		case 2:
			echo 'I couldn\'t open ' . $args . ' to write to. Please make sure that the conf directory is writable and that the server can write to it.';
			break;
		default:
			break;
	}
}

function lilina_install_page($page) {
	switch($page) {
		case 0:
			require_once('./inc/pages/install-not.php');
			break;
		case 1:
			require_once('./inc/pages/install-start.php');
			break;
		case 2:
		default:
			lilina_install_err(0, $page);
			break;
	}
}

function lilina_set_settings($args) {
	$settings_file = @fopen('./conf/settings.php', 'w+');
	if(!$settings_file) {
		lilina_install_err(2, 'settings.php');
		return;
	}
	
}
?>