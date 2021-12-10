﻿(function($) { "use strict";
	
	/* Search */
	
	if (jQuery(".search-click").length) {
		jQuery(".search-click").on('touchstart click', function(){
			jQuery(".header-search").addClass("header-search-active");
			jQuery(".header-search input[type='search']").focus();
			jQuery(".search-click").hide();
			jQuery(".discy-content").on("click",function () {
				jQuery(".search-click").show();
				jQuery(".header-search").removeClass("header-search-active");
			});
		});
	}
	
	if (jQuery(".search_type.user_filter_active").length) {
		jQuery(".search_type.user_filter_active").change(function () {
			var ThisSelect = jQuery(this);
			if (ThisSelect.val() == "users") {
				jQuery(".post-search .row > .col").removeClass("col6").addClass("col4");
				ThisSelect.parent().parent().parent().find(".user-filter-div select").attr("name","user_filter");
				jQuery(".user-filter-div").animate({
					opacity: 'show',
					height: 'show'
				},200, function() {
					jQuery(".user-filter-div").removeClass('hide');
				});
			}else {
				jQuery(".user-filter-div").animate({
					opacity: 'hide',
					height: 'hide'
				},200, function() {
					jQuery(".user-filter-div").addClass('hide');
					jQuery(".post-search .row > .col").removeClass("col4").addClass("col6");
					ThisSelect.parent().parent().parent().removeAttr("name");
				});
			}
		});
	}
	
	if (jQuery(".mobile-bar-search").length) {
		jQuery(".mobile-bar-search > a").click(function () {
			jQuery(".mobile-bar-search > form").animate({
				opacity: 'show',
				height: 'show'
			},100, function() {
				jQuery(".mobile-bar-search").addClass('mobile-bar-search-active');
			});
			return false;
		});
		jQuery(".mobile-bar-search form i").click(function () {
			jQuery(".mobile-bar-search > form").animate({
				opacity: 'hide',
				height: 'hide'
			},100, function() {
				jQuery(".mobile-bar-search").removeClass('mobile-bar-search-active');
			});
		});
	}

	if (jQuery(".live-search").length) {
		jQuery(".live-search").each(function () {
			var main_live_search = jQuery(this);
			var typingTimer;
			var doneTypingInterval = 500;
			main_live_search.on("keyup",function() {
				var live_search  = jQuery(this);
				var search_value = live_search.val();
				if (search_value == "") {
					live_search.closest(".main-search-form").find(".search-results").addClass("results-empty").html("").hide();
				}else {
					var search_type = live_search.closest(".main-search-form").find(".search_type").val();
					var search_loader = live_search.closest(".main-search-form").find(".search_loader");
					clearTimeout(typingTimer);
					typingTimer = setTimeout(function () {
						if (live_search.hasClass("live-search-icon")) {
							live_search.closest(".main-search-form").find("i.icon-search").attr("class","icon-arrows-ccw fa-spin");
						}else {
							search_loader.show(10);
						}
						jQuery.ajax({
							url: wpqa_js.admin_url,
							type: "POST",
							data: { action : 'wpqa_live_search',search_value : search_value,search_type : search_type },
							success:function(data) {
								live_search.closest(".main-search-form").find(".search-results").removeClass("results-empty").html(data).slideDown(300);
								if (live_search.hasClass("live-search-icon")) {
									live_search.closest(".main-search-form").find("i.icon-arrows-ccw").attr("class","icon-search");
								}else {
									search_loader.hide(10);
								}
							}
						});
					},500);
				}
			});
			
			main_live_search.on('focus',function() {
				var live_search  = jQuery(this);
				if (live_search.closest(".main-search-form").find(".results-empty").length == 0) {
					live_search.closest(".main-search-form").find(".search-results").show();
				}
			});
			
			jQuery(".search_type").change(function () {
				if (jQuery(this).closest(".main-search-form").find(".results-empty").length == 0) {
					jQuery(this).closest(".main-search-form").find(".search-results").addClass("results-empty").html("").hide();
				}
			});
			
			var outputContainer = main_live_search.closest(".main-search-form").find(".search-results");
			var input 			= main_live_search.get(0);
			jQuery('body').bind('click',function(e) {
				if (!jQuery.contains(outputContainer.get(0),e.target) && e.target != input) {
					outputContainer.hide();
				}
			});
		});
	}
	
	/* Fake file */
	
	jQuery(".fileinputs input[type='file']").live("change",function () {
		var file_fake = jQuery(this);
		file_fake.parent().find("button").text(file_fake.val());
	});
	
	jQuery(".fakefile").live("click",function () {
		jQuery(this).parent().find("input[type='file']").click();
	});

	/* Remove image */

	if (jQuery(".wpqa-remove-image").length) {
		jQuery(".wpqa-remove-image").on("click",function () {
			var image_this = jQuery(this);
			var image_name = image_this.data("name");
			if (confirm((image_name == "added_file"?wpqa_js.wpqa_remove_attachment:wpqa_js.wpqa_remove_image))) {
				var image_type  = image_this.data("type");
				var image_id    = image_this.data("id");
				var image_nonce = image_this.data("nonce");
				image_this.hide();
				image_this.parent().find(".loader_4").addClass("wpqa-remove-loader");
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: { action : 'wpqa_remove_image', wpqa_remove_image : image_nonce, image_name : image_name, image_type : image_type, image_id : image_id },
					success:function(data) {
						if (data == "") {
							if (image_name == "added_file") {
								image_this.parent().hide();
								if (image_this.closest(".wpqa-delete-attachment").find("li").length == 1) {
									image_this.closest(".wpqa-delete-attachment").hide().remove();
								}
							}else {
								image_this.parent().find(".wpqa-delete-image-span").hide();
							}
						}else {
							image_this.parent().find(".wpqa-delete-image-span").html(data);
						}
						image_this.parent().find(".loader_4").hide();
						image_this.remove();
					}
				});
			}
			return false;
		});
	}

	/* Delete attachment */

	if (jQuery(".delete-this-attachment").length) {
		jQuery(".delete-this-attachment").click(function () {
			var answer = confirm(wpqa_js.wpqa_remove_attachment);
			if (answer) {
		    	var delete_attachment = jQuery(this);
		    	var attachment_id = delete_attachment.attr("href");
		    	var post_id = delete_attachment.data("id");
		    	var single_attachment = "No";
		    	delete_attachment.hide();
		    	delete_attachment.parent().find(".loader_4").addClass("wpqa-remove-loader");
		    	if (delete_attachment.hasClass("single-attachment")) {
		    		single_attachment = "Yes";
		    	}
		    	jQuery.post(wpqa_js.admin_url,"action=wpqa_confirm_delete_attachment&attachment_id="+attachment_id+"&post_id="+post_id+"&single_attachment="+single_attachment,function (result) {
		    		delete_attachment.parent().find(".loader_4").hide();
		    		delete_attachment.parent().fadeOut(function() {
		    			jQuery(this).remove();
		    		});
		    	});
			}
			return false;
		});
	}
	
	/* Ask Question */

	jQuery(".button-hide-click").live("click",function () {
		var button_click = jQuery(this);
		button_click.hide().parent().find(".load_span").show().css({"display":"block"});
	});
	
	if (jQuery(".cat-ajax").length) {
		jQuery('.category-wrap').on('change','.cat-ajax',function() {
			var currentLevel = parseInt(jQuery(this).parent().parent().data('level'));
			wpqa_child_cats(jQuery(this),'wpqa-level-',currentLevel+1);
		});
	}
	
	if (jQuery(".question_tags,.post_tag").length) {
		jQuery('.question_tags,.post_tag').tag();
	}
	
	if (jQuery(".poll_options").length) {
		jQuery(".poll_options").each(function () {
			var poll_this = jQuery(this);
			var question_poll = poll_this.parent().find(".question_poll").is(":checked");
			if (question_poll == 1) {
				poll_this.slideDown(200);
			}else {
				poll_this.slideUp(200);
			}
			
			poll_this.parent().find(".question_poll").on("click",function () {
				var question_poll_c = poll_this.parent().find(".question_poll").is(":checked");
				if (question_poll_c == 1) {
					poll_this.slideDown(200);
				}else {
					poll_this.slideUp(200);
				}
			});
		});
	}
	
	if (jQuery(".question_polls_item,.question_upload_item").length) {
		jQuery(".question_polls_item,.question_upload_item").sortable({placeholder: "ui-state-highlight"});
	}
	
	if (jQuery(".add_poll_button_js,.question_image_poll").length) {
		jQuery(".add_poll_button_js,.question_image_poll").on("click",function() {
			var poll_this = jQuery(this);
			var poll_options = poll_this.closest(".poll_options");
			
			if (poll_this.hasClass("question_image_poll")) {
				var question_image_poll = poll_this;
				poll_options.find(".question_polls_item > li").remove();
			}else {
				var question_image_poll = poll_options.find(".question_image_poll");
			}

			var question_image_poll_c = question_image_poll.is(":checked");
			var add_poll = poll_options.find(".question_items > li").length;
			if (add_poll > 0) {
				var i_count = 0;
				while (i_count < add_poll) {
					if (poll_options.find(".question_items > #poll_li_"+add_poll).length) {
						add_poll++;
					}
					i_count++;
				}
			}else {
				add_poll++;
			}
			
			var wpqa_poll_image = (question_image_poll_c == 1 && wpqa_js.poll_image == 'on'?'<div class="attach-li"><div class="fileinputs"><input type="file" class="file" name="ask['+add_poll+'][image]" id="ask['+add_poll+'][image]"><i class="icon-camera"></i><div class="fakefile"><button type="button">'+wpqa_js.select_file+'</button><span><i class="icon-arrow-up"></i>'+wpqa_js.browse+'</span></div></div></div>':'');

			var wpqa_poll_title = (question_image_poll_c != 1 || wpqa_js.poll_image != 'on' || (wpqa_js.poll_image == 'on' && wpqa_js.poll_image_title == 'on')?'<p><input class="ask" name="ask['+add_poll+'][title]" value="" type="text"><i class="icon-comment"></i></p>':'');

			poll_options.find('.question_items').append('<li id="poll_li_'+add_poll+'">'+wpqa_poll_image+'<div class="poll-li">'+wpqa_poll_title+'<input name="ask['+add_poll+'][id]" value="'+add_poll+'" type="hidden"><div class="del-item-li"><i class="icon-cancel"></i></div><div class="move-poll-li"><i class="icon-menu"></i></div></div></li>');
			
			jQuery('#poll_li_'+add_poll).hide().fadeIn();
			
			jQuery(".del-item-li").on("click",function() {
				jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			});

			if (!poll_this.hasClass("question_image_poll")) {
				return false;
			}
		});
	}
	
	if (jQuery(".del-item-li").length) {
		jQuery(".del-item-li").on("click",function() {
			jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
				jQuery(this).remove();
			});
		});
	}
	
	if (jQuery(".video_description_input,.video_description").length) {
		jQuery(".video_description").each(function () {
			var video_this = jQuery(this);
			var video_description = video_this.parent().find(".video_description_input").is(":checked");
			if (video_description == 1) {
				video_this.slideDown(200);
			}else {
				video_this.slideUp(200);
			}
			
			video_this.parent().find(".video_description_input").on("click",function () {
				var video_description_c = video_this.parent().find(".video_description_input").is(":checked");
				if (video_description_c == 1) {
					video_this.slideDown(200);
				}else {
					video_this.slideUp(200);
				}
			});
		});
	}
	
	if (jQuery(".video_answer_description_input,.video_answer_description").length) {
		var video_this = jQuery(".video_answer_description");
		var video_description = video_this.parent().find(".video_answer_description_input").is(":checked");
		if (video_description == 1) {
			video_this.slideDown(200);
		}else {
			video_this.slideUp(200);
		}
		
		video_this.parent().find(".video_answer_description_input").on("click",function () {
			var video_description_c = video_this.parent().find(".video_answer_description_input").is(":checked");
			if (video_description_c == 1) {
				video_this.slideDown(200);
			}else {
				video_this.slideUp(200);
			}
		});
	}
	
	if (jQuery(".add_upload_button_js").length) {
		jQuery(".add_upload_button_js").on("click",function() {
			var add_attach = jQuery(this).parent().find(".question_items > li").length;
			if (add_attach > 0) {
				var i_count = 0;
				while (i_count < add_attach) {
					if (jQuery(this).parent().find(".question_items > #attach_li_"+add_attach).length) {
						add_attach++;
					}
					i_count++;
				}
			}else {
				add_attach++;
			}
			jQuery(this).parent().find('.question_items').append('<li id="attach_li_'+add_attach+'"><div class="attach-li"><div class="fileinputs"><input type="file" class="file" name="attachment_m['+add_attach+'][file_url]" id="attachment_m['+add_attach+'][file_url]"><i class="icon-camera"></i><div class="fakefile"><button type="button">'+wpqa_js.select_file+'</button><span><i class="icon-arrow-up"></i>'+wpqa_js.browse+'</span></div><div class="del-item-li"><i class="icon-cancel"></i></div><div class="move-poll-li"><i class="icon-menu"></i></div></div></div></li>');
			jQuery(".fileinputs input[type='file']").change(function () {
				var file_fake = jQuery(this);
				file_fake.parent().find("button").text(file_fake.val());
			});
			jQuery(".fakefile").on("click",function () {
				jQuery(this).parent().find("input[type='file']").click();
			});
			jQuery('#attach_li_'+add_attach).hide().fadeIn();
			jQuery(".del-item-li").on("click",function() {
				jQuery(this).parent().parent().parent().fadeOut(function() {
					jQuery(this).remove();
				});
			});
			return false;
		});
	}
	
	if (jQuery(".the-details").length) {
		jQuery("#wp-question-details-wrap").appendTo(".the-details");
		jQuery("#wp-post-details-wrap").appendTo(".the-details");
	}
	
	/* Panel pop */
	
	if (jQuery(".panel-pop > i").length) {
		jQuery(".panel-pop > i").on("click",function () {
			jQuery.when(jQuery(this).parent().fadeOut(200)).done(function() {
				jQuery(this).css({"top":"-100%","display":"none"});
				jQuery("#wpqa-message .the-title").val("");
				jQuery(".wrap-pop").remove();
			});
		});
	}
	
	un_login_panel("#signup-panel",".signup-panel-un");
	un_login_panel("#lost-password",".discy_users_only .reset-password,.lost-password-login");
	un_login_panel("#lost-password",".lost-passwords","no",".discy_users_only");
	un_login_panel("#login-panel",".login-panel-un");
	
	function un_login_panel(whatId,whatClass,whatFrom,bodyClass) {
		jQuery((whatFrom == "no"?(bodyClass != ""?bodyClass+" ":"")+".wpqa_form,":"")+whatClass).on("click",(whatFrom == "no"?whatClass:""),function() {
			var data_width = jQuery(whatId).attr("data-width");
			jQuery(".panel-un-login").hide(10);
			jQuery(whatId).animate({opacity: 'show' , height: 'show'}, 400);
			return false;
		});
	}
	
	panel_pop("#signup-panel",".signup-panel,.button-sign-up,.mob-sign-up,.login-links-r a");
	panel_pop("#lost-password",".lost-password,.discy_for_all .reset-password");
	panel_pop("#lost-password",".lost-passwords","no",".discy_for_all");
	panel_pop("#login-panel",".login-panel,.button-sign-in,.mob-sign-in,.comment-reply-login");
	panel_pop("#wpqa-question",".wpqa-question");
	panel_pop("#wpqa-question-user",".ask-question-user");
	panel_pop("#wpqa-post",".wpqa-post");
	panel_pop("#wpqa-message",".wpqa-message,.message-reply a");
	panel_pop("#wpqa-report",".report_c,.report_q");
	
	function panel_pop(whatId,whatClass,whatFrom,bodyClass) {
		if (jQuery(whatId).length && !jQuery(whatClass).hasClass("wpqa-not-pop")) {
			jQuery((whatFrom == "no"?(bodyClass != ""?bodyClass+" ":"")+".wpqa_form,":"")+whatClass).on("click",(whatFrom == "no"?whatClass:""),function() {
				if (jQuery(whatClass).parent().hasClass("message-reply")) {
					var user_id    = jQuery(this).attr("data-user-id");
					var message_id = jQuery(this).attr("data-id");
					if (message_id !== undefined && message_id !== false) {
						jQuery.ajax({
							url: wpqa_js.admin_url,
							type: "POST",
							data: { action : 'wpqa_message_reply',message_id : message_id },
							success:function(data) {
								jQuery("#wpqa-message .the-title").val(data);
							}
						});
					}
					if (user_id !== undefined && user_id !== false) {
						if (jQuery(".message_user_id").length) {
							jQuery(".message_user_id").attr("value",user_id);
						}else {
							jQuery("#wpqa-message .send-message").after('<input type="hidden" name="user_id" class="message_user_id" value="'+user_id+'">');
						}
					}
				}
				
				var data_width = jQuery(whatId).attr("data-width");
				jQuery(".panel-pop").css({"top":"-100%","display":"none"});
				jQuery(".wrap-pop").remove();
				var is_RTL = jQuery('body').hasClass('rtl')?true:false;
				var cssMargin = (is_RTL == true?"margin-right":"margin-left");
				var cssValue = "-"+(data_width !== undefined && data_width !== false?data_width/2:"")+"px";
				jQuery(whatId).css("width",(data_width !== undefined && data_width !== false?data_width:"")+"px").css(cssMargin,cssValue).show().animate({"top":"7%"},200);
				jQuery("html,body").animate({scrollTop:0},200);
				jQuery("body").prepend("<div class='wrap-pop'></div>");
				wrap_pop();
				return false;
			});
		}
	}
	
	function wrap_pop() {
		jQuery(".wrap-pop").on("click",function () {
			jQuery.when(jQuery(".panel-pop").fadeOut(200)).done(function() {
				jQuery(this).css({"top":"-100%","display":"none"});
				jQuery("#wpqa-message .the-title").val("");
				jQuery(".wrap-pop").remove();
			});
		});
	}

	jQuery('body').bind('click', function(e) {
		var click_area = jQuery(".user-login-click,.user-notifications,.mobile-aside");
		if (!jQuery(e.target).closest(click_area).length) {
			/* User login */
			jQuery(".user-login-click").removeClass("user-click-open").find(" > ul").slideUp(200);
			
			/* User notifications */
			jQuery(".user-login-area .user-notifications > div").slideUp(200);
			jQuery(".user-notifications-seen").removeClass("user-notifications-seen");
			
			/* User messages */
			jQuery(".user-messages > div").slideUp(200);
			
			/* Mobile aside */
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
		}
	});
	
	/* Show answer */
	
	if (jQuery(".show-answer-form").length) {
		jQuery(".show-answer-form").on("click",function() {
			jQuery(".show-answer-form").hide(10);
			jQuery(".comment-form-hide").animate({opacity: 'show' , height: 'show'}, 400);
			jQuery(".discy-main-wrap,.fixed-sidebar,.fixed_nav_menu").css({"height":"auto"});
		});
	}
	
	/* Profile setting */
	
	if (jQuery(".profile-setting").length) {
		jQuery(".profile-setting a").on("click",function () {
			var profile = jQuery(this);
			if (!profile.hasClass("active-tab")) {
				jQuery(".profile-setting a").removeClass("active-tab");
				jQuery("#edit-profile,#change-password").slideUp(10);
				jQuery(profile.attr("href")).slideDown(200);
				jQuery("#profile_type").attr("value",profile.attr("data-type"));
				profile.addClass("active-tab");
			}
			return false;
		});
	}
	
	/* Categories */
	
	if (jQuery(".home_categories").length) {
		jQuery(".home_categories").on("change",function () {
			var url = jQuery(this).val();
			if (url) {
				window.location = url;
			}
			return false;
		});
	}
	
	/* Message */
	
	if (jQuery(".message-delete a").length) {
		jQuery(".message-delete a").live("click",function () {
			if (confirm(wpqa_js.sure_delete_message)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery(".view-message").length) {
		jQuery(".view-message").live("click",function () {
			var view_message    = jQuery(this);
			var message_id      = view_message.attr("data-id");
			var message_content = view_message.parent().parent().find(".message-content");
			view_message.find(".message-open-close").removeClass("icon-minus").addClass("icon-plus");
			if (view_message.hasClass("view-message-open")) {
				message_content.slideUp(300);
				view_message.removeClass("view-message-open");
			}else {
				if (message_content.find(" > div").length) {
					message_content.slideDown(300);
					view_message.addClass("view-message-open").find(".message-open-close").removeClass("icon-plus").addClass("icon-minus");
				}else {
					view_message.addClass("view-message-open").parent().parent().find(".small_loader").addClass("small_loader_display");
					jQuery.ajax({
						url: wpqa_js.admin_url,
						type: "POST",
						data: { action : 'wpqa_message_view',message_id : message_id },
						success:function(data) {
							view_message.parent().find(".message-new").removeClass("message-new");
							view_message.parent().parent().find(".small_loader").removeClass("small_loader_display");
							view_message.find(".message-open-close").removeClass("icon-plus").addClass("icon-minus");
							message_content.html(data).slideDown(300);
							view_message.find(".message-new").removeClass("message-new");
						}
					});
				}
			}
			return false;
		});
	}
	
	if (jQuery(".block_message").length) {
		jQuery(".block_message").live("click",function () {
			var block_message = jQuery(this);
			var user_id       = block_message.attr("data-id");
			jQuery(".block_message_"+user_id).hide();
			jQuery.ajax({
				url: wpqa_js.admin_url,
				type: "POST",
				data: { action : (block_message.hasClass("unblock_message")?'wpqa_unblock_message':'wpqa_block_message'),user_id : user_id },
				success:function(data) {
					if (block_message.hasClass("unblock_message")) {
						jQuery(".block_message_"+user_id).removeClass("unblock_message").text(wpqa_js.block_message_text).show();
					}else {
						jQuery(".block_message_"+user_id).addClass("unblock_message").text(wpqa_js.unblock_message_text).show();
					}
				}
			});
			return false;
		});
	}
	
	/* Single question */
	
	if (jQuery(".post-delete,.question-delete").length) {
		jQuery(".post-delete,.question-delete").on("click",function () {
			var var_delete = (jQuery(".post-delete").length?wpqa_js.sure_delete_post:wpqa_js.sure_delete);
			if (confirm(var_delete)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery(".question-follow a").length) {
		jQuery(".question-follow a").on("click",function () {
			var question_follow = jQuery(this);
			var question_class = question_follow.closest(".article-question.article-post.question");
			var post_id = question_class.attr('id').replace("post-","");
			question_follow.hide();
			question_follow.parent().find(".loader_2").show();
			jQuery.ajax({
				url: wpqa_js.admin_url,
				type: "POST",
				data: { action : 'wpqa_question_'+(question_follow.hasClass("unfollow-question")?"unfollow":"follow"), post_id : post_id },
				success:function(data) {
					if (question_follow.hasClass("unfollow-question")) {
						question_follow.removeClass("unfollow-question").parent().removeClass("li-follow-question").find("i").addClass("icon-plus").removeClass("icon-minus");
					}else {
						question_follow.addClass("unfollow-question").parent().addClass("li-follow-question").find("i").removeClass("icon-plus").addClass("icon-minus");
					}
					question_follow.attr("original-title",(question_follow.hasClass("unfollow-question")?wpqa_js.follow_question_attr:wpqa_js.unfollow_question_attr)).show().parent().find(".loader_2").hide().parent().parent().find(".question-followers span").text(data);
				}
			});
			return false;
		});
	}
	
	question_stats("close");
	question_stats("open");
	
	function question_stats(stats) {
		if (jQuery(".question-"+stats).length) {
			jQuery(".question-"+stats).on("click",function () {
				var question_stats = jQuery(this);
				var question_class = question_stats.closest(".article-question.article-post.question");
				var post_id = question_class.attr('id').replace("post-","");
				var wpqa_open_close_nonce = question_stats.data("nonce");
				question_stats.hide();
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: { action : 'wpqa_question_'+stats, wpqa_open_close_nonce : wpqa_open_close_nonce, post_id : post_id },
					success:function(data) {
						location.reload();
					}
				});
				return false;
			});
		}
	}
	
	if (jQuery(".wpqa-open-click").length) {
		jQuery(".wpqa-open-click").click(function () {
			var whatsclass = jQuery(this).attr("data-class");
			if (whatsclass !== undefined && whatsclass !== false) {
				jQuery("."+whatsclass).addClass("wpqa-open-new");
			}else {
				jQuery(this).parent().find(".wpqa-open-div").addClass("wpqa-open-new");
			}
			jQuery(".wpqa-open-div:not(.wpqa-open-new)").slideUp(400);
			if (whatsclass !== undefined && whatsclass !== false) {
				jQuery("."+whatsclass).slideToggle(400);
			}else {
				jQuery(this).parent().find(".wpqa-open-div").slideToggle(400);
			}
			jQuery(".wpqa-open-new").removeClass("wpqa-open-new");
			return false;
		});
	}
	
	function vote_message(vote) {
		vote.find(".vote_result").show();
		vote.find(".li_loader").hide();
	}
	
	jQuery(".wpqa_vote").live("click",function() {
		var this_vote = jQuery(this);
		var type = this_vote.attr("data-type");
		var vote_type = this_vote.attr("data-vote-type");
		this_vote.parent().parent().addClass("active-vote");
		if (type == "question") {
			var vote_parent = this_vote.parent().parent().parent().parent();
		}else {
			var vote_parent = this_vote.parent().parent();
		}
		vote_parent.find(".vote_result").hide();
		vote_parent.find(".li_loader").show();
		vote_parent.parent().parent().find(".wpqa_error").slideUp(200);
		if (this_vote.hasClass("vote_not_user")) {
			vote_parent.parent().parent().find(".wpqa_error").text(wpqa_js.no_vote_user).slideDown(200);
			vote_message(vote_parent);
			this_vote.parent().parent().removeClass("active-vote");
		}else if (this_vote.hasClass("vote_not_allow")) {
			vote_parent.parent().parent().find(".wpqa_error").text((type == "question"?wpqa_js.no_vote_question:wpqa_js.no_vote_answer)).slideDown(200);
			vote_message(vote_parent);
			this_vote.parent().parent().removeClass("active-vote");
		}else if (this_vote.hasClass("vote_allow")) {
			var id = this_vote.attr('id').replace(type+'_vote_'+vote_type+'-',"");
			jQuery.ajax({
				url: wpqa_js.admin_url,
				type: "POST",
				data: { action : 'wpqa_'+(type == "comment"?"comment":"question")+'_vote_'+vote_type, id : id },
				success:function(data) {
					if (Math.floor(data) == data && jQuery.isNumeric(data)) {
						if (data > 0) {
							vote_parent.find(".vote_result").removeClass("vote_red");
						}else if (data == 0) {
							vote_parent.find(".vote_result").removeClass("vote_red");
						}else if (data < 0) {
							vote_parent.find(".vote_result").addClass("vote_red");
						}
					}else {
						data = data.replace("no_vote_more","");
						vote_parent.parent().parent().find(".wpqa_error").text((type == "question"?wpqa_js.no_vote_more:wpqa_js.no_vote_more_answer)).slideDown(200);
					}
					vote_parent.find(".vote_result").html(data);
					vote_message(vote_parent);
					this_vote.parent().parent().removeClass("active-vote");
				}
			});
		}
		return false;
	});
	
	wpqa_report();
	
	function wpqa_report() {
		if (jQuery(".report_activated").length) {
			var report_type = "";
			jQuery(".report_activated > a").on("click",function() {
				report_type = jQuery(this).attr("class");
				if (jQuery(".report_id").length) {
					jQuery(".report_id").remove();
				}
				
				if (report_type == "report_c") {
					var report_v = jQuery(this);
					var report_id = report_v.attr("href");
					jQuery(".submit-report").append('<input type="hidden" class="report_id" name="report_id" value="'+report_id+'">');
				}
				return false;
			});
				
			jQuery(".submit-report").submit(function () {
				var report_v = jQuery(this);
				var explain = report_v.find("textarea");
				if (explain.val() == '') {
					explain.css("border-color","#e1e2e3");
					if (explain.val() == '') {
						explain.css("border-color","#F00");
					}
					jQuery(".wpqa_error",report_v).html('<span class="required-error">'+wpqa_js.wpqa_error_text+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
					jQuery('.load_span',report_v).hide();
					jQuery('input[type="submit"]',report_v).show();
				}else {
					var fromSerialize = report_v.serialize();
					var fromWithAction = fromSerialize+"&action=wpqa_"+report_type;
					jQuery.post(wpqa_js.admin_url,fromWithAction,function(data) {
						if (data == "deleted_report") {
							if (report_type == "report_c") {
								location.reload();
							}else {
								window.location.href = wpqa_js.home_url;
							}
						}else {
							explain.val("").css("border-color","#e1e2e3");
							jQuery(".wpqa_success",report_v).text(wpqa_js.reported).animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
						}
						jQuery('.load_span',report_v).hide();
						jQuery('input[type="submit"]',report_v).show();
					});
				}
				return false;
			});
		}
	}
	
	jQuery(".poll_results").live("click",function() {
		var poll_area = jQuery(this).closest(".poll-area");
		poll_area.find(".poll_2").fadeOut(200);
		poll_area.find(".poll_1").delay(500).slideDown(200);

		poll_area.find(".progressbar-percent").each(function(){
			var $this = jQuery(this);
			var percent = $this.attr("attr-percent");
			$this.bind("inview", function(event, isInView, visiblePartX, visiblePartY) {
				if (isInView) {
					$this.animate({ "width" : percent + "%"}, 700);
				}
			});
		});
		return false;
	});
	
	jQuery(".poll_polls").live("click",function() {
		var poll_area = jQuery(this).closest(".poll-area");
		poll_area.find(".poll_1").fadeOut(200);
		poll_area.find(".poll_2").delay(500).slideDown(200);
		return false;
	});

	jQuery(".wpqa_poll_image img").live("click",function() {
		var wpqa_poll_image = jQuery(this);
		wpqa_poll_image.parent().parent().parent().find('input[type="radio"]').removeAttr('checked');
		wpqa_poll_image.parent().parent().parent().find('.wpqa_poll_image_select').removeClass('wpqa_poll_image_select');
		wpqa_poll_image.addClass('wpqa_poll_image_select').prev('input[type="radio"]').click().attr('checked','checked');
	});
	
	jQuery(".poll-submit").live("click",function() {
		var question_poll = jQuery(this);
		var poll_val = question_poll.parent().find('.required-item:checked');
		jQuery(question_poll).parent().find("input,label").hide().parent().find(".load_span").show();
		if (poll_val.length == 0) {
			jQuery(question_poll).parent().find("input,label").show().parent().find(".load_span").hide();
			question_poll.parent().parent().parent().parent().parent().parent().find(".wpqa_error").text(wpqa_js.wpqa_error_text).slideDown(200).delay(3000).slideUp(200);
		}else {
			var poll_id        = poll_val.val().replace("poll_","");
			var poll_title     = (wpqa_js.poll_image_title == 'on'?poll_val.attr("data-rel").replace("poll_",""):"");
			var question_class = question_poll.closest(".article-question.article-post.question");
			var post_id        = question_class.attr("id").replace("post-","");
			
			jQuery.ajax({
				url: wpqa_js.admin_url,
				type: "POST",
				data: { action : 'wpqa_question_poll', poll_id : poll_id, poll_title : poll_title, post_id : post_id },
				success:function(data) {
					if (data == "no_poll") {
						question_poll.parent().parent().parent().parent().parent().parent().find(".wpqa_error").text(wpqa_js.no_poll_more).slideDown(200).delay(3000).slideUp(200);
						jQuery(question_poll).parent().find("input,label").show().parent().find(".load_span").hide();
					}
					location.reload();
				}
			});
		}
		return false;
	});

	if (jQuery(".ask_anonymously").length) {
		jQuery(".ask_anonymously").each(function () {
			var ask_anonymously = jQuery(this);
			var wpqa_setting = ask_anonymously.is(":checked");
			if (wpqa_setting == 1) {
				ask_anonymously.closest(".ask_anonymously_p").find(".ask_named").hide(10);
				ask_anonymously.closest(".ask_anonymously_p").find(".ask_none").show(10);
			}else {
				ask_anonymously.closest(".ask_anonymously_p").find(".ask_named").show(10);
				ask_anonymously.closest(".ask_anonymously_p").find(".ask_none").hide(10);
			}
			
			ask_anonymously.on("click",function () {
				var ask_anonymously_c = ask_anonymously.is(":checked");
				if (ask_anonymously_c == 1) {
					ask_anonymously.closest(".ask_anonymously_p").find(".ask_named").hide(10);
					ask_anonymously.closest(".ask_anonymously_p").find(".ask_none").show(10);
				}else {
					ask_anonymously.closest(".ask_anonymously_p").find(".ask_named").show(10);
					ask_anonymously.closest(".ask_anonymously_p").find(".ask_none").hide(10);
				}
			});
		});
	}
	
	wpqa_favorite("add_favorite");
	wpqa_favorite("remove_favorite");
	
	function wpqa_favorite(favorite_type) {
		if (jQuery("."+favorite_type).length) {
			jQuery("."+favorite_type).on("click",function () {
				var var_favorite = jQuery(this);
				var question_class = var_favorite.closest(".article-question.article-post.question");
				var post_id = question_class.attr("id").replace('post-',"");
				var_favorite.hide();
				var_favorite.parent().find(".loader_2").show();
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: { action : 'wpqa_'+var_favorite.attr("class"), post_id : post_id },
					success:function(data) {
						var_favorite.find("span").text(data);
						if (var_favorite.parent().hasClass("active-favorite")) {
							var_favorite.addClass("add_favorite").removeClass("remove_favorite").attr("title",wpqa_js.add_favorite).parent().removeClass("active-favorite");
						}else {
							var_favorite.addClass("remove_favorite").removeClass("add_favorite").attr("title",wpqa_js.remove_favorite).parent().addClass("active-favorite");
						}
						var_favorite.show();
						var_favorite.parent().find(".loader_2").hide();
					}
				});
				return false;
			});
		}
	}
	
	/* Progress Bar */
	
	jQuery(".progressbar-percent").each(function(){
		var $this = jQuery(this);
		var percent = $this.attr("attr-percent");
		$this.bind("inview", function(event, isInView, visiblePartX, visiblePartY) {
			if (isInView) {
				$this.animate({ "width" : percent + "%"}, 700);
			}
		});
	});
	
	/* Categories Accordion */
	
	if (jQuery(".categories-toggle-accordion").length) {
		jQuery(".categories-toggle-accordion .accordion-title").each(function () {
			jQuery(this).find(" > a > i").click(function () {
				var categories = jQuery(this);
				categories.toggleClass("wpqa-minus");
				categories.parent().parent().next().slideToggle(300);
				return false;
			});
		});
	}
	
	/* Follow users */
	
	if (jQuery(".following_not,.following_you").length) {
		wpqa_follow("following_not","following_you");
		wpqa_follow("following_you","following_not");
		
		function wpqa_follow(follow,next_follow) {
			jQuery("."+follow).live("click",function () {
				var following_var = jQuery(this);
				var following_var_id = following_var.attr("data-rel");
				var user_follow_done = (following_var.parent().hasClass("user_follow_4")?"user_follow_done":"user_follow_yes");
				following_var.hide();
				following_var.parent().addClass("user_follow_active");
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: {action:'wpqa_'+follow,following_var_id:following_var_id},
					success:function(result) {
						if (following_var.parent().hasClass("user_follow_2") || following_var.parent().hasClass("user_follow_3") || following_var.parent().hasClass("user_follow_4")) {
							if (following_var.find(".follow-count").length) {
								following_var.find(".follow-count").text(result);
							}
							if (following_var.closest(".wpqa-profile-cover").find(".follow-cover-count").length) {
								following_var.closest(".wpqa-profile-cover").find(".follow-cover-count").text(result);
							}
							following_var.addClass(next_follow).removeClass(follow).attr("title",(follow == "following_not"?wpqa_js.follow_question:wpqa_js.unfollow_question)).show().parent().removeClass("user_follow_active");
							if (follow == "following_not") {
								following_var.parent().removeClass(user_follow_done).find(".follow-value").text((follow == "following_not"?wpqa_js.follow_question:wpqa_js.unfollow_question));
							}else {
								following_var.parent().addClass(user_follow_done).find(".follow-value").text((follow == "following_not"?wpqa_js.follow_question:wpqa_js.unfollow_question));
							}
						}else {
							following_var.addClass(next_follow).removeClass(follow).attr("title",(follow == "following_not"?wpqa_js.follow_question:wpqa_js.unfollow_question)).show().parent().removeClass("user_follow_active");
							if (follow == "following_not") {
								following_var.parent().removeClass(user_follow_done).find("i").removeClass("icon-minus").addClass("icon-plus");
							}else {
								following_var.parent().addClass(user_follow_done).find("i").removeClass("icon-plus").addClass("icon-minus");
							}
						}
					}
				});
				return false;
			});
		}
	}
	
	/* Add Point */
	
	if (jQuery(".bump-question-area a").length) {
		jQuery(".bump-question-area a").on("click",function () {
			var point_a = jQuery(this);
			var input_add = jQuery("#input-add-point");
			var input_add_point = input_add.val();
			var question_class = point_a.closest(".article-question.article-post.question");
			var question_content = point_a.closest(".question-content");
			var post_id = question_class.attr("id").replace('post-',"");
			point_a.hide();
			point_a.parent().find(".load_span").show();
			jQuery.ajax({
				url: wpqa_js.admin_url,
				type: "POST",
				data: {action:'wpqa_add_point',input_add_point:input_add_point,post_id:post_id},
				success:function(data) {
					question_content.find(".wpqa_error").hide(10).text(data).slideDown(200).delay(3000).slideUp(200);
					point_a.show();
					point_a.parent().find(".load_span").hide();
					input_add.val("");
				}
			});
			return false;
		});
	}
	
	/* Login & Password & Signup */
	
	wpqa_forms(".login-form","login");
	wpqa_forms(".wpqa-lost-password","password");
	wpqa_forms(".signup_form","signup");
	
	function wpqa_forms(whatClass,whatAction) {
		if (jQuery(whatClass).length) {
			jQuery(whatClass).submit(function() {
				var thisform = jQuery(this);
				jQuery('input[type="submit"]',thisform).hide();
				jQuery('.load_span',thisform).show().css({"display":"block"});
				jQuery('.required-item',thisform).each(function () {
					var required = jQuery(this);
					required.css("border-color","#e1e2e3");
					if (required.val() == '' && required.attr("type") != "file") {
						required.css("border-color","#F00");
						return false;
					}
				});
				
				if (jQuery('.wpqa_captcha',thisform).length) {
					var wpqa_captcha = jQuery('.wpqa_captcha',thisform);
					var url = wpqa_js.wpqa_dir+"captcha/captcha.php";
					var postStr = wpqa_captcha.attr("name")+"="+encodeURIComponent(wpqa_captcha.val());
					
					wpqa_captcha.css("border-color","#e1e2e3");
					
					if (wpqa_captcha.val() == "") {
						jQuery(".wpqa_error",thisform).html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_text+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
						wpqa_captcha.css("border-color","#F00");
						jQuery('.load_span',thisform).hide().css({"display":"none"});
						jQuery('input[type="submit"]',thisform).show();
						return false;
					}else if (wpqa_captcha.hasClass("captcha_answer")) {
						if (wpqa_captcha.val() != wpqa_js.captcha_answer) {
							jQuery(".wpqa_error",thisform).html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
							wpqa_captcha.css("border-color","#F00");
							jQuery('.load_span',thisform).hide().css({"display":"none"});
							jQuery('input[type="submit"]',thisform).show();
							return false;
						}
					}else {
						var message = "";
						jQuery.ajax({
							url:   url,
							type:  "POST",
							data:  postStr,
							async: false,
							success: function(data) {
								message = data;
							}
						});
						if (message == "wpqa_captcha_0") {
							jQuery(".wpqa_error",thisform).html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
							wpqa_captcha.css("border-color","#F00");
							jQuery('.load_span',thisform).hide().css({"display":"none"});
							jQuery('input[type="submit"]',thisform).show();
							return false;
						}
					}
				}
				
				var fromSerialize = thisform.serialize();
				var fromWithAction = fromSerialize+"&action=wpqa_ajax_"+whatAction+"_process";
				jQuery.post(wpqa_js.admin_url,fromWithAction,function(response) {
					var result = jQuery.parseJSON(response);
					if (result.success == 1) {
						if (whatAction == "password") {
							jQuery('input[type="email"]',thisform).val("");
							jQuery(".wpqa_success",thisform).html(result.done).animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
						}else {
							window.location = result.redirect;
						}
					}else if (result.error) {
						jQuery(".wpqa_error",thisform).html('<span class="required-error">'+result.error+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
					}else {
						return true;
					}
					jQuery('.load_span',thisform).hide().css({"display":"none"});
					jQuery('input[type="submit"]',thisform).show();
				});
				return false;
			});
		}
	}
	
	/* Notifications */
	
	if (jQuery(".notifications-click").length) {
		jQuery(".notifications-click").on("click",function () {
			if (!jQuery(this).hasClass("messages-click")) {
				jQuery(".user-messages").removeClass("user-notifications-seen").find(" > div").slideUp(200);
				jQuery(".user-login-click").removeClass("user-click-open").find(" > ul").slideUp(200);
				jQuery(this).parent().toggleClass("user-notifications-seen").find(" > div").slideToggle(200).parent().find(" > .notifications-number").remove();
				jQuery.post(wpqa_js.admin_url,{action:"wpqa_update_notifications"});
			}
		});
	}
	
	/* Messages */
	
	if (jQuery(".messages-click").length) {
		jQuery(".messages-click").on("click",function () {
			jQuery(".notifications-area").removeClass("user-notifications-seen").find(" > div").slideUp(200);
			jQuery(".user-login-click").removeClass("user-click-open").find(" > ul").slideUp(200);
			jQuery(this).parent().toggleClass("user-notifications-seen").find(" > div").slideToggle(200).parent().find(" > .notifications-number").remove();
		});
	}
	
	/* Datepicker */
	
	if (jQuery(".age-datepicker").length) {
		jQuery(".age-datepicker").datepicker({changeMonth:true,changeYear:true,yearRange:"-90:+00",dateFormat:"yy-mm-dd"});
	}

	if (jQuery(".date-datepicker").length) {
		jQuery(".date-datepicker").datepicker({changeMonth:true,dateFormat:"yy-mm-dd"});
	}
	
	/* Comments & Answers */
	
	if (jQuery("#commentform").length) {
		jQuery("#commentform").attr((wpqa_js.attachment_answer == "on" || wpqa_js.featured_image_answer == "on"?"enctype":"data-empty"),(wpqa_js.attachment_answer == "on" || wpqa_js.featured_image_answer == "on"?"multipart/form-data":"none")).submit(function () {
			var thisform = jQuery(this);
			jQuery('.required-error',thisform).remove();
			if (jQuery('.wpqa_captcha',thisform).length) {
				var wpqa_captcha = jQuery('.wpqa_captcha',thisform).parent().find("input");
				var url = wpqa_js.wpqa_dir+"captcha/captcha.php";
				var postStr = wpqa_captcha.attr("name")+"="+encodeURIComponent(wpqa_captcha.val());
				wpqa_captcha.css("border-color","#e1e2e3");
				if (wpqa_captcha.val() == "") {
					wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
					jQuery("#commentform .load_span").hide();
					jQuery("#commentform .button-hide-click").show();
					return false;
				}else if (wpqa_captcha.hasClass("captcha_answer")) {
					if (wpqa_captcha.val() != wpqa_js.captcha_answer) {
						wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery("#commentform .load_span").hide();
						jQuery("#commentform .button-hide-click").show();
						return false;
					}else {
						return true;
					}
				}else {
					var message = "";
					jQuery.ajax({
						url   : url,
						type  : "POST",
						data  : postStr,
						async : false,
						success: function(data){
							message = data;
						}
					});
					if (message == "wpqa_captcha_0") {
						wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_js.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(3000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery("#commentform .load_span").hide();
						jQuery("#commentform .button-hide-click").show();
						return false;
					}else {
						return true;
					}
				}
			}
		});
	}
	
	if (jQuery("li.comment").length) {
		wpqa_best_answer("best_answer_re");
		wpqa_best_answer("best_answer_a");
		
		function wpqa_best_answer(type) {
			jQuery("li.comment").on("click","."+type,function () {
				jQuery("#comments .wpqa_error").slideUp(200);
				var best_answer = jQuery(this);
				var comment_id = best_answer.closest("li.comment").attr('id').replace("li-comment-","");
				var nonce = best_answer.data("nonce");
				jQuery("."+type).hide();
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: { action : 'wpqa_'+type, comment_id : comment_id, wpqa_best_answer_nonce : nonce },
					success:function(result) {
						if (result == "best") {
							if (type == "best_answer_a") {
								jQuery("#comment-"+comment_id).addClass(".comment-best-answer");
								jQuery("#comment-"+comment_id+" .comment-meta").before('<div class="best-answer">'+wpqa_js.best_answer+'</div>');
								jQuery("#comment-"+comment_id+" .comment-reply-main .last-item-answers").before('<li><a class="best_answer_re" data-nonce="'+wpqa_js.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_js.cancel_best_answer+'"><i class="icon-cancel"></i>'+wpqa_js.cancel_best_answer+'</a></li>');
							}else {
								jQuery(".commentlist .comment-reply-main .last-item-answers").before('<li><a class="best_answer_a" data-nonce="'+wpqa_js.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_js.choose_best_answer+'"><i class="icon-check"></i>'+wpqa_js.choose_best_answer+'</a></li>');
								jQuery(".best-answer").remove();
								jQuery(".comment-best-answer").removeClass("comment-best-answer");
							}
						}else if (result == "remove_best") {
							jQuery(".commentlist .comment-reply-main .last-item-answers").before('<li><a class="best_answer_a" data-nonce="'+wpqa_js.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_js.choose_best_answer+'"><i class="icon-check"></i>'+wpqa_js.choose_best_answer+'</a></li>');
							jQuery(".best-answer").remove();
							jQuery(".comment-best-answer").removeClass("comment-best-answer");
						}else {
							jQuery("#comment-"+result).addClass(".comment-best-answer").find(".wpqa_error").text(wpqa_js.best_answer_selected).slideDown(200);
							jQuery("#comment-"+result+" .comment-meta").before('<div class="best-answer">'+wpqa_js.best_answer+'</div>');
							jQuery("#comment-"+result+" .comment-reply-main .last-item-answers").before('<li><a class="best_answer_re" data-nonce="'+wpqa_js.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_js.cancel_best_answer+'"><i class="icon-cancel"></i>'+wpqa_js.cancel_best_answer+'</a></li>');
							jQuery("html,body").animate({scrollTop: jQuery("#comment-"+result).offset().top-35},"slow");
						}
						jQuery("."+type).parent().remove();
					}
				});
				return false;
			});
		}
	}
	
	if (jQuery(".single-question .comment-best-answer").length) {
		jQuery(".comment-best-answer").prependTo("ol.commentlist");
		jQuery(".comment-best-answer").hide;
	}
	
	if (jQuery("#respond").length) {
		if (window.location.hash == "#respond") {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show();
		}
		
		jQuery(".meta-answer").live("click",function () {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show();
			jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
		});
		
		jQuery(".single .wpqa-reply-link").live("click",function () {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show();
			var reply_link = jQuery(this);
			jQuery(".wpqa-cancel-link").remove();
			jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
			jQuery("#respond #comment_parent").val(reply_link.attr("data-id"));
			jQuery("#respond .section-title").append('<div class="wpqa-cancel-link cancel-comment-reply"><a rel="nofollow" id="cancel-comment-reply-link" href="#respond">'+wpqa_js.cancel_reply+'</a></div>');
			return false;
		});
		
		jQuery(".wpqa-cancel-link a").live("click",function () {
			jQuery(".wpqa-cancel-link").remove();
			jQuery("#respond #comment_parent").val(0);
			return false;
		});
		
		var check_email = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		jQuery("#respond #submit").live("click",function () {
			if (wpqa_js.comment_editor == "on") {
				if (jQuery("#respond .tmce-active").length) {
					var comment_text = jQuery("#respond #comment_ifr").contents().find("body").html();
				}else {
					var comment_text = jQuery("#respond .wp-editor-area").val();
				}
			}else {
				var comment_text = jQuery("#respond #comment").val();
			}

			var comment_name = (jQuery("#respond #comment_name").length?jQuery("#respond #comment_name").val():"not_empty");
			var comment_email = (jQuery("#respond #comment_email").length?jQuery("#respond #comment_email").val():"not_empty");
			
			if ((wpqa_js.require_name_email == 'require_name_email' && comment_email !=  'not_empty' && !check_email.test(comment_email)) || (wpqa_js.require_name_email == 'require_name_email' && (comment_name == '' || comment_email == '')) || comment_text == '' || comment_text == '<p><br data-mce-bogus="1"></p>' || comment_text == '<p><br></p>' || comment_text == '<p></p>') {
				if (wpqa_js.require_name_email == 'require_name_email') {
					if (comment_name == '') {
						var wpqa_error_text = wpqa_js.wpqa_error_name;
					}else if (comment_email == '') {
						var wpqa_error_text = wpqa_js.wpqa_error_email;
					}else if (comment_email !=  'not_empty' && !check_email.test(comment_email)) {
						var wpqa_error_text = wpqa_js.wpqa_valid_email;
					}else {
						var wpqa_error_text = wpqa_js.wpqa_error_comment;
					}
				}
				jQuery("#respond .wpqa_error").animate({opacity: 'hide' , height: 'hide'}, 400).remove();
				jQuery("#respond .comment-form").prepend('<div class="wpqa_error">'+wpqa_error_text+'</div>');
				jQuery("#respond .wpqa_error").animate({opacity: 'show' , height: 'show'}, 400);
				jQuery("#commentform .load_span").hide();
				jQuery("#commentform .button-hide-click").show();
				return false;
			}

			if (wpqa_js.comment_limit > 0 || wpqa_js.comment_min_limit > 0) {
				var message = "";
				jQuery.ajax({
					url: wpqa_js.admin_url,
					type: "POST",
					data: { action : 'wpqa_comment_limit', comment_text : comment_text, comment_limit : wpqa_js.comment_limit, comment_min_limit : wpqa_js.comment_min_limit },
					async : false,
					success: function(data){
						message = data;
					}
				});
				var wpqa_error_limit = wpqa_js.wpqa_error_limit;
				if (message == "wpqa_error" || message == "wpqa_min_error") {
					jQuery("#respond .wpqa_error").animate({opacity: 'hide' , height: 'hide'}, 400).remove();
					jQuery("#respond .comment-form").prepend('<div class="wpqa_error">'+(message == "wpqa_error"?wpqa_js.wpqa_error_limit+': '+wpqa_js.comment_limit:wpqa_js.wpqa_error_min_limit+': '+wpqa_js.comment_min_limit)+'</div>');
					jQuery("#respond .wpqa_error").animate({opacity: 'show' , height: 'show'}, 400);
					jQuery("#commentform .load_span").hide();
					jQuery("#commentform .button-hide-click").show();
					return false;
				}
			}
		});
	}
	
	if (jQuery(".delete-comment").length) {
		jQuery(".delete-comment").on("click",function () {
			var var_delete = (jQuery(".delete-answer").length?wpqa_js.sure_delete_answer:wpqa_js.sure_delete_comment);
			if (confirm(var_delete)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	/* Ask Question */
	
	if (jQuery(".add_media").length) {
		jQuery(".add_media").on("click",function (event) {
			event.preventDefault();
			wp.media.model.settings.post.id = 0;
		});
	}

	jQuery(window).load(function() {
		if (jQuery('.wp-editor-wrap').length) {
			jQuery('.wp-editor-wrap').each(function() {
				var editor_iframe = jQuery(this).find('iframe');
				if (editor_iframe.height() < 150) {
					editor_iframe.css({'height':'150px'});
				}
			});
		}
	});
	
	/* Close */
	
	jQuery(document).keyup(function(event) {
		if (event.which == '27') {
			/* Panel pop */
			
			jQuery.when(jQuery(".panel-pop").fadeOut(200)).done(function() {
				jQuery(this).css({"top":"-100%","display":"none"});
				jQuery(".wrap-pop").remove();
			});
			
			/* Mobile menu */
			
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			
			/* User login */
			
			jQuery(".user-login-click").removeClass("user-click-open").find(" > ul").slideUp(200);
			
			/* User notifications */
			
			jQuery(".user-login-area .user-notifications > div").slideUp(200);
			jQuery(".user-notifications-seen").removeClass("user-notifications-seen");
			
			/* User messages */
			
			jQuery(".user-messages > div").slideUp(200);
		}
	});
	
})(jQuery);

/* Captcha */

function wpqa_get_captcha(captcha_file,captcha_id) {
	jQuery("#"+captcha_id).attr("src",captcha_file+'&'+Math.random()).parent().find(".wpqa_captcha").val("");
}

/* Child categories */

function wpqa_child_cats(dropdown,result_div,level) {
	var cat         = dropdown.val();
	var results_div = result_div + level;
	var field_attr  = dropdown.attr('data-taxonomy');
	jQuery.ajax({
		type: 'post',
		url: wpqa_js.admin_url,
		data: {
			action: 'wpqa_child_cats',
			catID: cat,
			field_attr: field_attr
		},
		beforeSend: function() {
			dropdown.parent().parent().parent().next('.loader_2').addClass("category_loader_show");
			dropdown.parent().parent().parent().addClass("no-load");
		},
		complete: function() {
			dropdown.parent().parent().parent().next('.loader_2').removeClass("category_loader_show");
			dropdown.parent().parent().parent().removeClass("no-load");
		},
		success: function(html) {
			dropdown.parent().parent().nextAll().each(function() {
				jQuery(this).remove();
			});
			
			if (html != "") {
				dropdown.addClass('hasChild').parent().parent().parent().append('<span id="'+result_div+level+'" data-level="'+level+'"></span>');
				dropdown.parent().parent().parent().find('#'+results_div).html(html).slideDown('fast');
			}
		}
	});
}