$(document).ready(function () {
	log('Dispatcher', 'time');
	PAGES.dispatcher();
	log('Dispatcher', 'time');
});
PAGES.dispatcher = function () {
	if (!PAGES.type) PAGES.type = $('main').attr('data-type');
	
	// Common preparations
	FORMS.init();
	
	// Specific preparations
	switch (PAGES.type) {
		case 'collection.activity':
			PAGES.COLLECTION.init();
			break;
		default:
			break;
	}
};

// Collection
PAGES.COLLECTION = {};
PAGES.COLLECTION.init = function() {
	// Open facebook, viber and other contact windows
	PAGES.COLLECTION.openPersonsContacts();
	
	// Call
	PAGES.COLLECTION.call();
};
PAGES.COLLECTION.call = function() {
	
};
PAGES.COLLECTION.openPersonsContacts = function() {
	// Facebook
	var fbUrl = $('input[name="facebook"]').val();
	if (fbUrl) {
		LAYOUT.WINDOWS.open(fbUrl);
	}
	
	// Viber
};