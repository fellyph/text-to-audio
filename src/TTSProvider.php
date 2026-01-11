<?php

namespace Fellyph\TextToAudio;

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Messages\DTO\UserMessage;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\ProviderImplementations\Google\GoogleProvider;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;

/**
 * Handles the connection to Gemini TTS.
 */
class TTSProvider {
	/**
	 * Convert text to audio binary using php-ai-client.
	 *
	 * @param string $text The text to convert.
	 * @return string|\WP_Error Binary audio data or WP_Error.
	 */
	public function convert( $text ) {
		if ( ! Settings::is_api_key_configured() ) {
			return new \WP_Error(
				'missing_api_key',
				wp_strip_all_tags( Settings::get_missing_api_key_message() )
			);
		}

		// Strip shortcodes and HTML for better TTS
		$text = wp_strip_all_tags( strip_shortcodes( $text ) );

		try {
			$settings = Settings::get_settings();
			$api_key  = $settings['gemini_api_key'];
			$voice    = $settings['tts_voice'];
			$speed    = (float) $settings['tts_speed'];
			$pitch    = (float) $settings['tts_pitch'];

			// Initialize the registry with the API Key
			$registry = AiClient::defaultRegistry();
			$google_provider = $registry->getProvider( 'google' );
			$google_provider->setRequestAuthentication( new ApiKeyRequestAuthentication( $api_key ) ) ;

			$config = new ModelConfig();
			$config->setOutputModalities( [ ModalityEnum::audio() ] );
			$config->setOutputSpeechVoice( $voice );
			
			// For Gemini models, we can also pass these via customOptions if the provider supports them
			// or use instructions. Given the current library state, we'll set them as custom options.
			$config->setCustomOption( 'speech_config', [
				'voice_config' => [
					'voice_name' => $voice,
				],
				'speaking_rate' => $speed,
				'pitch'         => $pitch,
			] );

			// We use a model that supports multi-modal output (like gemini-1.5-flash or flash-2.0)
			$result = AiClient::prompt( $text )
				->usingProvider( 'google' )
				->usingModel( 'gemini-1.5-flash' )
				->usingModelConfig( $config )
				->generateResult();

			$audio_file = $result->toAudioFile();
			return $audio_file->getContents();

		} catch ( \WordPress\AiClient\Providers\Http\Exception\ResponseException $e ) {
			return new \WP_Error( 'tts_api_error', sprintf( __( 'API Error: %s', 'text-to-audio' ), $e->getMessage() ) );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'tts_error', sprintf( __( 'Unexpected Error: %s', 'text-to-audio' ), $e->getMessage() ) );
		}
	}
}
