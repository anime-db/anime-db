
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
	chenge: function(field) {
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
		image.find('.chenge-button')
	);
	field.button.click(function() {
		field.chenge(field);
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
				FormImagePopUp.hide();
				$('body').append(FormImagePopUp.block);
				// apply buttons
				UI.button(FormImagePopUp.block);
				// subscribe field on upload image in pop-up
				FormImagePopUp.onUpload = function(data) {
					field.update(data);
				};
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
	show: function() {
		for (key in Cap.observers) {
			Cap.observers[key].show();
		}
		Cap.element.show();
	},
	observe: function(observer) {
		Cap.observers.push(observer);
	}
};

/**
 * UI
 */
var UI = {
	// add button jQuery UI style
	button: function(context) {
		if (typeof(context) == 'undefined') {
			context = document;
		}
		$(context).find('input:submit, input:button, input:reset, button').button();
	}
}


// init after document load
$(function(){

UI.button();
// init form collection
Form.Collection.init();
// init cap
Cap.setElement($('#cap'));
// init form image
$('.f-image').each(function(){
	new FormImageController($(this));
});

});