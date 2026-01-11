<?php

namespace Fellyph\TextToAudio;

/**
 * Handles the management of UI elements in the post editor.
 */
class UI {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_audio_conversion_meta_box' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'wp_ajax_convert_post_to_audio', [ $this, 'ajax_convert_to_audio' ] );
	}

	/**
	 * Add meta box to the post editor.
	 */
	public function add_audio_conversion_meta_box() {
		add_meta_box(
			'fellyph_text_to_audio_metabox',
			__( 'Text to Audio', 'text-to-audio' ),
			[ $this, 'render_meta_box' ],
			'post',
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box content.
	 */
	public function render_meta_box( $post ) {
		$audio_id = get_post_meta( $post->ID, '_fellyph_audio_id', true );
		$audio_url = $audio_id ? wp_get_attachment_url( $audio_id ) : '';
		?>
		<div id="fellyph-tts-ui">
			<?php if ( $audio_url ) : ?>
				<audio controls src="<?php echo esc_url( $audio_url ); ?>" style="width: 100%; margin-bottom: 10px;"></audio>
			<?php endif; ?>
			
			<button type="button" id="fellyph-convert-btn" class="button button-primary" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
				<?php echo $audio_url ? esc_html__( 'Regenerate Audio', 'text-to-audio' ) : esc_html__( 'Convert to Audio', 'text-to-audio' ); ?>
			</button>
			
			<div id="fellyph-tts-status" style="margin-top: 10px; font-style: italic; display: none;">
				<?php esc_html_e( 'Processing...', 'text-to-audio' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin scripts for AJAX.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'fellyph-text-to-audio-js',
			plugin_dir_url( __FILE__ ) . '../assets/js/admin.js',
			[ 'jquery' ],
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'fellyph-text-to-audio-css',
			plugin_dir_url( __FILE__ ) . '../assets/css/admin.css',
			[],
			'1.0.0'
		);

		wp_localize_script( 'fellyph-text-to-audio-js', 'fellyphTTS', [
			'nonce' => wp_create_nonce( 'fellyph-tts-nonce' ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		] );
	}

	/**
	 * AJAX handler for post conversion.
	 */
	public function ajax_convert_to_audio() {
		check_ajax_referer( 'fellyph-tts-nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Invalid post ID.', 'text-to-audio' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			wp_send_json_error( __( 'Post not found.', 'text-to-audio' ) );
		}

		// Perform conversion
		$tts_provider = new TTSProvider();
		$audio_data = $tts_provider->convert( $post->post_content );

		if ( is_wp_error( $audio_data ) ) {
			wp_send_json_error( $audio_data->get_error_message() );
		}

		// Save to media library
		$media_handler = new Media();
		$attachment_id = $media_handler->save_audio( $post_id, $audio_data );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( $attachment_id->get_error_message() );
		}

		update_post_meta( $post_id, '_fellyph_audio_id', $attachment_id );

		wp_send_json_success( [
			'audio_url' => wp_get_attachment_url( $attachment_id ),
		] );
	}
}
