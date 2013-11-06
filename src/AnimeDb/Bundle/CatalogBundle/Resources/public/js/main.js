var BlockLoadHandler = function() {
	this.observers = [];
};
BlockLoadHandler.prototype = {
	registr: function(observer) {
		if (typeof(observer.update) != 'undefined') {
			this.observers.push(observer);
		}
	},
	unregistr: function(observer) {
		for (var i in this.observers) {
			if (this.observers[i] === observer) {
				delete this.observers[i];
			}
		}
	},
	notify: function(block) {
		for (var i in this.observers) {
			this.observers[i].update(block);
		}
	}
};


/**
 * Form collection
 */
//Model collection
var FormCollection = function(collection, button_add, rows, remove_selector, handler) {
	var that = this;
	this.collection = collection;
	this.index = rows.length;
	this.rows = [];
	this.remove_selector = remove_selector;
	this.button_add = button_add.click(function() {
		that.add();
	});
	this.row_prototype = collection.data('prototype');
	this.handler = handler;
	for (var i = 0; i < rows.length; i++) {
		this.rows.push(new FormCollectionRow($(rows[i]), this));
	}
};
FormCollection.prototype = {
	add: function() {
		// increment index
		this.index++;
		// prototype of new item
		var new_row = new FormCollectionRow(
			$(this.row_prototype.replace(/__name__(label__)?/g, this.index)),
			this
		);
		// notify observers
		this.handler.notify(new_row.row);
		// add row
		this.rows.push(new_row);
		this.button_add.parent().before(new_row.row);
	}
};
// Model collection row
var FormCollectionRow = function(row, collection) {
	this.row = row;
	this.collection = collection;
	// add handler for remove button
	var that = this;
	row.find(collection.remove_selector).click(function() {
		that.remove();
	});
};
FormCollectionRow.prototype = {
	remove: function() {
		this.row.remove();
		// remove row in collection
		for (var i = 0; i < this.collection.rows.length; i++) {
			if (this.collection.rows[i] === this) {
				delete this.collection.rows[i];
				break;
			}
		}
	}
};

var FormCollectionContainer = function() {
	this.collections = [];
};
FormCollectionContainer.prototype = {
	add: function(collection) {
		this.collections[collection.collection.attr('id')] = collection;
	},
	get: function(name) {
		return this.collections[name];
	}
};

/**
 * Form image
 */
