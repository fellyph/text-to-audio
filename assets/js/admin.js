jQuery(document).ready(function ($) {
  const $btn = $("#fellyph-convert-btn");
  const $status = $("#fellyph-tts-status");
  const $ui = $("#fellyph-tts-ui");

  $btn.on("click", function () {
    const postId = $(this).data("post-id");

    $btn.prop("disabled", true);
    $status
      .show()
      .removeClass("success-message error-message")
      .text("Processing...");

    $.ajax({
      url: fellyphTTS.ajax_url,
      type: "POST",
      data: {
        action: "convert_post_to_audio",
        post_id: postId,
        nonce: fellyphTTS.nonce,
      },
      success: function (response) {
        if (response.success) {
          $status.addClass("success-message").text("Conversion complete!");

          // Update or add the audio player
          let $audio = $ui.find("audio");
          if ($audio.length) {
            $audio.attr("src", response.data.audio_url);
            $audio[0].load(); // Reload the audio element
          } else {
            $audio = $(
              '<audio controls style="width: 100%; margin-bottom: 10px;"></audio>'
            );
            $audio.attr("src", response.data.audio_url);
            $ui.prepend($audio);
          }

          $btn.text("Regenerate Audio");
        } else {
          $status.addClass("error-message").text("Error: " + response.data);
        }
      },
      error: function (xhr, status, error) {
        $status.addClass("error-message").text("AJAX error: " + error);
      },
      complete: function () {
        $btn.prop("disabled", false);
      },
    });
  });
});
