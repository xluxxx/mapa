
"use strict"

var dezSettingsOptions = {};

function getUrlParams(dParam) 
	{
		var dPageURL = window.location.search.substring(1),
			dURLVariables = dPageURL.split('&'),
			dParameterName,
			i;

		for (i = 0; i < dURLVariables.length; i++) {
			dParameterName = dURLVariables[i].split('=');

			if (dParameterName[0] === dParam) {
				return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
			}
		}
	}

(function($) {
	
	"use strict"
	
	var direction =  getUrlParams('dir');
	
	if(direction == 'rtl')
	{
        direction = 'rtl'; 
    }else{
        direction = 'ltr'; 
    }
	
	
	dezSettingsOptions = {
		typography: "poppins",
		version: "light",
		layout: "vertical",
		primary: "color_1",
		headerBg: "color_1",
		navheaderBg: "color_1",
		sidebarBg: "color_1",
		sidebarStyle: "full",
		sidebarPosition: "fixed",
		headerPosition: "fixed",
		containerLayout: "full",
		direction: direction,
		navTextColor:'color_1'
	};

	
	new dezSettings(dezSettingsOptions); 

	jQuery(window).on('resize',function(){
    
        var body = $('body');
        
        var dzTypography = body.data('typography');
        var dzThemeVersion = body.data('theme-version');
        var dzLayout = body.data('layout');
        var dzPrimary = body.data('primary');
        var dzHeaderBg = body.data('headerBg');
        var dzNavHeaderBg = body.data('nav-headerBg');
        var dzSidebarBg = body.data('sidebarBg');
        var dzsidebarStyle = body.data('sidebarStyle');
        var dzHeaderPosition = body.data('headerPosition');
        var dzContainerLayout = body.data('containerLayout');
        //var dzNavTextColor = body.data('navTextColor');
        //var dzNavigationBarImg = body.data('navigationBarImg');
        
        dezSettingsOptions = {
			typography: (dzTypography != '') ? dzTypography :  "poppins",
			version: (dzThemeVersion != '') ? dzThemeVersion : "light",
			layout: (dzLayout != '') ? dzLayout : "vertical",
			primary: (dzPrimary != '') ? dzPrimary : "color_1",
			headerBg: (dzHeaderBg != '') ? dzHeaderBg : "color_1",
			navheaderBg: (dzNavHeaderBg != '') ? dzNavHeaderBg : "color_1",
			sidebarBg: (dzSidebarBg != '') ? dzSidebarBg : "color_1",
			sidebarStyle: (dzSidebarStyle != '') ? dzsidebarStyle : "full",
			sidebarPosition: (dzSidebarPosition != '') ? dzSidebarPosition : "fixed",
			headerPosition: (dzHeaderPosition != '') ? dzHeaderPosition : "fixed",
			containerLayout: (dzContainerLayout != '') ? dzContainerLayout : "full",
			direction: direction,
			//navTextColor: (dzNavTextColor != '') ? dzNavTextColor : "color_1",
			//navigationBarImg: (dzNavigationBarImg != '') ? dzNavigationBarImg : "images/sidebar-img/1.jpg", /* image path or null*/
		};
    
        /*Check container layout on resize */
		///alert(dezSettingsOptions.primary);
        dezSettingsOptions.containerLayout = $('#container_layout').val();
        /*Check container layout on resize END */
        
		//new dezSettings(dezSettingsOptions); 
	});
	
})(jQuery);