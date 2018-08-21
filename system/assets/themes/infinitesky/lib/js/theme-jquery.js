jQuery(document).ready(function($) {	
    $('*:first-child').addClass('first-child');
    $('*:last-child').addClass('last-child');
    $('*:nth-child(even)').addClass('even');
    $('*:nth-child(odd)').addClass('odd');

    $('.section.overlay').wrapInner('<div class="overlay"></div>');


    var numwidgets = $('#footer-widgets div.widget').length;
	$('#footer-widgets').addClass('cols-'+numwidgets);
	$.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
          this.trigger(ev);
          return el.apply(this, arguments);
        };
      });

	$('.nav-footer ul.menu>li').after(function(){
		if(!$(this).hasClass('last-child') && $(this).hasClass('menu-item') && $(this).css('display')!='none'){
			return '<li class="separator menu-item">|</li>';
		}
	});

	$('.genesis-teaser').matchHeight();
    $('.equalize').matchHeight();

    $('.section.align-buttons-bottom .section-content a.btn').wrap('<div class="btn-wrapper"></div>');
    $('.nav-primary .menu li.menu-item .sub-menu .menu-item.separator').html('<hr />');
    $('ul').prev('p').css('margin-bottom','0');

    if (typeof formshow !== 'undefined') {
        // the variable is defined
        var arrayLength = formshow.length;
        console.log(arrayLength);
        var classes = new Array();
        for (var i = 0; i < arrayLength; i++) {
            classes[i] = '.site-inner .gform_wrapper .gform_body ul.gform_fields li.gfield .ginput_container ul.gfield_checkbox li.gchoice_1_1_' + formshow[i];
        }
        var string = '<style>' + classes.join() + '{display: block;}</style>';
        $('.section-what-can-we-solve-with-you').append(string);

    }

    var $_GET = {};
    if(document.location.toString().indexOf('?') !== -1) {
        var query = document.location
            .toString()
            // get the query string
            .replace(/^.*?\?/, '')
            // and remove any existing hash string (thanks, @vrijdenker)
            .replace(/#.*$/, '')
            .split('&');

        for(var i=0, l=query.length; i<l; i++) {
            var aux = decodeURIComponent(query[i]).split('=');
            $_GET[aux[0]] = aux[1];
        }
    }
    var search_term = $_GET['s'];
    if($('.search-form input[type=search]').val() == search_term){
        $('.search-form input[type=search]').val('').attr('placeholder','Search');
    }
});