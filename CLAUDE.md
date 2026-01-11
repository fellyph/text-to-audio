# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Text to Audio is a WordPress plugin that converts post content to audio files using Google Gemini TTS via the `wordpress/php-ai-client` library.

## Development Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

## Architecture

### Plugin Structure

- `text-to-audio.php` - Main plugin entry point (currently features are commented out)
- `src/` - PSR-4 autoloaded classes under `Fellyph\TextToAudio` namespace:
  - `Settings.php` - Admin settings page for API key and TTS configuration (voice, speed, pitch)
  - `TTSProvider.php` - Gemini TTS integration using `wordpress/php-ai-client`
  - `Media.php` - Saves generated audio to WordPress Media Library
  - `UI.php` - Post editor meta box with AJAX-powered conversion button
  - `Shortcode.php` - `[post_audio]` shortcode for frontend audio player

### Data Flow

1. User clicks "Convert to Audio" in post editor meta box
2. AJAX request triggers `UI::ajax_convert_to_audio()`
3. `TTSProvider::convert()` sends post content to Gemini TTS API
4. `Media::save_audio()` stores resulting MP3 in WordPress uploads
5. Attachment ID saved to post meta (`_fellyph_audio_id`)

### Key Integration: php-ai-client

The plugin uses `wordpress/php-ai-client` for Gemini API calls. Key pattern:

```php
$registry = AiClient::defaultRegistry();
$google_provider = $registry->getProvider('google');
$google_provider->setRequestAuthentication(new ApiKeyRequestAuthentication($api_key));

$config = new ModelConfig();
$config->setOutputModalities([ModalityEnum::audio()]);

$result = AiClient::prompt($text)
    ->usingProvider('google')
    ->usingModel('gemini-1.5-flash')
    ->usingModelConfig($config)
    ->generateResult();
```

## WordPress Playground

The `blueprint.json` configures WordPress Playground with the plugin activated and WP_DEBUG enabled.

## Compatibility

- WordPress: 6.9+
- PHP: 7.2.24+
