(function($){
	$.fn.toggleFade=function(settings){if(settings==undefined){settings={speedIn:'fast'};
}settings=jQuery.extend({speedIn:"normal",
speedOut:settings.speedIn},settings);
return this.each(function(){var isHidden=jQuery(this).is(":hidden");
jQuery(this)[isHidden?"fadeIn":"fadeOut"](isHidden?settings.speedIn:settings.speedOut);});};})(jQuery);(function($){$.fn.toggleSlide=function(settings){if(settings==undefined){settings={speedIn:'slow'};}settings=jQuery.extend({speedIn:"normal",speedOut:settings.speedIn},settings);return this.each(function(){var isHidden=jQuery(this).is(":hidden");jQuery(this)[isHidden?"slideDown":"slideUp"](isHidden?settings.speedIn:settings.speedOut);});};})(jQuery);

jQuery(document).ready(function($) {

	$('div.faq-title a').click(function() {
		$(this).parent().next("div").toggleFade();//dialog({modal: true});
		return false;
	});
	$('div.w-faq-title a').click(function() {
		$(this).parent().next("div").dialog({modal: true});
		return false;
	});

});