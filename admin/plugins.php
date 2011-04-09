<?php
/**
 * Plugin management page
 *
 * @package Lilina
 * @subpackage Admin
 */

/** */
require_once('admin.php');
require_once(LILINA_PATH . '/admin/includes/settings.php');

if (isset($_GET['_nonce'])) {
	// Check the nonces. This could be written better.
	if (isset($_GET['activate'])) {
		$type = 'activate';
		$plugin = $_GET['activate'];
	}
	elseif (isset($_GET['deactivate'])) {
		$type = 'deactivate';
		$plugin = $_GET['deactivate'];
	}
	else {
		lilina_nice_die(_r('Incorrect action specified.'));
	}

	if (!check_nonce('plugins.' . $plugin, $_GET['_nonce'])) {
		lilina_nice_die(_r('Nonces do not match.'));
	}

	$names = lilina_plugins_list(get_plugin_dir());
	foreach ($names as $name) {
		$meta = plugins_meta($name);
		if ($meta->id === $plugin) {
			$file = str_replace(get_plugin_dir(), '', $name);
		}
	}

	if ($type === 'activate') {
		activate_plugin($file);
		
		header('HTTP/1.1 302 Found', true, 302);
		header('Location: ' . get_option('baseurl') . 'admin/plugins.php?activated=1');
		die();
	}
	else {
		deactivate_plugin($file);
		
		header('HTTP/1.1 302 Found', true, 302);
		header('Location: ' . get_option('baseurl') . 'admin/plugins.php?deactivated=1');
		die();
	}
}

if (isset($_GET['settings'])) {
	$succeeded = apply_filters('settings.plugins.' . $_GET['settings'] . '.settingspage', false);
	if (!$succeeded) {
		header('HTTP/1.1 500 Internal Server Error', true, 500);
		lilina_nice_die(_r('Plugin page not found'));
	}
	die();
}

admin_header(_r('Plugins'));

if (!empty($_GET['activated'])) {
	echo '<div class="message"><p>' . _r('Plugin <strong>activated</strong>.') . '</p></div>';
}

if(!empty($_GET['deactivated'])) {
	echo '<div class="message"><p>' . _r('Plugin <strong>deactivated</strong>.') . '</p></div>';
}

?>

<h1><?php _e('Plugin Management'); ?></h1>

<p><a href="plugins-add.php"><?php _e('Install a plugin') ?></a> | <a href="plugins-add.php?action=update"><?php _e('Check for updates') ?></a></p>

<form action="settings.php" method="post">
	<fieldset id="plugins">
		<table class="item-table">
			<thead>
				<tr>
					<th scope="col"><?php _e('Plugin') ?></th>
					<th scope="col"><?php _e('Description') ?></th>
				</tr>
			</thead>
			<tbody>
<?php
foreach (lilina_plugins_list(get_plugin_dir()) as $plugin):
	global $current_plugins;
	$meta = plugins_meta($plugin);
	$plugin_file = str_replace(get_plugin_dir(), '', $plugin);

	$activated = isset($current_plugins[md5($plugin_file)]);

	$class = 'plugin-row';
	$nonce = generate_nonce('plugins.' . $meta->id);
	if ($activated) {
		$class .= ' activated';
		$actions = '<a href="plugins.php?deactivate=' . $meta->id . '&amp;_nonce=' . $nonce . '">' . _r('Deactivate') . '</a>';
	}
	else {
		$class .= ' deactivated';
		$actions = '<a href="plugins.php?activate=' . $meta->id . '&amp;_nonce=' . $nonce . '">' . _r('Activate') . '</a>';
	}

	$link = apply_filters('settings.plugins.' . $meta->id . '.settingslink', 'plugins.php?settings=' . $meta->id);

	if (!empty($settings)) {
		$actions .= sprintf(' | <a href="%s">%s</a>', $link, _r('Settings'));
	}

	$info = array();
	$info[] = sprintf(_r('Version %s'), $meta->version);

	if (!empty($meta->author)) {
		if ($meta->author_uri) {
			$info[] = apply_filters('settings.plugins.' . $meta->id . '.author', sprintf(
				_r('By %s</a>'),
				'<a href="' . $meta->author_uri . '">' . $meta->author . '</a>'
			), $meta->author, $meta->author_uri);
		}
		else {
			$info[] = apply_filters('settings.plugins.' . $meta->id . '.author', sprintf(_r('By %s'), $meta->author), $meta_author);
		}
	}

	if (!empty($meta->uri)) {
		$info[] = apply_filters('settings.plugins.' . $meta->id . '.link', sprintf(_r('<a href="%s">Visit plugin site</a>'), $meta->uri));
	}

	$info = apply_filters('settings.plugins.' . $meta->id . '.info', $info);
?>
				<tr class="<?php echo $class ?>">
					<td class="plugin-name"><span class="name"><?php echo $meta->name ?></span><p class="plugin-actions"><?php echo $actions ?></p></td>
					<td class="plugin-desc"><?php echo $meta->description ?><p><?php echo implode(' | ', $info) ?></p></td>
				</tr>
<?php
endforeach;
?>
			</tbody>
		</table>
	</fieldset>
</form>
<?php
admin_footer();
?>