/*   TECHWELL (c) 2016 [www.techwell.ru] [hello@techwell.ru] */
/*   Variables   */
CORE   = {
	mode: 'development'
};
COMMON = {
	nofifyTimeout: 4000,
};
FORMS  = {
	loading: false
};
LAYOUT = {map: false};
PAGES  = {type: false};
/* # Variables # */
/*   Core   */
CORE.getURL = function() {
	var surl           = decodeURI(window.location)
	var curl           = new String(surl);
	CORE.url           = {};
	CORE.url.hash      = (window.location.hash.length > 1) ? window.location.hash.substr(1) : '';
	CORE.url.domain    = curl.split('//')[1].split('/')[0];
	CORE.url.params    = curl.split('?')[1] ? curl.split('?')[1].split('#')[0] : false;
	CORE.url.directory = curl.split(CORE.url.domain)[1] ? curl.split(CORE.url.domain)[1].split('?')[0].split('#')[0].substr(1) : false;
	CORE.url.full      = surl;
};
CORE.inURL  = function(txt) {
	if (CORE.url.full.indexOf(txt) > -1) return true;
	else return false;
};
CORE.log    = log = function(text, type) {
	if (CORE.mode !== 'development') return false;
	if (!type) {
		console.log(text);
	} else if (type == 'warn') {
		console.warn(text);
	} else if (type == 'time') {
		if (!window.console.times) window.console.times = {};
		if (!window.console.times || !window.console.times.text) {
			console.time(text);
			window.console.times.text = true;
		} else {
			console.timeEnd(text);
			delete(window.console.times.text);
		}
	} else if (type == 'group') {
		if (!window.console.groups) window.console.groups = {};
		if (!window.console.groups || !window.console.groups.text) {
			console.group(text);
			window.console.groups.text = true;
		} else {
			console.groupEnd(text);
			delete(window.console.groups.text);
		}
	}
};
/* # Core # */
/*   Common functions   */
COMMON.toNum         = function(string) {
	if (!string.length) return false;
	return string.replace(/[^0-9]/g, '');
};
COMMON.checkEmail    = function(email) {
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(email) || (email.length == 0)) return false;
	else return true;
};
COMMON.checkPhone    = function(phone) {
	if (COMMON.toNum(phone) && COMMON.toNum(phone).length == 11) return true;
	else return false;
};
COMMON.checkPassword = function(pass) {
	if (pass.length >= 6) return true;
	else return false;
};
COMMON.formPrice     = function(e, noSuffix) {
	var t = new String(e);
	if (e < 10000) return e + (!noSuffix ? ' Р.' : '');
	else return t.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') + (!noSuffix ? ' Р.' : '');
};
/* # Common functions # */
/*   Layout   */
LAYOUT.init         = function() {
	// Get objects
	this.header  = $('#header');
	this.content = $('#content');
};
LAYOUT.WINDOWS      = {
	count:  0,
	list:   {},
	width:  500,
	height: 500,
	left:   window.screenX + window.outerWidth,
	top:    0,
	watching: false,
};
LAYOUT.WINDOWS.init = function() {
	// Set window size
	this.width  = window.screen.availWidth - window.outerWidth;
	this.height = Math.floor(window.screen.availHeight / 2);
	
	// When leaving page we close all opened windows
	window.onbeforeunload = function (e) {
		LAYOUT.WINDOWS.closeAll();
		var e = e || window.event;
	};
	
	// Update on resize
	if (!this.watching) {
		this.watching = true;
		$(window).on('resize', function() {
			if (LAYOUT.WINDOWS.updateTimeout) clearTimeout(LAYOUT.WINDOWS.updateTimeout);
			LAYOUT.WINDOWS.updateTimeout = setTimeout(function() {
				console.log(LAYOUT.WINDOWS.init());
				LAYOUT.WINDOWS.updateTimeout = false;
			}, 300);
		});
	}
	
	return this;
};
LAYOUT.WINDOWS.open = function(url, focus) {
	if (this.count < 2) {
		// Setting params
		var name = 'w' + Math.round(Math.random() * 10000000);
		var params = [
			'name=' + name,
			'width=' + this.width,
			'height=' + this.height,
			'top=' + this.top,
			'left=' + this.left,
			'titlebar=' + 0,
			'toolbar=' + 0,
			'menubar=' + 0,
			'location=' + 0,
			'status=' + 0,
		];
		var focus = (focus !== undefined) ? focus : false;
		
		// Opening
		this.list[name] = window.open(url, '_blank', params.join(','), false);
		this.count++;
		this.top += this.height;
		
		// Return
		return name;
	} else {
		Materialize.toast('Too much windows already opened. Please, close one or more.');
	}
};
LAYOUT.WINDOWS.close = function(name) {
	if (name === undefined) {
		var names = Object.keys(this.list);
		var name = names[names.length - 1];
		var window = this.list[name];
		if (window === undefined) {
			// Return
			return false;
		}
	}
	if (name && this.list[name]) {
		this.list[name].close();
		delete(this.list[name]);
		this.count--;
		this.top -= this.height;
		
		// Return
		return this.count;
	}
};
LAYOUT.WINDOWS.closeAll = function() {
	for (var i in this.list) {
		var window = this.list[i];
		window.close(window);
		
		// Reset counter and list
		this.count = 0;
		this.list = {};
		this.top = 0;
	}
};
/* # Layout # */
/*   Forms   */
FORMS.init       = function() {
	// Inputs
	FORMS.inputsInit();
	
	// Forms submitting
	$('form.ajax').off().on('submit', function(e) {
		e.preventDefault();
		if (FORMS.loading) return false;
		var $self = $(this);
		var url   = $self.attr('action');
		if (!url) return false;
		
		// Data
		var error = false;
		var data  = $self.serialize();
		
		// Debug
		log(data);
		
		// Error
		if (error) {
			return false;
		}
		
		// Hide messages
		var $ajaxMessageField = $self.find('.ajax-message');
		if ($ajaxMessageField.length) {
			$ajaxMessageField.slideUp().removeClass('error').removeClass('success');
		}
		
		// Submitting
		FORMS.loading = true;
		$.ajax({
			type:     $self.attr('method') ? $self.attr('method') : 'POST',
			url:      url,
			data:     data,
			dataType: 'json',
			complete: function() {
				FORMS.loading = false;
			},
			success:  function(responseData) {
				if (responseData.status == true) {
					if (responseData.message !== null) {
						FORMS.message($ajaxMessageField, responseData.message);
					} else {
						if (responseData.redirectUrl !== null) {
							window.location.href = responseData.redirectUrl;
						}
					}
				} else {
					if (responseData.message !== null) {
						FORMS.message($ajaxMessageField, responseData.message);
					} else {
						location.reload();
					}
				}
			}
		});
	});
};
FORMS.inputsInit = function() {
	// Selects
	$('select').material_select();
	
	// Datepickers
	$('.datepicker').each(function() {
		var firstDay = $(this).attr('data-firstDay');
		$(this).pickadate({
			firstDay: (firstDay !== undefined) ? firstDay : 1
		});
	});
	$('.datepicker[data-start]').each(function() {
		var picker = $(this).pickadate('picker');
		picker.set('select', parseInt($(this).attr('data-start')) * 1000);
	});
};
FORMS.message    = function(field, message) {
	if (field === undefined) return false;
	var $field = field;
	if ($field.length) {
		if (typeof(message) !== 'string') {
			var msg = '';
			for (var key in message) {
				msg += message[key] + '<br>';
			}
		} else {
			var msg = message;
		}
		$field.html(msg).slideDown();
	} else {
		for (var key in message) {
			Materialize.toast(message[key], COMMON.nofifyTimeout);
		}
	}
};
/* # Forms # */
/*   Binds   */
CORE.getURL();
$(document).ready(function() {
	LAYOUT.WINDOWS.init();
});
/* # Binds # */