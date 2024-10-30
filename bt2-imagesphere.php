<?php
/*
Plugin Name: bt2.Image Sphere
Plugin URI: https://mho.me
Description: Add 360 degree image-/panorama-/sphere-support to Wordpress. This might be a absolut simple and working Plugin ... and it is.
Version: 1.0.2
Author: mho.codus
Author URI: https://mho.me
Text Domain: bt2is-image-sphere
Domain Path: /lang/
License: http://creativecommons.org/licenses/by/3.0/
*/

if (!defined('BIT2_IMAGE_SPHERE_VERSION')) define('BIT2_IMAGE_SPHERE_VERSION', '1.0.2');

// de- & activation
function bt2is_activation() {
	update_option('bt2is_version', BIT2_IMAGE_SPHERE_VERSION);
	$default_settings = array('width' => '640px', 'height' => '480px','style' => '.spinner-layer {width: 40px;height: 40px;background-color: #e51c24;margin: 100px auto;-webkit-animation: sk-rotateplane 1.2s infinite ease-in-out;animation: sk-rotateplane 1.2s infinite ease-in-out;}@-webkit-keyframes sk-rotateplane {0% { -webkit-transform: perspective(120px) }50% { -webkit-transform: perspective(120px) rotateY(180deg) }100% { -webkit-transform: perspective(120px) rotateY(180deg)rotateX(180deg) }}@keyframes sk-rotateplane {0% {transform: perspective(120px) rotateX(0deg) rotateY(0deg);-webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg)} 50% {transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);-webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg)} 100% {transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);-webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);}}');
	$settings = get_option('bt2is_settings');
	if ($settings === false) $settings = array();
	update_option('bt2is_settings', array_merge($default_settings, $settings));
}
register_activation_hook(__FILE__, 'bt2is_activation');

function bt2is_check_version() {
	if (BIT2_IMAGE_SPHERE_VERSION !== get_option('bt2is_version')) bt2is_activation();
}
add_action('plugins_loaded', 'bt2is_check_version');

function bt2is_deactivation() {
	delete_option('bt2is_settings');
}
register_deactivation_hook(__FILE__, 'bt2is_deactivation');

function bt2is_lang() {
	load_plugin_textdomain('bt2is-image-sphere', false, dirname(plugin_basename(__FILE__)) . '/lang');
}
add_action('plugins_loaded', 'bt2is_lang');

function bt2is_register_scripts() {
	if ( !defined('WP_DEBUG') || WP_DEBUG != true ) :
		wp_register_script('bt2is-three', plugin_dir_url(__FILE__) . 'scripts/three.min.js', array(), '3.3', true);
		wp_register_script('bt2is-canvasrenderer', plugin_dir_url(__FILE__) . 'scripts/CanvasRenderer.js', array('bt2is-three'), '2.5', true);
		wp_register_script('bt2is-projector', plugin_dir_url(__FILE__) . 'scripts/Projector.js', array('bt2is-three'), '2.5', true);
		wp_register_script('bt2is-sphere', plugin_dir_url(__FILE__) . 'scripts/sphere.min.js', array('bt2is-three', 'bt2is-canvasrenderer','bt2is-projector'), '2.5', true);
	else :
		wp_register_script('bt2is-three', plugin_dir_url(__FILE__) . 'scripts/three.js', array(), '3.3', true);
		wp_register_script('bt2is-canvasrenderer', plugin_dir_url(__FILE__) . 'scripts/CanvasRenderer.js', array('bt2is-three'), '2.5', true);
		wp_register_script('bt2is-projector', plugin_dir_url(__FILE__) . 'scripts/Projector.js', array('bt2is-three'), '2.5', true);
		wp_register_script('bt2is-sphere', plugin_dir_url(__FILE__) . 'scripts/sphere.js', array('bt2is-three','bt2is-canvasrenderer','bt2is-projector'), '2.5', true);
	endif;
	wp_register_script('bt2is-image-sphere', plugin_dir_url(__FILE__) . 'imagesphere.js', array('jquery', 'bt2is-sphere'), BIT2_IMAGE_SPHERE_VERSION, true);
}
add_action('plugins_loaded', 'bt2is_register_scripts');

