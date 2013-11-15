var BlockLoadHandler = function() {
	this.observers = [];
};
BlockLoadHandler.prototype = {
	registr: function(observer) {
		if (typeof(observer.update) == 'function') {
			this.observers.push(observer);
		} else if (typeof(observer) == 'function') {
			this.observers.push({update:observer});
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
		var row = new FormCollectionRow($(rows[i]));
		row.setCollection(this);
		this.rows.push(row);
	}
};
FormCollection.prototype = {
	add: function() {
		var row = new FormCollectionRow($(this.row_prototype.replace(/__name__(label__)?/g, this.index + 1)));
		this.addRowObject(row);
		return row;
	},
	addRowObject: function(row) {
		row.setCollection(this);
		// notify observers
		this.handler.notify(row.row);
		// add row
		this.rows.push(row);
		this.button_add.parent().before(row.row);
		// increment index
		this.index++;
	}
};
// Model collection row
var FormCollectionRow = function(row) {
	this.row = row;
	this.collection = null;
};
FormCollectionRow.prototype = {
	remove: function() {
		this.row.remove();
		var rows = [];
		// remove row in collection
		for (var i = 0; i < this.collection.rows.length; i++) {
			if (this.collection.rows[i] !== this) {
				rows.push(this.collection.rows[i]);
			}
		}
		this.collection.rows = rows;
	},
	setCollection: function(collection) {
		this.collection = collection;
		// add handler for remove button
		var that = this;
		this.row.find(collection.remove_selector).click(function() {
			that.remove();
		});
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
	},
	remove: function(name) {
		delete this.collections[name];
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
	html: $('html'),
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
		Cap.html.removeClass('scroll-lock');
	},
	// show cup and observers
	show: function(observer) {
		Cap.element.show();
		observer.show();
		Cap.html.addClass('scroll-lock');
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
	}
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
	popup_loader: null,
	list: [],
	load: function(name, options) {
		options = $.extend({
			success: function() {},
			error: function() {
				if (confirm('Failed to get the data. Want to try again?')) {
					$.ajax(options);
				} else {
					PopupList.popup_loader.hide();
				}
			}
		}, options||{});

		if (typeof(PopupList.list[name]) != 'undefined') {
			options.success(PopupList.list[name]);
		} else {
			// init popup on success load popup content
			var success = options.success;
			options.success = function(data) {
				PopupList.list[name] = new Popup($(data));
				success(PopupList.list[name]);
				$('body').append(PopupList.list[name].body);
			}
			// load
			$.ajax(options);
		}
	},
	get: function(name) {
		if (typeof(PopupList.list[name]) != 'undefined') {
			return PopupList.list[name];
		} else {
			return null;
		}
	},
	lazyload: function(name, options) {
		var timeout = 0;
		if (PopupList.popup_loader == null) {
			PopupList.popup_loader = new Popup($('#b-lazyload-popup'));
			timeout = 50;
		}

		options = $.extend({
			success: function() {},
			error: function() {
				if (confirm('Failed to get the data. Want to try again?')) {
					$.ajax(options);
				} else {
					PopupList.popup_loader.hide();
				}
			}
		}, options||{});

		if (typeof(PopupList.list[name]) != 'undefined') {
			options.success(PopupList.list[name]);
		} else {
			PopupList.popup_loader.show();

			// init popup on success load popup content
			var success = options.success;
			options.success = function(data) {
				var popup = new Popup(PopupList.popup_loader.body.clone().hide());
				popup.body.attr('id', name).find('.content').append(data);
				$('body').append(popup.body);

				PopupList.list[name] = popup;
				success(popup);

				// animate show popup
				var width = popup.body.width();
				var height = popup.body.height();
				PopupList.popup_loader.body.find()
				PopupList.popup_loader.body.addClass('resize').animate({
					'width': width,
					'height': height,
					'margin-left': -(width/2),
					'margin-top': -(height/2)
				}, 400, function() {
					popup.show();
					// reset style
					PopupList.popup_loader.body.removeClass('resize').removeAttr('style').hide();
				});
			}

			// postpone downloading of content to have time to load popap
			if (timeout) {
				setTimeout(function() {
					$.ajax(options);
				}, timeout);
			} else {
				$.ajax(options);
			}
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
var FormRefill = function(button, item_id, controller, handler) {
	this.button = button;
	this.item_id = item_id;
	this.controller = controller;
	this.handler = handler;

	var that = this;
	this.button.click(function() {
		if (that.button.data('can-refill') == 1) {
			that.refill();
		} else {
			that.search();
		}
		return false;
	});
};
FormRefill.prototype = {
	refill: function() {
		var name = 'refill-form-' + this.controller.field.attr('id');
		// create popup
		var that = this;
		if (popup = PopupList.get(name)) {
			this.init_refill_popup(popup);
			popup.show();
		} else {
			PopupList.lazyload(name, {
				url: this.button.attr('href'),
				success: function(popup) {
					that.handler.notify(popup.body);
					that.init_refill_popup(popup);
				}
			});
		}
	},
	search: function() {
		// create popup
		var that = this;
		if (popup = PopupList.get('refill-search')) {
			this.init_search_popup(popup);
			popup.show();
		} else {
			PopupList.lazyload('refill-search', {
				url: this.button.attr('href'),
				success: function(popup) {
					that.init_search_popup(popup);
				}
			});
		}
	},
	init_refill_popup: function (popup) {
		var that = this;
		popup.body.find('form').submit(function() {
			that.update(popup);
			return false;
		});
	},
	init_search_popup: function (popup) {
		var that = this;
		popup.body.find('a').each(function() {
			new FormRefillSearchItem(that, popup, $(this));
		});
	},
	update: function(popup) {
		this.controller.update(popup);
		popup.hide();
	}
};
var FormRefillSimple = function(field) {
	this.field = field;
};
FormRefillSimple.prototype = {
	update: function(popup) {
		this.field.val(popup.body.find('#'+this.field.attr('id')).val());
	}
};
var FormRefillCollection = function(field, collection, container) {
	this.field = field;
	this.collection = collection; // FormCollection
	this.container = container; // FormCollectionContainer
};
FormRefillCollection.prototype = {
	update: function(popup) {
		// remove old rows
		while (this.collection.rows.length) {
			this.collection.rows[0].remove();
		}
		// add new rows
		var collection = this.container.get(this.field.attr('id'));
		for (var i = 0; i < collection.rows.length; i++) {
			this.collection.addRowObject(new FormCollectionRow(collection.rows[i].row.clone()));
		}
	}
};
var FormRefillSearchItem = function(form, popup, link) {
	var that = this;
	this.form = form;
	this.popup = popup;
	this.link = link.click(function() {
		that.refill();
		return false;
	});
	
};
FormRefillSearchItem.prototype = {
	refill: function() {
		this.popup.hide();
		// TODO show search result popup
	}
};


// init after document load
$(function(){

// init cap
Cap.setElement($('#cap'));

var CollectionContainer = new FormCollectionContainer();

var FormContainer = new BlockLoadHandler();

// registr form collection
FormContainer.registr(function(block) {
	block.find('.f-collection > div').each(function() {
		var collection = $(this);
		CollectionContainer.add(
			new FormCollection(
				collection,
				collection.find('.bt-add-item'),
				collection.find('.f-row:not(.f-coll-button)'),
				'.bt-remove-item',
				FormContainer
			)
		);
	});
});

// registr form image
FormContainer.registr(function(block) {
	block.find('.f-image').each(function() {
		new FormImageController($(this));
	});
});
// registr form local path
FormContainer.registr(function(block) {
	block.find('.f-local-path').each(function() {
		new FormLocalPathController($(this));
	});
});

// registr form check all
FormContainer.registr(function(block) {
	new TableCheckAllController(block.find('.f-table-check-all'));
});


// apply form for document
FormContainer.notify($(document));


// init notice container
var container = $('#notice-container');
if (container.size() && (from = container.data('from'))) {
	new NoticeContainerModel(container, from);
}

// confirm delete
$('.item-controls .delete, .storages-list .icon-storage-delete, .b-notice-list button[type=submit]').each(function(){
	new ConfirmDeleteModel($(this));
});

// init form field refill 
$('[data-type=refill]').each(function() {
	var field = $(this);
	if (field.data('prototype')) {
		var controller = new FormRefillCollection(
			field,
			CollectionContainer.get(field.attr('id')),
			CollectionContainer
		);
	} else {
		var controller = new FormRefillSimple(field);
	}

	// add plugin links and hendler
	$(field.closest('.f-row').find('label')[0])
		.append($(field.data('plugins')))
		.find('a').each(function() {
			new FormRefill($(this), field.data('id'), controller, FormContainer);
		});
});

});