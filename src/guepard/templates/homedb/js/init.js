$(function(){

$('#catalog-item-autofill').click(function(e){
//	$(this).parents('form').attr({action:'/anime/autofill.html'});
	location.href = '/anime/autofill.html';
	return false;
});
$('input:submit, button, .catalog-last-added .details').button();

});