<?php

namespace WsInpostMapOnCheckout\App;

if (!defined('ABSPATH')) exit;

class WSInpostSettings
{
	private $capability = 'manage_options';
	private const WSINPOST_SETTINGS_KEY = "ws_inpost_plugin_options";
	private $generalFields;
	private $fields;

	public function __construct()
	{
		$this->generalFields = [
			[
				'id' => 'active-button',
				'label' => __('Show button on checkout', "ws-inpost-map"),
				'description' => '',
				'type' => 'checkbox',
			]
		];

		$this->fields = [
			[
				'id' => 'button-font-size',
				'label' => __('Button font size', "ws-inpost-map"),
				'description' => '',
				'type' => 'number',
				'default' => "12",
			],
			[
				'id' => 'button-font-weight',
				'label' => __('Button font weight', "ws-inpost-map"),
				'description' => '',
				'type' => 'select',
				'options' => [
					"100" => "Thin",
					"200" => "Extra Light",
					"300" => "Light",
					"400" => "Normal",
					"500" => "Medium",
					"600" => "Semi Bold",
					"700" => "Bold",
					"800" => "Extra Bold ",
					"900 " => "Heavy",
				],
				'default' => "400",
			],
			[
				'id' => 'button-text-color',
				'label' => __('Button text color', "ws-inpost-map"),
				'description' => '',
				'type' => 'color',
				'default' => "#ffffff",
			],
			[
				'id' => 'button-background-color',
				'label' => __('Button background color', "ws-inpost-map"),
				'description' => '',
				'type' => 'color',
				'default' => "#000000",
			],
			[
				'id' => 'button-padding',
				'label' => __('Button padding', "ws-inpost-map"),
				'description' => '',
				'type' => 'number',
				'default' => "10",
			],
			[
				'id' => 'button-border',
				'label' => __('Border on button', "ws-inpost-map"),
				'description' => '',
				'type' => 'checkbox',
			],
			[
				'id' => 'button-border-color',
				'label' => __('Button border color', "ws-inpost-map"),
				'description' => '',
				'type' => 'color',
				'default' => "#000000",
			],
			[
				'id' => 'button-border-size',
				'label' => __('Button border size', "ws-inpost-map"),
				'description' => '',
				'type' => 'number',
				'default' => "2",
			],
			[
				'id' => 'button-border-radius',
				'label' => __('Button border radius', "ws-inpost-map"),
				'description' => '',
				'type' => 'number',
				'default' => "10",
			],
			[
				'id' => 'button-opacity-value',
				'label' => __('Button opacity on hover value (value / 100)', "ws-inpost-map"),
				'description' => '',
				'type' => 'number',
				'default' => "100",
			],
			[
				'id' => 'button-text-color-hover',
				'label' => __('Button text color on hover', "ws-inpost-map"),
				'description' => '',
				'type' => 'color',
				'default' => "#000000",
			],
			[
				'id' => 'button-background-color-hover',
				'label' => __('Button background color on hover', "ws-inpost-map"),
				'description' => '',
				'type' => 'color',
				'default' => "#ffffff",
			]
		];

		add_action('admin_init', [$this, 'settings_init']);
		add_action('admin_menu', [$this, 'options_page']);
		add_action('woocommerce_after_checkout_form', [$this, 'addCustomStyles']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
	}

	public function saveDefaultData()
	{
		$fieldsToSaveInWpOptions = [];
		foreach ($this->fields as $field) {
			if (isset($field['default'])) {
				$fieldsToSaveInWpOptions[$field['id']] = $field['default'];
			}
		}
		if (false === get_option(self::WSINPOST_SETTINGS_KEY)) {
			update_option(self::WSINPOST_SETTINGS_KEY, $fieldsToSaveInWpOptions);
		}
	}

	public function admin_enqueue_scripts($screen)
	{
		if ('toplevel_page_ws-inpost-settings' !== $screen) return;

		wp_enqueue_script('ws-inpost-settings-js', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/js/admin/sections.js', array(), false, true);
		wp_enqueue_style('ws-inpost-settings-style', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/css/admin/sections.css');
	}

	/**
	 * Register the settings and all fields.
	 *
	 * @return void
	 */
	public function settings_init(): void
	{
		register_setting('ws-inpost-settings', self::WSINPOST_SETTINGS_KEY);

		add_settings_section(
			'ws-inpost-general-section',
			__('General', 'ws-inpost-map'),
			[$this, 'render_section'],
			'ws-inpost-settings'
		);
		add_settings_section(
			'ws-inpost-button-section',
			__('Button', 'ws-inpost-map'),
			[$this, 'render_section'],
			'ws-inpost-settings'
		);
		add_settings_section(
			'ws-licence-section',
			__('Pro Version', 'ws-inpost-map'),
			[$this, 'renderLicenceSection'],
			'ws-inpost-settings'
		);

		$this->addSettingFields($this->generalFields, [$this, 'render_field'], 'ws-inpost-settings', 'ws-inpost-general-section');
		$this->addSettingFields($this->fields, [$this, 'render_field'], 'ws-inpost-settings', 'ws-inpost-button-section');
	}

	public function addSettingFields($fields, $callback, $manuPage, $section)
	{
		foreach ($fields as $field) {
			add_settings_field(
				$field['id'],
				$field['label'],
				$callback,
				$manuPage,
				$section,
				[
					'label_for' => $field['id'],
					'class' => 'wporg_row',
					'field' => $field,
				]
			);
		}
	}

	/**
	 * Add a subpage to the WordPress Settings menu.
	 */
	public function options_page(): void
	{
		add_menu_page(
			'Settings',
			__('WS Inpost Settings', "ws-inpost-map"),
			$this->capability,
			'ws-inpost-settings',
			[$this, 'render_options_page'],
			'dashicons-location',
			'80',
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_options_page(): void
	{

		if (!current_user_can($this->capability)) {
			return;
		}

		if (isset($_GET['settings-updated'])) {
			add_settings_error('ws_messages', 'ws_message', __('Settings Saved', 'ws-inpost-map'), 'updated');
		}

		global $wp_settings_sections;
		if (isset($_GET['page']) && sanitize_text_field($_GET['page']) === "ws-inpost-settings") {
			$page = sanitize_text_field($_GET['page']);
			$sections = $wp_settings_sections[$page];
		} else {
			return;
		}
?>
		<div id="settings-container" class="wrap">
			<div class="messages-box"><?php settings_errors('ws_messages'); ?></div>
			<div class="information-container">
				<div class="first-row">
					<a href="<?php echo esc_html("https://k4.pl/en/") ?>"><img width="100px" height="100px" src="<?php echo esc_attr(WSIM_INPOST_MAP_PLUGIN_DIR_URL) . "assets/src/img/K4-logo.png"; ?>"></img></a>
					<p><?php echo esc_html__('WS Inpost Map', 'ws-inpost-map'); ?></p>
				</div>
				<div class="description">
					<p><?php echo esc_html__('Plugin designed to enhance the shipping options in WooCommerce. It provides an additional shipping method and enables customers to conveniently select a parcel locker (Paczkomat) using an integrated map feature.', 'ws-inpost-map'); ?></p>
				</div>
			</div>
			<div class="settings-tabs">
				<?php
				foreach ($sections as $section) {
				?>
					<a href="<?php echo "#" . esc_attr($section["id"]); ?>"><?php echo esc_html($section["title"]); ?></a>
				<?php
				}
				?>
			</div>
			<form action="options.php" method="post">
				<?php
				settings_fields('ws-inpost-settings');
				?>
				<div id="ws-inpost-general-section" class="active"><?php do_settings_fields('ws-inpost-settings',  'ws-inpost-general-section'); ?></div>
				<div id="ws-inpost-button-section"><?php do_settings_fields('ws-inpost-settings',  'ws-inpost-button-section'); ?></div>
				<div id="ws-licence-section"><?php echo esc_html($this->renderLicenceSection()); ?></div>
				<?php
				submit_button(__('Save Settings', 'ws-inpost-map'));
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Args to configure the field.
	 */
	public function render_field(array $args): void
	{
		$field = $args['field'];
		$options = get_option(self::WSINPOST_SETTINGS_KEY);
		switch ($field['type']) {
			case "text": {
		?>
					<input type="text" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "checkbox": {
				?>
					<input type="checkbox" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="1" <?php echo isset($options[$field['id']]) ? (checked($options[$field['id']], 1, false)) : (''); ?>>
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "textarea": {
				?>
					<textarea id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]"><?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?></textarea>
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "select": {
				?>
					<select id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]">
						<?php foreach ($field['options'] as $key => $option) { ?>
							<option value="<?php echo esc_html($key); ?>" <?php echo isset($options[$field['id']]) ? (selected($options[$field['id']], $key, false)) : (''); ?>>
								<?php echo esc_html($option); ?>
							</option>
						<?php } ?>
					</select>
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "password": {
				?>
					<input type="password" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "wysiwyg": {
					wp_editor(
						isset($options[$field['id']]) ? $options[$field['id']] : '',
						$field['id'],
						array(
							'textarea_name' => self::WSINPOST_SETTINGS_KEY[$field["id"]],
							'textarea_rows' => 5,
						)
					);
					break;
				}
			case "email": {
				?>
					<input type="email" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "url": {
				?>
					<input type="url" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "color": {
				?>
					<input type="color" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "date": {
				?>
					<input type="date" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
				<?php
					break;
				}
			case "number": {
				?>
					<input type="number" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr(self::WSINPOST_SETTINGS_KEY); ?>[<?php echo esc_attr($field['id']); ?>]" value="<?php echo isset($options[$field['id']]) ? esc_attr($options[$field['id']]) : ''; ?>" min="0" max="100">
					<p class="description">
						<?php echo esc_html($field['description']); ?>
					</p>
		<?php
					break;
				}
		}
	}

	/**
	 * Render a section on a page, with an ID and a text label.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     An array of parameters for the section.
	 *
	 *     @type string $id The ID of the section.
	 * }
	 */
	public function render_section(array $args): void
	{
		?>
		<div class="<?php echo esc_attr($args['id']); ?>">
			<p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('', 'ws-inpost-map'); ?></p>
		</div>
	<?php
	}

	public function renderLicenceSection()
	{
	?>
		<h2><?php echo esc_html__("Pro version features", "ws-inpost-map"); ?></h2>
		<ul class="pro-version-fatures-list">
			<li class="feature"><?php echo esc_html__("More customisation options", "ws-inpost-map"); ?></li>
			<li class="feature"><?php echo esc_html__("Advanced support", "ws-inpost-map"); ?></li>
			<li class="feature"><?php echo esc_html__("Ability to change field and map position on checkout", "ws-inpost-map"); ?></li>
		</ul>
		<p class="premium-version"><?php echo esc_html__("To get premium version visit this link:", "ws-inpost-map") . " "; ?><a href="https://k4.pl/en/shop"><?php echo esc_html__("K4-shop", "ws-inpost-map"); ?></a></p>
<?php
	}

	public function addCustomStyles()
	{
		$options = get_option(self::WSINPOST_SETTINGS_KEY);
		$buttonBackgroundColor = $options['button-background-color'];
		$buttonFontSize = $options['button-font-size'];
		$buttonFontWeight = $options['button-font-weight'];
		$buttonTextColor = $options['button-text-color'];
		$buttonBorderColor = $options['button-border-color'];
		$buttonBorderSize = $options['button-border-size'];
		$buttonPadding = $options['button-padding'];
		$buttonBorderRadius = $options['button-border-radius'];
		$buttonOpacityValue = floatval($options['button-opacity-value'] / 100);
		$buttonTextColoOnHover = $options['button-text-color-hover'];
		$buttonOpacityBackgroundColor = $options['button-background-color-hover'];
		$active = "";
		if (!isset($options['active-button'])) {
			$active = "#billing__paczkomat_id_field{display:none !important}; .select-paczkomat-button{display:none !important};";
		}
		$border = "border:none !important";
		if (isset($options['button-border'])) {
			if ($buttonBorderSize) {
				$border = "border:solid " . $buttonBorderSize . "px";
			} else {
				$border = "border:solid 1px";
			}
		}
		$custom_css = "{$active}
		.select-paczkomat-button{
		font-size:{$buttonFontSize}px !important;
		font-weight: {$buttonFontWeight} !important;
		background-color: {$buttonBackgroundColor} !important;
		color: {$buttonTextColor} !important;
		{$border} !important;
		border-color: {$buttonBorderColor} !important;
		padding:{$buttonPadding}px !important;
		border-radius:{$buttonBorderRadius}px !important;
		}
		.select-paczkomat-button:hover{
		color:{$buttonTextColoOnHover} !important;
		opacity: {$buttonOpacityValue} !important;
		background-color: {$buttonOpacityBackgroundColor} !important;
		}";

		echo "<style>" . esc_html($custom_css) . "</style>";
	}
}
