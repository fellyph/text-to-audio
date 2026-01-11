<?php
/**
 * Shortcode.php
 */

namespace Fellyph\TextToAudio;

/**
 * Handles the [post_audio] shortcode.
 */
class Shortcode {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'post_audio', [ $this, 'render_shortcode' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_assets() {
		if ( ! is_singular() ) {
			return;
		}

		wp_enqueue_style(
			'fellyph-text-to-audio-frontend',
			plugin_dir_url( __FILE__ ) . '../assets/css/frontend.css',
			[],
			'1.0.0'
		);
	}

	/**
	 * Render the shortcode.
	 */
	public function render_shortcode( $atts ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		$audio_id = get_post_meta( $post_id, '_fellyph_audio_id', true );
		if ( ! $audio_id ) {
			return '';
		}

		$audio_url = wp_get_attachment_url( $audio_id );
		if ( ! $audio_url ) {
			return '';
		}

		ob_start();
		?>
		<div class="fellyph-post-audio-player">
			<audio controls src="<?php echo esc_url( $audio_url ); ?>" style="width: 100%;"></audio>
		</div>
		<?php
		return ob_get_clean();
	}
}
