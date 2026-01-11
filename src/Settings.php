<?php

namespace Fellyph\TextToAudio;

/**
 * Handles plugin settings and API configuration.
 */
class Settings {
	/**
	 * Option name for plugin settings.
	 */
	const OPTION_NAME = 'fellyph_text_to_audio_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Add settings page to the dashboard.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Text to Audio Settings', 'text-to-audio' ),
			__( 'Text to Audio', 'text-to-audio' ),
			'manage_options',
			'text-to-audio',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'text-to-audio-group',
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_settings' ],
			]
		);

		add_settings_section(
			'fellyph_text_to_audio_main_section',
			__( 'Gemini API Configuration', 'text-to-audio' ),
			null,
			'text-to-audio'
		);

		add_settings_field(
			'gemini_api_key',
			__( 'Gemini API Key', 'text-to-audio' ),
			[ $this, 'render_api_key_field' ],
			'text-to-audio',
			'fellyph_text_to_audio_main_section'
		);

		add_settings_field(
			'tts_voice',
			__( 'Voice', 'text-to-audio' ),
			[ $this, 'render_voice_field' ],
			'text-to-audio',
			'fellyph_text_to_audio_main_section'
		);

		add_settings_field(
			'tts_speed',
			__( 'Speed', 'text-to-audio' ),
			[ $this, 'render_speed_field' ],
			'text-to-audio',
			'fellyph_text_to_audio_main_section'
		);

		add_settings_field(
			'tts_pitch',
			__( 'Pitch', 'text-to-audio' ),
			[ $this, 'render_pitch_field' ],
			'text-to-audio',
			'fellyph_text_to_audio_main_section'
		);
	}

	/**
	 * Render the API Key field.
	 */
	public function render_api_key_field() {
		$options = get_option( self::OPTION_NAME );
		$api_key = isset( $options['gemini_api_key'] ) ? $options['gemini_api_key'] : '';
		?>
		<input type="password" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[gemini_api_key]" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Enter your Google Gemini API Key.', 'text-to-audio' ); ?></p>
		<?php
	}

	/**
	 * Render the Voice selection field.
	 */
	public function render_voice_field() {
		$options = get_option( self::OPTION_NAME );
		$voice   = isset( $options['tts_voice'] ) ? $options['tts_voice'] : 'Puck';
		$voices  = [ 'Aoede', 'Charon', 'Fenrir', 'Kore', 'Puck' ];
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[tts_voice]">
			<?php foreach ( $voices as $v ) : ?>
				<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $voice, $v ); ?>><?php echo esc_html( $v ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'Select the voice for the audio generation.', 'text-to-audio' ); ?></p>
		<?php
	}

	/**
	 * Render the Speed field.
	 */
	public function render_speed_field() {
		$options = get_option( self::OPTION_NAME );
		$speed   = isset( $options['tts_speed'] ) ? $options['tts_speed'] : '1.0';
		?>
		<input type="number" step="0.1" min="0.5" max="2.0" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[tts_speed]" value="<?php echo esc_attr( $speed ); ?>" class="small-text">
		<p class="description"><?php esc_html_e( 'Set the speaking speed (0.5 to 2.0).', 'text-to-audio' ); ?></p>
		<?php
	}

	/**
	 * Render the Pitch field.
	 */
	public function render_pitch_field() {
		$options = get_option( self::OPTION_NAME );
		$pitch   = isset( $options['tts_pitch'] ) ? $options['tts_pitch'] : '0.0';
		?>
		<input type="number" step="0.1" min="-20.0" max="20.0" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[tts_pitch]" value="<?php echo esc_attr( $pitch ); ?>" class="small-text">
		<p class="description"><?php esc_html_e( 'Adjust the pitch (-20.0 to 20.0).', 'text-to-audio' ); ?></p>
		<?php
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Text to Audio Settings', 'text-to-audio' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'text-to-audio-group' );
				do_settings_sections( 'text-to-audio' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get the plugin settings.
	 */
	public static function get_settings() {
		$defaults = [
			'gemini_api_key' => '',
			'tts_voice'      => 'Puck',
			'tts_speed'      => '1.0',
			'tts_pitch'      => '0.0',
		];
		$options = get_option( self::OPTION_NAME );
		return wp_parse_args( $options, $defaults );
	}

	/**
	 * Get the stored API key.
	 */
	public static function get_api_key() {
		$settings = self::get_settings();
		return $settings['gemini_api_key'];
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @param array $input The input settings.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = [];

		// Sanitize API Key - trim whitespace and ensure it's a string.
		if ( isset( $input['gemini_api_key'] ) ) {
			$sanitized['gemini_api_key'] = sanitize_text_field( trim( $input['gemini_api_key'] ) );
		}

		// Sanitize voice selection.
		$valid_voices = [ 'Aoede', 'Charon', 'Fenrir', 'Kore', 'Puck' ];
		if ( isset( $input['tts_voice'] ) && in_array( $input['tts_voice'], $valid_voices, true ) ) {
			$sanitized['tts_voice'] = $input['tts_voice'];
		} else {
			$sanitized['tts_voice'] = 'Puck';
		}

		// Sanitize speed (0.5 to 2.0).
		if ( isset( $input['tts_speed'] ) ) {
			$speed = floatval( $input['tts_speed'] );
			$sanitized['tts_speed'] = max( 0.5, min( 2.0, $speed ) );
		} else {
			$sanitized['tts_speed'] = 1.0;
		}

		// Sanitize pitch (-20.0 to 20.0).
		if ( isset( $input['tts_pitch'] ) ) {
			$pitch = floatval( $input['tts_pitch'] );
			$sanitized['tts_pitch'] = max( -20.0, min( 20.0, $pitch ) );
		} else {
			$sanitized['tts_pitch'] = 0.0;
		}

		return $sanitized;
	}

	/**
	 * Check if the API key is configured.
	 *
	 * @return bool True if API key is set, false otherwise.
	 */
	public static function is_api_key_configured() {
		$api_key = self::get_api_key();
		return ! empty( $api_key );
	}

	/**
	 * Get a user-friendly error message for missing API key.
	 *
	 * @return string Error message with link to settings page.
	 */
	public static function get_missing_api_key_message() {
		$settings_url = admin_url( 'options-general.php?page=text-to-audio' );
		return sprintf(
			/* translators: %s: URL to settings page */
			__( 'Gemini API Key is not configured. Please <a href="%s">configure your API Key</a> in the plugin settings to enable audio generation.', 'text-to-audio' ),
			esc_url( $settings_url )
		);
	}
}