function bt2is_create_menu() {
	add_options_page('Image Sphere', '<span class="dashicons dashicons-format-gallery"></span> Image Sphere', 'manage_options', __FILE__, 'bt2is_options_page');
	add_action('admin_init', 'bt2is_register_settings');
}
add_action('admin_menu', 'bt2is_create_menu');

function bt2is_register_settings() {
	register_setting('bt2is_options', 'bt2is_settings');
}

function bt2is_options_page() {
	?>
	<div class="wrap">
		<h2><span class="dashicons dashicons-format-gallery"></span> Image Sphere</h2>

		<form method="post" action="options.php">
			<?php
			settings_fields('bt2is_options');
			$settings = get_option('bt2is_settings');
			?>
			<table class="form-table">
				<tr valign="top">
					<th><label for="bt2is_settings_style"><?php _e('Style of the container', 'bt2is-image-sphere'); ?></label></th>
					<td><textarea id="bt2is_settings_style" name="bt2is_settings[style]" cols="40" rows="5"><?php echo $settings['style']; ?></textarea></td>
				</tr>

				<tr valign="top">
					<th><label for="bt2is_settings_width"><?php _e('Default width', 'bt2is-image-sphere'); ?></label></th>
					<td><input type="text" id="bt2is_settings_width" name="bt2is_settings[width]" size="5" value="<?php echo $settings['width']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th><label for="bt2is_settings_height"><?php _e('Default height', 'bt2is-image-sphere'); ?></label></th>
					<td><input type="text" id="bt2is_settings_height" name="bt2is_settings[height]" size="5" value="<?php echo $settings['height']; ?>" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function bt2is_enqueue_admin_scripts() {
	if (floatval(get_bloginfo('version')) >= 3.5)
		wp_enqueue_script('bt2is-admin', plugin_dir_url(__FILE__) . 'imagesphere_admin.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_media', 'bt2is_enqueue_admin_scripts');

function bt2is_add_pano_button() {
	if (floatval(get_bloginfo('version')) >= 3.5) {
		?>
		<button type="button" id="insert-bt2is-button" class="button insert-media add_media" data-editor="content"><span class="dashicons dashicons-format-gallery"></span> Panoramabild</button>
		<?php
	}
}
add_action('media_buttons', 'bt2is_add_pano_button', 15);

// content
function bt2is_enqueue_footer_css() {
	$settings = get_option('bt2is_settings');
	echo sprintf("<style type='text/css' media='all'>%s</style>\n",preg_replace('/\s\s+/', '', $settings['style']));
}

function bt2is_handle_shortcode($atts) {
	wp_enqueue_script('bt2is-image-sphere');
	add_action('wp_footer', 'bt2is_enqueue_footer_css');
	$settings = get_option('bt2is_settings');
	$atts = shortcode_atts( array(
			'id' => '0',
			'height' => $settings['height'],
			'width' => $settings['width'],
			'url' => ''
	), $atts, 'bt2imagesphere' );

	if ($atts['id'] != 0) {
		$id = $atts['id'];
		$url = wp_get_attachment_url($id);
		$text = str_replace('%title%', get_the_title($id), $title);
	}

	else {
		$url = $atts['url'];
		$text = str_replace('%title%', '', $title);
	}

	$output = '<div class="spherecontainer" id="sphere-'. uniqid() . '" data-spheresource="' . $url . '" style="display: block; cursor: move; width: '. $atts['width'] .'; height: '. $atts['height'] .';"><canvas width="1870" height="720"></canvas></div>';
	return $output;
}
add_shortcode('bt2imagesphere', 'bt2is_handle_shortcode');


