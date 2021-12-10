﻿(function($) { "use strict";
	
	/* Mobile */
	
	if( navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || 
		navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || 
		navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || 
		navigator.userAgent.match(/Windows Phone/i) ){ 
		var mobile_device = true;
	}else{ 
		var mobile_device = false;
	}
	
	/* Vars */
	
	var $window = jQuery(window);
	var is_RTL  = jQuery('body').hasClass('rtl')?true:false;
	
	/* Menu */
	
	jQuery("nav.nav ul li ul").parent("li").addClass("parent-list");
	jQuery(".parent-list").find("a:first").append(" <span class='menu-nav-arrow'><i class='icon-right-open-mini'></i></span>");
	
	jQuery("nav.nav ul a").removeAttr("title");
	jQuery("nav.nav ul ul").css({display: "none"});
	jQuery("nav.nav ul li").each(function() {	
		var sub_menu = jQuery(this).find("ul:first");
		jQuery(this).hover(function() {	
			sub_menu.stop().css({overflow:"hidden", height:"auto", display:"none", paddingTop:0}).slideDown(200, function() {
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},function() {	
			sub_menu.stop().slideUp(50, function() {	
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});
	
	/* Header fixed */
	
	var fixed_enabled = jQuery("#wrap").hasClass("fixed-enabled");
	if (fixed_enabled && jQuery(".header").length) {
		var hidden_header = jQuery(".hidden-header").offset().top;
		if (hidden_header < 40) {
			var aboveHeight = -20;
		}else {
			var aboveHeight = hidden_header;
		}
		$window.scroll(function(){
			if ($window.scrollTop() > aboveHeight) {
				jQuery(".header").css({"top":"0"}).addClass("fixed-nav");
			}else {
				jQuery(".header").css({"top":"auto"}).removeClass("fixed-nav");
			}
		});
	}else {
		jQuery(".header").removeClass("fixed-nav");
	}
	
	/* Header mobile */
	
	jQuery("nav.nav > ul > li").clone().appendTo('.navigation_mobile > ul');
	
	/* Call to action */
	
	if (jQuery(".call-action-style_1").length) {
		var action_button = jQuery(".call-action-button").outerHeight();
		var action_content = jQuery(".call-action-style_1 .discy-container").outerHeight();
		jQuery(".call-action-button").css({"margin-top":(action_content-action_button)/2});
	}
	
	/* Cover */
	
	if (jQuery(".wpqa-cover-content").length) {
		var author_image = jQuery(".wpqa-cover-content .author-image-span").outerHeight();
		var user_content = jQuery(".wpqa-cover-content .user-content").height();
		jQuery(".wpqa-cover-content .user-content").css({"padding-top":(author_image-user_content)/2});
	}
	
	/* User login */
	
	if (jQuery(".user-click").length) {
		jQuery(".user-click").on("click",function () {
			jQuery(".user-notifications.user-notifications-seen").removeClass("user-notifications-seen").find(" > div").slideUp(200);
			jQuery(".user-messages > div").slideUp(200);
			jQuery(this).parent().toggleClass("user-click-open").find(" > ul").slideToggle(200);
		});
	}
	
	/* Tipsy */
	
	jQuery(".tooltip-n").tipsy({fade:true,gravity:"s"});
	jQuery(".tooltip-s").tipsy({fade:true,gravity:"n"});
	jQuery(".tooltip-nw").tipsy({fade:true,gravity:"nw"});
	jQuery(".tooltip-ne").tipsy({fade:true,gravity:"ne"});
	jQuery(".tooltip-w").tipsy({fade:true,gravity:"w"});
	jQuery(".tooltip-e").tipsy({fade:true,gravity:"e"});
	jQuery(".tooltip-sw").tipsy({fade:true,gravity:"sw"});
	jQuery(".tooltip-se").tipsy({fade:true,gravity:"se"});
	
	if (jQuery(".user-login-click").length) {
		var user_image   = jQuery(".user-login-click .user-image");
		var user_image_h = user_image.outerHeight();
		var user_login   = jQuery(".user-login-click .user-login");
		var user_login_h = user_login.outerHeight();
		user_login.css({"margin-top":(user_image_h-user_login_h)/2});
		
		$window.bind("resize", function () {
			var user_image   = jQuery(".user-login-click .user-image");
			var user_image_h = user_image.outerHeight();
			var user_login   = jQuery(".user-login-click .user-login");
			var user_login_h = user_login.outerHeight();
			user_login.css({"margin-top":(user_image_h-user_login_h)/2});
		});
	}
	
	/* Nav menu */
	
	if (jQuery("nav.nav_menu").length) {
		jQuery(".nav_menu > ul > li > a,.nav_menu > div > ul > li > a").on("click",function () {
			var li_menu = jQuery(this).parent();
			if (li_menu.find(" > ul").length) {
				if (li_menu.hasClass("nav_menu_open")) {
					li_menu.find(" > ul").slideUp(200,function () {
						li_menu.removeClass("nav_menu_open");
					});
				}else {
					li_menu.find(" > ul").slideDown(200,function () {
						li_menu.addClass("nav_menu_open");
					});
				}
				jQuery(".discy-main-wrap,.fixed-sidebar,.fixed_nav_menu").css({"height":"auto"});
				return false;
			}
		});
	}
	
	/* Mobile aside */
	
	if (jQuery('.mobile-aside').length) {
		jQuery('.mobile-aside li.menu-item-has-children').append('<span class="mobile-arrows"><i class="icon-down-open"></i></span>');
		
		jQuery('.mobile-aside-close').on('touchstart click',function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			return false;
		});
		
		jQuery('.mobile-menu-click').on('touchstart click',function () {
			jQuery('.mobile-menu-wrap').addClass('mobile-aside-open');
			return false;
		});
		
		if (jQuery('.mobile-aside ul.menu > li').length) {
			jQuery('.mobile-aside li.menu-item-has-children > a,.mobile-aside li.menu-item-has-children > .mobile-arrows').on("touchstart click",function(){
				jQuery(this).parent().find('ul:first').slideToggle(200);
				jQuery(this).parent().find('> .mobile-arrows').toggleClass('mobile-arrows-open');
				return false;
			});
		}
		
		jQuery('.mobile-aside-inner').mCustomScrollbar({axis:'y'});
	}
	
	/* Post share */
	
	if (jQuery(".post-share").length) {
		var cssArea = (is_RTL == true?"left":"right");
		jQuery(".post-share").each(function () {
			var share_width = jQuery(this).find(" > ul").outerWidth()+20;
			jQuery(this).find(" > ul").css(cssArea,"-"+share_width+"px");
		});
	}
	
	/* Go up */
	
	$window.scroll(function () {
		var cssArea = (is_RTL == true?"left":"right");
		if (jQuery(this).scrollTop() > 100 ) {
			jQuery(".go-up").css(cssArea,"20px");
			jQuery(".ask-button").css(cssArea,(jQuery(".go-up").length?"70px":"20px"));
		}else {
			jQuery(".go-up").css(cssArea,"-60px");
			jQuery(".ask-button").css(cssArea,"20px");
		}
	});
	
	jQuery(".go-up").on("click",function(){
		jQuery("html,body").animate({scrollTop:0},500);
		return false;
	});
	
	/* Tabs */
	
	if (jQuery(".widget ul.tabs").length) {
		jQuery(".widget ul.tabs").tabs(".widget .tab-inner-wrap",{effect:"slide",fadeInSpeed:100});
	}
	
	if (jQuery("ul.tabs-box").length) {
		jQuery("ul.tabs-box").tabs(".tab-inner-wrap-box",{effect:"slide",fadeInSpeed:100});
	}
	
	/* Owl */
	
	if (jQuery(".slider-owl").length) {
		jQuery(".slider-owl").each(function () {
			var $slider = jQuery(this);
			var $slider_item = $slider.find('.slider-item').length;
			$slider.find('.slider-item').css({"height":"auto"});
			if ($slider.find('img').length) {
				var $slider = jQuery(this).imagesLoaded(function() {
					$slider.owlCarousel({
						autoplay: 3000,
						margin: 10,
						responsive: {
							0: {
								items: 1
							}
						},
						autoplayHoverPause: true,
						navText : ["", ""],
						nav: ($slider_item > 1)?true:false,
						rtl: is_RTL,
						loop: ($slider_item > 1)?true:false,
						autoHeight: true
					});
				});
			}else {
				$slider.owlCarousel({
					autoplay: 3000,
					margin: 10,
					responsive: {
						0: {
							items: 1
						}
					},
					autoplayHoverPause: true,
					navText : ["", ""],
					nav: ($slider_item > 1)?true:false,
					rtl: is_RTL == true,
					loop: ($slider_item > 1)?true:false,
					autoHeight: true
				});
			}
		});
	}
	
	/* Accordion & Toggle */
	
	if (jQuery(".accordion").length) {
		jQuery(".accordion .accordion-title").each(function(){
			jQuery(this).on("click",function() {
				if (jQuery(this).parent().parent().hasClass("toggle-accordion")) {
					jQuery(this).parent().find("li:first .accordion-title").addClass("active");
					jQuery(this).parent().find("li:first .accordion-title").next(".accordion-inner").addClass("active");
					jQuery(this).toggleClass("active");
					jQuery(this).next(".accordion-inner").slideToggle(200).toggleClass("active");
					jQuery(this).find("i").toggleClass("icon-minus").toggleClass("icon-plus");
				}else {
					if (jQuery(this).next().is(":hidden")) {
						jQuery(this).parent().parent().find(".accordion-title").removeClass("active").next().slideUp(200);
						jQuery(this).parent().parent().find(".accordion-title").next().removeClass("active").slideUp(200);
						jQuery(this).toggleClass("active").next().slideDown(200);
						jQuery(this).next(".accordion-inner").toggleClass("active");
						jQuery(this).parent().parent().find("i").removeClass("icon-plus").addClass("icon-minus");
						jQuery(this).find("i").removeClass("icon-minus").addClass("icon-plus");
					}
				}
				return false;
			});
		});
	}
	
	/* Flex menu */
	
	if (jQuery("ul.menu.flex").length) {
		jQuery('ul.menu.flex').flexMenu({
			threshold   : 0,
			cutoff      : 0,
			linkText    : '<i class="icon-dot-3"></i>',
			linkTextAll : '<i class="icon-dot-3"></i>',
			linkTitle   : '',
			linkTitleAll: '',
			showOnHover : ($window.width() > 991?true:false),
		});
		
		jQuery("ul.menu.flex .active-tab").parent().parent().addClass("active-menu");
	}
	
	if (jQuery("nav.nav ul").length) {
		jQuery('nav.nav ul').flexMenu({
			threshold   : 0,
			cutoff      : 0,
			linkText    : '<i class="icon-dot-3"></i>',
			linkTextAll : '<i class="icon-dot-3"></i>',
			linkTitle   : '',
			linkTitleAll: '',
			showOnHover : ($window.width() > 991?true:false),
		});
		
		jQuery("nav.nav ul .active-tab").parent().parent().addClass("active-menu");
	}
	
	/* Select */
	
	if (jQuery(".widget select").length) {
		jQuery(".widget select").wrap('<div class="styled-select"></div>');
	}
	
	/* Lightbox */
	
	if (jQuery(".active-lightbox").length) {
		var lightboxArgs = {			
			animation_speed: "fast",
			overlay_gallery: true,
			autoplay_slideshow: false,
			slideshow: 5000, // light_rounded / dark_rounded / light_square / dark_square / facebook
			theme: "pp_default", 
			opacity: 0.8,
			show_title: false,
			social_tools: "",
			deeplinking: false,
			allow_resize: true, // Resize the photos bigger than viewport. true/false
			counter_separator_label: "/", // The separator for the gallery counter 1 "of" 2
			default_width: 940,
			default_height: 529
		};
		
		jQuery("a[href$=jpg], a[href$=JPG], a[href$=jpeg], a[href$=JPEG], a[href$=png], a[href$=gif], a[href$=bmp]:has(img)").prettyPhoto(lightboxArgs);
		jQuery("a[class^='prettyPhoto'], a[rel^='prettyPhoto']").prettyPhoto(lightboxArgs);
	}
	
	/* Load */
	
	$window.load(function() {
		
		/* Loader */
		
		jQuery(".loader").fadeOut(500);
		
		/* Users */
		
		if (jQuery(".user-section-grid,.user-section-simple").length) {
			jQuery('.user-section-grid,.user-section-simple').each(function() {
				jQuery(this).find('> div > div').matchHeight();
			});
		}
		
		if (jQuery(".user-section-columns").length) {
			jQuery('.user-section-columns').each(function() {
				jQuery(this).find('.post-inner').matchHeight();
			});
		}
		
		/* Badges & Tags & Categories */
		
		if (jQuery(".badge-section,.tag-sections,.points-section ul .point-section").length) {
			jQuery(".badge-section > *,.tag-sections,.points-section ul .point-section").matchHeight();
		}
		
		/* 2 columns questions */
		
		if (jQuery(".article-question.post-with-columns").length) {
			jQuery(".article-question.post-with-columns").matchHeight();
			jQuery(".article-question.post-with-columns > .single-inner-content").matchHeight();
		}
		
		/* Sticky Question */
		
		var sticky_sidebar = jQuery(".single-question .question-sticky");
		if (sticky_sidebar.length && $window.width() > 480) {
			jQuery(".single-question .question-vote-sticky").css({"width":sticky_sidebar.outerWidth()});
			jQuery('.single-question .question-vote-sticky').theiaStickySidebar({additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+40,minWidth : sticky_sidebar.outerWidth()});
		}
		
		/* Questions */
		
		if (jQuery(".question-header-mobile").length) {
			$window.bind("resize", function () {
				if (jQuery(this).width() < 480) {
					if (jQuery(".question-header-mobile").length) {
						jQuery(".article-question").each(function () {
							var question_mobile_h = jQuery(this).find(".question-header-mobile").outerHeight()-20;
							var author_image_h = jQuery(this).find(".author-image").outerHeight();
							jQuery(this).find(".author-image").css({"margin-top":(question_mobile_h-author_image_h)/2});
						});
					}
				}else {
					jQuery(".article-question .author-image,.question-image-vote,.question-image-vote .theiaStickySidebar").removeAttr("style");
					jQuery(".article-question .author-image").css({"width":"46px"});
					
					if (sticky_sidebar.length) {
						jQuery(".single-question .question-image-vote").css({"width":sticky_sidebar.outerWidth()});
						jQuery('.single-question .question-image-vote').theiaStickySidebar({additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+40,minWidth : sticky_sidebar.outerWidth()});
					}
				}
			});
			
			if ($window.width() < 480) {
				if (jQuery(".question-header-mobile").length) {
					jQuery(".article-question").each(function () {
						var question_mobile_h = jQuery(this).find(".question-header-mobile").outerHeight()-20;
						var author_image_h = jQuery(this).find(".author-image").outerHeight();
						jQuery(this).find(".author-image").css({"margin-top":(question_mobile_h-author_image_h)/2});
					});
				}
			}
		}
		
		if ($window.width() > 991 && jQuery("section .question-articles > .article-question").length > 3 && jQuery("section .question-articles > .article-question .question-image-vote").length) {
			var last_question_h = jQuery("section .question-articles > .article-question:last-child").height();
			var last_popup_h = jQuery("section .question-articles > .article-question:last-child .author-image-pop-2").height();
			if (last_question_h < last_popup_h) {
				jQuery("section .question-articles > .article-question:last-child .author-image-pop-2").addClass("author-image-pop-top");
			}
			if (jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist").length) {
				var last_question_answer_h = jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment").height();
				var last_answer_popup_h = jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment .author-image-pop-2").height();
				if (last_question_answer_h < last_answer_popup_h) {
					jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment .author-image-pop-2").addClass("author-image-pop-top");
				}
			}
		}
		
	});
	
})(jQuery);

jQuery.noConflict()(function discy_sidebar() {
	var main_wrap_h    = jQuery(".discy-main-wrap").outerHeight();
	var main_sidebar_h = jQuery(".inner-sidebar").outerHeight();
	if (jQuery(".nav_menu_sidebar").length) {
		var nav_menu_h = jQuery(".nav_menu_sidebar").outerHeight();
		jQuery('.discy-main-wrap,.nav_menu_sidebar').matchHeight();
	}else {
		var nav_menu_h = jQuery(".nav_menu").outerHeight();
	}
	if (jQuery('.menu_left').length && nav_menu_h > main_wrap_h) {
		jQuery('.discy-main-inner,nav.nav_menu,div.nav_menu_sidebar').matchHeight();
	}else if ((main_wrap_h > nav_menu_h && jQuery(".fixed_nav_menu").length) || (main_wrap_h > main_sidebar_h && jQuery(".fixed-sidebar").length)) {
		if (jQuery(".fixed_nav_menu").length) {
			jQuery('.discy-main-wrap,.fixed_nav_menu').theiaStickySidebar({additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+jQuery(".admin-bar #wpadminbar").outerHeight()+30});
		}
		if (jQuery(".fixed-sidebar").length) {
			jQuery('.discy-main-wrap,.fixed-sidebar').theiaStickySidebar({additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+jQuery(".admin-bar #wpadminbar").outerHeight()});
		}
	}
});