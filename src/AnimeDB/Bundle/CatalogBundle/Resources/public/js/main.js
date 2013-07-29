
var Form = {};
Form.Collection = {
	classes: {
		collection: 'b-col-r',
		button_add: 'b-col-add-item',
		button_remove: 'b-col-rm-item'
	},
	templates: {
		row: '<div class="f-row">__data__</div>',
	},

	init: function() {
		var fc = Form.Collection;
		// each collections
		$.each($('.'+fc.classes.collection), function() {
			var collection = $(this);
			collection.data('index', collection.find(':input').length);

			// add "remove" button
			var tpl = '<a href="#" class="'+fc.classes.button_remove+'">'+collection.data('remove')+'</a>';
			collection.children().append($(tpl));

			// add "add" button
			var tpl = '<a href="#" class="'+fc.classes.button_add+'">'+collection.data('add')+'</a>';
			collection.append($(fc.templates.row.replace(/__data__/, tpl)));
		});
		// add "remove" and "add" buttons
		$('.'+fc.classes.button_add).bind('click', fc.onAdd);
		$('.'+fc.classes.button_remove).bind('click', fc.orRemove);
	},

	onAdd: function(e) {
		e.preventDefault();
		var fc = Form.Collection;
		var collection = $(this).parent().parent();
		// increment index
		var index = collection.data('index');
		collection.data('index', index + 1);
		// prototype of new item
		var new_item = collection.data('prototype').replace(/__name__(label__)?/g, index);
		// remove button temaplte
		var tpl = '<a href="#" class="'+fc.classes.button_remove+'">'+fc.templates.remove+'</a>';
		new_item = $(new_item).append($(tpl).bind('click', fc.orRemove));
		// add new item
		collection.find('.'+fc.classes.button_add).parent().before(new_item);
	},
	orRemove: function(e) {
		e.preventDefault();
		$(this).parent().remove();
	}
};



/**
 * Form image
 */
// Model Field
var FormImageModelField = function(file, image, button) {
	this.file = file;
	this.image = image;
	this.button = button;
};
FormImageModelField.prototype = {
	change: function() {
		FormImagePopup.popup.show();
	},
	// update field data
	update: function(data) {
		this.file.val(data.path);
		this.image.attr('src', data.image);
	}
}
// Model Popup
var FormImageModelPopup = function(popup, remote, local) {
	var that = this;
	this.remote = remote;
	this.local = local;
	this.popup = popup;

	this.popup.hide();
	this.form = popup.body.find('form').submit(function() {
		that.upload();
		return false;
	});
};
FormImageModelPopup.prototype = {
	// update callback
	onUpload: function() {},
	upload: function() {
		var that = this;
		// send form as ajax and call onUpload handler
		this.form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				that.onUpload(data);
				that.popup.hide();
				that.form.resetForm();
			},
			error: function(data, error, message) {
				// for normal error
				if (data.status == 404) {
					data = JSON.parse(data.responseText);
					if (typeof(data.error) !== 'undefined' && data.error) {
						message = data.error;
					}
				}
				alert(message);
			}
		});
	}
};
// Image controller
var FormImageController = function(image) {
	var field = new FormImageModelField(
		image.find('input'),
		image.find('img'),
		image.find('.change-button')
	);
	field.button.click(function() {
		field.change();
	});

	// create popup
	if (typeof(FormImagePopup) == 'undefined') {
		new PopupFromUrl({
			url: image.data('popup'),
			success: function (popup) {
				// create model
				FormImagePopup = new FormImageModelPopup(
					popup,
					$('#image-popup-remote'),
					$('#image-popup-local')
				);
				// subscribe field on upload image in pop-up
				FormImagePopup.onUpload = function(data) {
					field.update(data);
				};
			}
		});
	}
};



/**
 * Form local path
 */
// model field
var FormLocalPathModelField = function(path, button) {
	this.path = path;
	this.button = button;

	var that = this;
	this.button.click(function() {
		that.change();
	});
};
FormLocalPathModelField.prototype = {
	change: function() {
		FormLocalPathPopup.change(this.path.val());
		FormLocalPathPopup.popup.show();
	}
};

