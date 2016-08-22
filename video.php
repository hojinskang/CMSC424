<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 ?>
<div style="text-align:center; position: absolute;">
   <form id="search-form" name="search-form" onsubmit="return search()">
      <input type="search" data = "civic" id="search-input" class="search-input" placeholder="" style="display:none;">
   </form>
   <ul id="results" style="width: 375px; border: 0px;"></ul>
</div>
   <script src="jquery.min.js" type="text/javascript"></script>
   <script>
   //*** inside VAL ( ) is where u wanna put $name, and will auto run
   var val = "<?php echo $_SESSION['videoname']?>";
    $('#search-input').val(val);
      var rfm = 'AIzaSyDYwPzLevXauI-kTSVXTLroLyHEONuF9Rw';
      $(function() {
          var searchField = $('#search-input');

          $('#search-form').submit(function(e) {
              e.preventDefault();
          });
      });

      function search() {
        // document.getElementById("#search-input").data = 1;
        // alert(document.getElementById("#search-input").data);
          $('#results').html('');

          q = $('#search-input').val();
          // alert(q);

          $.get(
              "https://www.googleapis.com/youtube/v3/search", {
                  part: 'snippet, id',
                  q: q,
                  maxResults: 1,
                  type: 'video',
                  key: rfm
              },
              function(data) {
                  for (var i = 0; i < data.items.length; i++) {
                      $.get(
                          "https://www.googleapis.com/youtube/v3/videos", {
                              part: 'snippet, contentDetails',
                              key: rfm,
                              id: data.items[i].id.videoId
                          },
                          function(video) {
                              if (video.items.length > 0) {
                                  var output = getResults(video.items[0]);

                                  $("#results").append(output);
                              }
                          });
                  }
              });
      }

      function convertTime(duration) {
          var a = duration.match(/\d+/g);

          if (duration.indexOf('M') >= 0 && duration.indexOf('H') == -1 && duration.indexOf('S') == -1) {
              a = [0, a[0], 0];
          }

          if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1) {
              a = [a[0], 0, a[1]];
          }
          if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1 && duration.indexOf('S') == -1) {
              a = [a[0], 0, 0];
          }

          duration = 0;

          if (a.length == 3) {
              duration = duration + parseInt(a[0]) * 3600;
              duration = duration + parseInt(a[1]) * 60;
              duration = duration + parseInt(a[2]);
          }

          if (a.length == 2) {
              duration = duration + parseInt(a[0]) * 60;
              duration = duration + parseInt(a[1]);
          }

          if (a.length == 1) {
              duration = duration + parseInt(a[0]);
          }
          var h = Math.floor(duration / 3600);
          var m = Math.floor(duration % 3600 / 60);
          var s = Math.floor(duration % 3600 % 60);
          return ((h > 0 ? h + ":" + (m < 10 ? "0" : "") : "") + m + ":" + (s < 10 ? "0" : "") + s);
      }

      function getResults(item) {
          var videoID = item.id;
          var title = item.snippet.title;
          var thumb = item.snippet.thumbnails.high.url;
          var channelProfile = item.snippet.thumbnails;
          var channelTitle = item.snippet.channelTitle;
          var videoDuration = convertTime(item.contentDetails.duration);

          if (title.length > 65) {
              title = title.substring(0, 65).trim() + '...';
          }

          var output =
              '<div id="search-result">' +
              '<div id="video-thumb-container">' +
              '<img src="' + thumb + '" data-videoID="' + videoID + '" id="video-thumb" style="width:375px;"><br/>Click the image above to play the video</div></div>'

          return output;
      }

      $("#results").on("click", "img[data-videoID]", function() {
          playVideo($(this).attr("data-videoID"));
      });

      function playVideo(id) {
          var output =
              '<iframe width="375" height="260" src="http://www.youtube.com/embed/' + id + '?autoplay=1">'+

              '</iframe>'
          document.getElementById("results").innerHTML = output;
          var btn = '<button type="button" onclick="hideVideo()">Exit Video</button>';
          document.getElementById("stop").innerHTML = btn;

      }

      function hideVideo() {
          document.getElementById("results").innerHTML = " ";
          document.getElementById("stop").innerHTML = "";
      }
      // document.getElementById("search-input").innerHTML = "civic";
       search();
   </script>
