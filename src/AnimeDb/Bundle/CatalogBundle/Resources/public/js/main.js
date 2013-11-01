// init after document load
$(function(){

// init cap
Cap.setElement($('#cap'));

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
	new FormCollection(
		collection,
		collection.find('.bt-add-item'),
		collection.find('.f-row'),
		'.bt-remove-item',
		handler
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
});