// model folder
var FormLocalPathModelFolder = function(folder, path) {
	this.path = path;

	var that = this;
	this.folder = folder.click(function() {
		that.select();
		return false;
	});
};
FormLocalPathModelFolder.prototype = {
	select: function() {
		FormLocalPathPopup.change(this.folder.attr('href'));
	}
};

// model pop-up
var FormLocalPathModelPopup = function(popup, path, button, folders, prototype, field) {
	this.popup = popup;
	this.path = path;
	this.button = button;
	this.field = field;
	this.form = popup.body.find('form');
	this.folders = folders;
	this.folder_prototype = prototype;
	this.folder_models = [];

	var that = this;
	this.popup.hide();
	// chenge input element
	this.path.change(function() {
		that.change();
	});
	this.form.submit(function() {
		that.change();
	});
	// apply chenges
	this.button.click(function() {
		that.apply();
		return false;
	});
};
FormLocalPathModelPopup.prototype = {
	change: function(value) {
		if (typeof(value) != 'undefined') {
			this.path.val(value);
		}

		// start updating
		this.popup.body.addClass('updating');

		var that = this;
		// send form as ajax
		this.form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				// remove old folders
				that.clearFoldersList();

				// create folders
				for (var i in data.folders) {
					// prototype of new item
					var new_item = that.folder_prototype
						.replace('__name__', data.folders[i].name)
						.replace('__link__', data.folders[i].path);
					that.addFolder(new FormLocalPathModelFolder($(new_item), that.path));
				}
			},
			error: function(data, error, message) {
				alert(message);
			},
			complete: function() {
				that.popup.body.removeClass('updating');
			}
		});
	},
	clearFoldersList: function() {
		this.folder_models = [];
		this.folders.text('');
	},
	addFolder: function(folder) {
		this.folder_models.push(folder);
		this.folders.append(folder.folder);
	},
	apply: function() {
		this.field.path.val(this.path.val());
		this.popup.hide();
	}
};
// Form local path controller
var FormLocalPathController = function(path) {
	// create field model
	var field = new FormLocalPathModelField(
		path.find('input'),
		path.find('.change-path')
	);

	// create popup
	if (typeof(FormLocalPathPopup) == 'undefined') {
		new PopupFromUrl({
			url: path.data('popup'),
			success: function (popup) {
				var folders = popup.body.find('.folders');
				// create model
				FormLocalPathPopup = new FormLocalPathModelPopup(
					popup,
					popup.body.find('#local_path_popup_path'),
					popup.body.find('.change-path'),
					folders,
					folders.data('prototype'),
					field
				);
			}
		});
	}
};



var Cap = {
	element: null,
	observers: [],
	setElement: function(el) {
		Cap.element = el.click(function() {
			Cap.hide();
		});
	},
	// hide cup and observers
	hide: function(observer) {
		if (typeof(observer) !== 'undefined') {
			observer.hide();
		} else {
			for (key in Cap.observers) {
				Cap.observers[key].hide();
			}
		}
		Cap.element.hide();
	},
	// show cup and observers
	show: function(observer) {
		Cap.element.show();
		observer.show();
	},
	observe: function(observer) {
		Cap.observers.push(observer);
	}
};


var Popup = function(body) {
	var that = this;
	this.body = body;
	this.close = body.find('.bt-popup-close').click(function() {
		that.hide();
	});
	Cap.observe(this);
};
Popup.prototype = {
	show: function() {
		Cap.show(this.body);
	},
	hide: function() {
		Cap.hide(this.body);
	},
	load: function(options) {
	}
}

var PopupFromUrl = function(options) {
	options = $.extend({
		success: function() {},
	}, options||{});

	// init popup on success load popup content
	var success = options.success;
	options.success = function(data) {
		var popup = new Popup($(data));
		success(popup);
		$('body').append(popup.body);
	}
	// load
	$.ajax(options);
}


// init after document load
$(function(){

// init form collection
Form.Collection.init();
// init cap
Cap.setElement($('#cap'));
// init form image
$('.f-image').each(function(){
	new FormImageController($(this));
});
// init form local path
$('.f-local-path').each(function(){
	new FormLocalPathController($(this));
});

});