// Model Field
var FormImageModelField = function(field, image, button) {
	this.field = field;
	this.image = image;
	this.popup = null;
	var that = this;
	this.button = button.click(function() {
		that.change();
	});
};
FormImageModelField.prototype = {
	change: function() {
		this.popup.show();
	},
	// update field data
	update: function(data) {
		this.field.val(data.path);
		this.image.attr('src', data.image);
	},
	setPopup: function(popup) {
		this.popup = popup;
	}
}
// Model Popup
var FormImageModelPopup = function(popup, remote, local, field) {
	this.remote = remote;
	this.local = local;
	this.popup = popup;
	this.field = field;
	this.field.setPopup(this);
	this.form = popup.body.find('form')
	this.popup.hide();
};
FormImageModelPopup.prototype = {
	show: function() {
		// unbund old hendlers and bind new
		var that = this;
		this.form.unbind('submit').bind('submit', function() {
			that.upload();
			return false;
		});
		// show popup
		this.popup.show();
	},
	upload: function() {
		var that = this;
		// send form as ajax and call onUpload handler
		this.form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				that.field.update(data);
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
	// on load popup
	var init_obj = function (popup) {
		// create model
		new FormImageModelPopup(
			popup,
			$('#image-popup-remote'),
			$('#image-popup-local'),
			field
		);
	};

	// create popup
	if (popup = PopupList.get('image')) {
		init_obj(popup);
	} else {
		PopupList.load('image', {
			url: image.data('popup'),
			success: init_obj
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
	this.popup = null;

	var that = this;
	this.button.click(function() {
		that.change();
	});
};
FormLocalPathModelField.prototype = {
	change: function() {
		this.popup.change(this.path.val());
		this.popup.show();
	},
	setPopup: function(popup) {
		this.popup = popup;
	}
};

// model folder
var FormLocalPathModelFolder = function(folder, path) {
	this.path = path;
	this.popup = null;

	var that = this;
	this.folder = folder.click(function() {
		that.select();
		return false;
	});
};
FormLocalPathModelFolder.prototype = {
	select: function() {
		this.popup.change(this.folder.attr('href'));
	},
	setPopup: function(popup) {
		this.popup = popup;
	}
};

// model pop-up
var FormLocalPathModelPopup = function(popup, path, button, folders, prototype, field) {
	this.popup = popup;
	this.path = path;
	this.button = button;
	this.field = field;
	this.field.setPopup(this);
	this.form = popup.body.find('form');
	this.folders = folders;
	this.folder_prototype = prototype;
	this.folder_models = [];

	var that = this;
	this.popup.hide();
	// apply chenges
	this.button.click(function() {
		that.apply();
		return false;
	});
};
FormLocalPathModelPopup.prototype = {
	show: function() {
		// unbund old hendlers and bind new
		var that = this;
		this.form.unbind('submit').bind('submit', function() {
			that.change();
			return false;
		});
		this.path.unbind('change keyup').bind('change keyup', function() {
			that.change();
			return false;
		});
		// show popup
		this.popup.show();
	},
	change: function(value) {
		if (typeof(value) !== 'undefined') {
			this.path.val(value);
		}
		// return if not full path
		if (this.path.val().length && !(/[\\\/]$/.test(this.path.val()))) {
			return false;
		}

		// start updating
		this.popup.body.addClass('updating');

		var that = this;
		// send form as ajax
		this.form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				that.path.val(data.path);
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
		folder.setPopup(this);
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
	// on load popup
	var init_obj = function (popup) {
		var folders = popup.body.find('.folders');
		// create model
		new FormLocalPathModelPopup(
			popup,
			popup.body.find('#local_path_popup_path'),
			popup.body.find('.change-path'),
			folders,
			folders.data('prototype'),
			field
		);
	};

	// create popup
	if (popup = PopupList.get('local-path')) {
		init_obj(popup);
	} else {
		PopupList.load('local-path', {
			url: path.data('popup'),
			success: init_obj
		});
	}
};


/**
 * Cap for block site
 */
var Cap = {
	element: null,
	observers: [],
	setElement: function(element) {
		Cap.element = element.click(function() {
			Cap.hide();
		});
	},
	// hide cup and observers
	hide: function(observer) {
		if (typeof(observer) !== 'undefined') {
			observer.hide();
		} else {
			for (var i in Cap.observers) {
				Cap.observers[i].hide();
			}
		}
		Cap.element.hide();
	},
	// show cup and observers
	show: function(observer) {
		Cap.element.show();
		observer.show();
	},
	// need methods 'show' and 'hide'
	registr: function(observer) {
		Cap.observers.push($.extend({
			show: function() {},
			hide: function() {}
		}, observer));
	},
	unregistr: function(observer) {
		for (var i in Cap.observers) {
			if (Cap.observers[i] === observer) {
				delete Cap.observers[i];
			}
		}
	},
};

/**
 * Popup
 */
var Popup = function(body) {
	var that = this;
	this.body = body;
	this.close = body.find('.bt-popup-close').click(function() {
		that.hide();
	});
	Cap.registr(this);
};
Popup.prototype = {
	show: function() {
		Cap.show(this.body);
	},
	hide: function() {
		Cap.hide(this.body);
	}
}

var PopupList = {
	list: [],
	load: function(name, options) {
		if (typeof(this.list[name]) == 'undefined') {
			options = $.extend({
				success: function() {}
			}, options||{});

			// init popup on success load popup content
			var success = options.success;
			var that = this;
			options.success = function(data) {
				var popup = new Popup($(data));
				that.list[name] = popup;
				success(popup);
				$('body').append(popup.body);
			}
			// load
			$.ajax(options);
		}
	},
	get: function(name) {
		if (typeof(this.list[name]) != 'undefined') {
			return this.list[name];
		} else {
			return null;
		}
	}
}

/**
 * Notice
 */
var NoticeModel = function(container, block, close_url, close) {
	this.container = container;
	this.block = block;
	this.close_url = close_url;
	this.close_button = close;
	var that = this;
	this.close_button.click(function(){
		that.close();
	});
};
NoticeModel.prototype = {
	close: function() {
		var that = this;
		this.block.animate({opacity: 0}, 400, function() {
			// report to backend
			$.ajax({
				type: 'POST',
				url: that.close_url,
				success: function() {
					// remove this
					that.block.remove();
					delete that.container.notice;
					// load new notice
					that.container.load();
				}
			});
		});
	}
};
/**
 * Notice container
 */
var NoticeContainerModel = function(container, from) {
	this.container = container;
	this.from = from;
	this.notice = null;
	this.load();
};
NoticeContainerModel.prototype = {
	load: function() {
		var that = this;
		this.notice = null;
		$.ajax({
			url: this.from,
			success: function(data) {
				if (data) {
					that.show(data)
				}
			}
		});
	},
	show: function(data) {
		data.notice;
		var block = $(data.content);
		this.notice = new NoticeModel(this, block, data.close, block.find('.bt-close'));
		this.container.append(this.notice.block);
	}
};

/**
 * Check all
 */
var CheckAllModel = function(checker, list) {
	this.checker = checker;
	this.list = list;
	var that = this;
	this.checker.click(function(){
		that.change();
	});
};
CheckAllModel.prototype = {
	change: function() {
		if (this.checker.is(':checked')) {
			this.all();
		} else {
			this.neither();
		}
	},
	all: function() {
		for (var i in this.list) {
			this.list[i].check();
		}
	},
	neither: function() {
		for (var i in this.list) {
			this.list[i].uncheck();
		}
	}
};
// Check all node
var CheckAllNodeModel = function(checkbox) {
	this.checkbox = checkbox;
};
CheckAllNodeModel.prototype = {
	check: function() {
		this.checkbox.prop('checked', true);
	},
	uncheck: function() {
		this.checkbox.prop('checked', false);
	}
};
// Check all in table
var TableCheckAllController = function(checker) {
	var checkboxes = checker.parents('table').find('.'+checker.data('target'));
	var list = [];
	for (var i = 0; i < checkboxes.length; i++) {
		list.push(new CheckAllNodeModel($(checkboxes[i])));
	}
	new CheckAllModel(checker, list);
}

/**
 * Confirm delete
 */
var ConfirmDeleteModel = function(link) {
	this.massage = link.data('massage') || 'Are you sure want to delete this item(s)?';
	this.link = link;
	var that = this;
	link.click(function() {
		return that.remove();
	});
};
ConfirmDeleteModel.prototype = {
	remove: function() {
		return confirm(this.massage);
	}
};


/**
 * Form refill field
 */
FormRefill = function(button, item_id, controller) {
	this.button = button;
	this.item_id = item_id;
	this.controller = controller;

	var that = this;
	this.button.click(function(e) {
		that.refill(e);
		return false;
	});
};
FormRefill.prototype = {
	refill: function(e) {
		console.log(this.button.attr('href'));
	}
};
FormRefillText = function(field) {
	this.field = field;
};
FormRefillSelect = function(field) {
	this.field = field;
};
FormRefillCollection = function(field, collection) {
	this.field = field;
	this.collection = collection; // FormCollection
};


// init after document load
$(function(){

// init cap
Cap.setElement($('#cap'));

var coll_cont = new FormCollectionContainer();

// init form collection
$('.f-collection > div').each(function(){
	// init handler for new row
	var handler = new BlockLoadHandler();
	// form image
	handler.registr({
		update: function(block) {
			block.find('.f-image').each(function(){
				new FormImageController($(this));
			});
		}
	});
	// form local path
	handler.registr({
		update: function(block) {
			block.find('.f-local-path').each(function(){
				new FormLocalPathController($(this));
			});
		}
	});
	// create collection
	var collection = $(this);
	coll_cont.add(
		new FormCollection(
			collection,
			collection.find('.bt-add-item'),
			collection.find('.f-row'),
			'.bt-remove-item',
			handler
		)
	);
});

// init form image
$('.f-image').each(function(){
	new FormImageController($(this));
});
// init form local path
$('.f-local-path').each(function(){
	new FormLocalPathController($(this));
});

// init notice container
var container = $('#notice-container');
if (container.size() && (from = container.data('from'))) {
	new NoticeContainerModel(container, from);
}

// check all
new TableCheckAllController($('.f-table-check-all'));

// confirm delete
$('.item-controls .delete, .storages-list .icon-storage-delete, .b-notice-list button[type=submit]').each(function(){
	new ConfirmDeleteModel($(this));
});

// init form field refill 
$('[data-type=refill]').each(function() {
	var field = $(this);
	if (field.data('prototype')) {
		var controller = new FormRefillCollection(field, coll_cont.get(field.attr('id')));
	} else {
		var controller = new FormRefillText(field);
	}

	// add plugin links and hendler
	$(field.closest('.f-row').find('label')[0])
		.append($(field.data('plugins')))
		.find('a[data-can-refill=1]').each(function() {
			new FormRefill($(this), field.data('id'), controller);
		});
});

});