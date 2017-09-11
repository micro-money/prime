/* Author: JJ Coder, wpjjcoder@gmail.com */
var JJNGGUtils = {
  
  wordpressThumbs: function(html_id, use_thumbs) {
    var nivo_images = jQuery("div#" + html_id + " img.nivo_image");
    var src = null;
    var i = null;
    jQuery("div#" + html_id + " div.nivo-controlNav img").each(function(index, item) {
      src = jQuery(nivo_images[index]).attr("src");
      if(use_thumbs) {
        i = src.lastIndexOf("/");
        jQuery(item).attr("src",  src.substr(0, i) + "/thumbs/thumbs_" + src.substr(i+1));
      }else{
        jQuery(item).attr("src", src);
      }
    });    
  },
  
  wordpressThumbsCenterFix: function(html_id) {
    jQuery("div#" + html_id + " div.nivo-controlNav img:first").addClass("first_thumb");
  }
  
}