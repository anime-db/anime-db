$(function(){

$('#catalog-item-autofill').click(function(e){
//	$(this).parents('form').attr({action:'/item-autofill.html'});
	location.href = '/item-autofill.html';
	return false;
});
$('input:submit, button, .catalog-last-added .details').button();




//Get the ul that holds the collection of tags
var collection = $('.b-col-r');

//setup an "add a tag" link
var add_link = $('<a href="#" class="b-col-add-item">Add a tag</a>');
var new_link = $('<div></div>').append(add_link);


// add the "add a tag" anchor and li to the tags ul
collection.append(new_link);

// count the current form inputs we have (e.g. 2), use that as the new
// index when inserting a new item (e.g. 2)
collection.data('index', collection.find(':input').length);

add_link.bind('click', function(e) {
    // prevent the link from creating a "#" on the URL
    e.preventDefault();

    // add a new tag form (see next code block)
    addTagForm(collection, new_link);
    return false;
});

function addTagForm(collection, new_link) {
    // Get the data-prototype explained earlier
    var prototype = collection.data('prototype');

    // get the new index
    var index = collection.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var new_form = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    collection.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var new_link = $('<div></div>').append(new_form);
    new_link.before(new_link);
}

});