# AI Instruction: php-ai-client & Gemini TTS in WordPress

This guide helps AI agents integrate Gemini Text-to-Speech (TTS) using the `wordpress/php-ai-client` library in a WordPress environment.

## Overview

The `php-ai-client` is a provider-agnostic SDK. While it has built-in support for text and image generation, TTS support in the Google provider may require manual configuration or specific model selection.

## Setup

1. **Install Dependencies**:

   ```bash
   composer require wordpress/php-ai-client
   ```

2. **Initialize Client**:
   The client requires an API key and a registry of providers.

   ```php
   use WordPress\AiClient\AiClient;
   use WordPress\AiClient\ProviderImplementations\Google\GoogleProvider;

   $registry = AiClient::defaultRegistry();
   // The registry automatically registers GoogleProvider if available.
   ```

## Gemini TTS Usage

### Prerequisites

- Google Cloud Project with Text-to-Speech API enabled.
- API Key with appropriate permissions.

### Example: Basic TTS Conversion

> [!IMPORTANT]
> Ensure the Gemini model used supports `audio` output modality.

```php
use WordPress\AiClient\AiClient;

$result = AiClient::prompt('Hello, this is a test of Gemini TTS.')
    ->asOutputModalities(ModalityEnum::audio()) // Request audio output
    ->generateResult();

$audioFile = $result->toAudioFile();
$binaryData = $audioFile->getContents();
file_put_contents('output.mp3', $binaryData);
```

### Integration Tips

- **Media Library**: When saving to WordPress, use `wp_handle_sideload()` or `wp_insert_attachment()` with the binary data.
- **Settings**: Always provide a UI for users to input their own Gemini API Key.
- **Provider Agnostic**: Use `AiClient::prompt()` instead of provider-specific classes where possible to maintain flexibility.

## Reference Documentation

- [PHP AI Client GitHub](https://github.com/WordPress/php-ai-client)
- [Gemini TTS Documentation](https://cloud.google.com/text-to-speech/docs/gemini-tts)
