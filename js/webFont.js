WebFontConfig = {
	google: { 
		families: ['Oxygen:700','Open+Sans:400,400i'] 
	}
};
	(
		function() {
			var wf = document.createElement('script');
  		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      		'://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
  		wf.async = 'true';
  		var s = document.getElementsByTagName('script')[0];
  		s.parentNode.insertBefore(wf, s);
		})
	();

