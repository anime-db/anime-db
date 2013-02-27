$(function(){

$('#catalog-item-autofill').click(function(e){
//	$(this).parents('form').attr({action:'/item-autofill.html'});
	location.href = '/item-autofill.html';
	return false;
});
$('input:submit, button, .catalog-last-added .details').button();

});