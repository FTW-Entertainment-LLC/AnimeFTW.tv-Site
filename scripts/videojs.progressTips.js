/*! videojs-progressTips - v0.1.1 - 2013-09-16
 * https://github.com/mickey/videojs-progressTips
 * Copyright (c) 2013 Michael Bensoussan; Licensed MIT */

(function() {
  videojs.plugin('progressTips', function(options) {
    var init = function() {
      var player = this
      var el = $(player.el())
      el.find(".vjs-progress-control")
        .after($("<div id='vjs-tip'><div id='vjs-tip-arrow'></div><div id='vjs-tip-inner'></div></div>"))

      el.find(".vjs-progress-control").on("mousemove", function(event) {
        var seekBar = player.controlBar.progressControl.seekBar
        var seekBarEl = $(seekBar.el())
        var mousePosition = (event.pageX - seekBarEl.offset().left) / seekBar.width()

        var timeInSeconds = mousePosition * player.duration()
        if (timeInSeconds === player.duration()) {
          timeInSeconds = timeInSeconds - 0.1
        }
        var minutes = Math.floor(timeInSeconds / 60)
        var seconds = Math.floor(timeInSeconds - minutes * 60)
        if (seconds < 10) seconds = "0" + seconds

        el.find('#vjs-tip-inner').html("" + minutes + ":" + seconds)

        var barHeight = el.find('.vjs-control-bar').height()

        el.find("#vjs-tip")
          .css("top", "" + (seekBarEl.position().top - barHeight - 20) + "px")
          .css("left", "" + (event.pageX - $(this).offset().left - 30) + "px")
          .css("visibility", "visible")

        return
      })

      el.find(".vjs-progress-control, .vjs-play-control").on("mouseout", function() {
        el.find("#vjs-tip").css("visibility", "hidden")
      })
    }

    this.on("loadedmetadata", init)
  })
}).call(this);