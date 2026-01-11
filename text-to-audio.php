<?php
/**
 * Plugin Name: Text to Audio with Gemini TTS
 * Description: Convert WordPress posts into audio files using Google Gemini TTS.
 * Version: 1.0.0
 * Author: Fellyph Cintra
 * License: GPL-2.0-or-later
 * Text Domain: text-to-audio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Fellyph\TextToAudio\Settings;
use Fellyph\TextToAudio\UI;

/**
 * Main plugin class.
 */
class TextToAudioMain {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin features.
	 */
	public function init() {
		new Settings();
		new UI();
		new \Fellyph\TextToAudio\Shortcode();
	}
}

new TextToAudioMain();
