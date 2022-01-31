/* show/hide participants */
$(document).ready(function(){
	$('tr.tgl').hide();
	$('.toggle').click(function(){
		$('tr.tgl').toggle();
	});
	$('.onclk0').click(function(){
		$('#sbm0').addClass('btnr');
	});
	$('.onchg0').change(function(){
		$('#sbm0').addClass('btnr');
	});
});