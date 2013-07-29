
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
		Cap.show(FormImagePopUp.block);
	},
	// update field data
	update: function(data) {
		this.file.val(data.path);
		this.image.attr('src', data.image);
	}
}
// Model PopUp
var FormImageModelPopUp = function(block, remote, local) {
	var that = this;
	this.block = block;
	this.remote = remote;
	this.local = local;

	this.hide();
	this.form = block.find('form').submit(function() {
		that.upload();
		return false;
	});
	Cap.observe(this);
};
FormImageModelPopUp.prototype = {
	// update callback
	onUpload: function() {},
	show: function() {
		this.block.show();
	},
	hide: function() {
		this.block.hide();
	},
	upload: function() {
		var that = this;
		// send form as ajax and call onUpload handler
		this.form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				that.onUpload(data);
				Cap.hide();
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
	if (typeof(FormImagePopUp) == 'undefined') {
		$.ajax({
			url: image.data('popup'),
			type: 'get',
			success: function (data) {
				// create model
				FormImagePopUp = new FormImageModelPopUp(
					$(data),
					$('#image-popup-remote'),
					$('#image-popup-local')
				);
				$('body').append(FormImagePopUp.block);
				// subscribe field on upload image in pop-up
				FormImagePopUp.onUpload = function(data) {
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
		FormLocalPathPopUp.change(this.path.val());
		Cap.show(FormLocalPathPopUp.block);
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
		FormLocalPathPopUp.change(this.folder.attr('href'));
	}
};

// model pop-up
var FormLocalPathModelPopUp = function(block, path, button, folders, prototype, field) {
	this.block = block;
	this.path = path;
	this.button = button;
	this.field = field;
	this.form = block.find('form');
	this.folders = folders;
	this.folder_prototype = prototype;
	this.folder_models = [];

	var that = this;
	this.hide();
	Cap.observe(this);
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
}
FormLocalPathModelPopUp.prototype = {
	show: function() {
		this.block.show();
	},
	hide: function() {
		this.block.hide();
	},
	change: function(value) {
		if (typeof(value) != 'undefined') {
			this.path.val(value);
		}

		// start updating
		this.block.addClass('updating');

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
				that.block.removeClass('updating');
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
		Cap.hide();
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
	if (typeof(FormLocalPathPopUp) == 'undefined') {
		$.ajax({
			url: path.data('popup'),
			type: 'get',
			success: function (data) {
				var popup = $(data);
				var folders = popup.find('.folders');
				// create model
				FormLocalPathPopUp = new FormLocalPathModelPopUp(
					popup,
					popup.find('#local_path_popup_path'),
					popup.find('.change-path'),
					folders,
					folders.data('prototype'),
					field
				);
				$('body').append(FormLocalPathPopUp.block);
			}
		});
	}
};



var Cap = {
	element: null,
	observers: [],
	setElement: function(el) {
		Cap.element = el.click(Cap.hide);
	},
	// hide cup and observers
	hide: function() {
		for (key in Cap.observers) {
			Cap.observers[key].hide();
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