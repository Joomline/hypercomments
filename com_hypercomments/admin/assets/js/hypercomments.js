function ShowLoadingScreen()
{
	jQuery("body").css("cursor", "wait");

	var fade_div = jQuery("#id_admin_forms_fade");

	if (fade_div.length == 0)
	{
		// Создаем div
		fade_div = jQuery('<div></div>')
			.appendTo(document.body)
			.hide()
			.attr('id', "id_admin_forms_fade")
			.attr('class', "shadowed")
			.css('z-index', "1500")
			.css('position', "absolute")
			.css('left', "50%")
			.css('top', "50%")
			.append('<img src="/administrator/components/com_hypercomments/assets/images/ajax_loader.gif" id="id_fade_div_img" />')
			.css('width', "32");
	}

	fade_div
		.show()
		.css('top', (jQuery(window).height() - fade_div.outerHeight(true)) / 2 + jQuery(window).scrollTop())
		.css('left', (jQuery(window).width() - fade_div.outerWidth(true)) / 2 + jQuery(window).scrollLeft());

	var overlay = jQuery("#overlay");

	if (overlay.length == 0)
	{
		jQuery('<div></div>')
			.appendTo(document.body)
			.hide()
			.attr('id', "overlay")
			.css('z-index', "1499")
			.show()
		;
	}
	else{
		overlay.show();
	}


}

// Скрытие экрана загрузки AJAX.
function HideLoadingScreen()
{
	jQuery("body").css("cursor", "auto");
	jQuery("#id_admin_forms_fade").hide();
	jQuery("#overlay").hide();
}




(function($){
	HCmanage = {
		// init app
		init: function(param){
			this.init_events();
			this.set_init_param(param);
			if(param.widget_id == '')
			{
				$('.e_hc_login').click();
			}
		},

		// set param app
		set_init_param: function(param){
			for(var prop in param){
				this[prop] = param[prop];
			}
		},

		// init events manage page
		init_events: function(){
			var $this = this;
			$('.e_hc_login').on('click', function ()
			{
				$this.login();
			});
		},

		// event login
		login: function(){
			this.popup(600, 450, 'Auth HyperComments', this.hc_url+'/auth?service=google', this.create_widget(this));
		},

		showMessage: function(type, msg){
			var divClass = (type == 'error') ? 'alert-error' : 'alert-success';
			if(typeof(hclang[msg]) != 'undefined'){
				msg = hclang[msg];
			}
			$('#system-message-container').html(
				'<button type="button" class="close" data-dismiss="alert">×</button>' +
				'<div class="alert '+divClass+'">' +
				'	<h4 class="alert-heading">Message</h4>' +
				'	<p>' + msg + '</p>' +
				'</div>'
			);
		},

		// create new widget
		create_widget: function(context){
			var date      = new Date();
            var time_zone = -date.getTimezoneOffset()/60;
			ShowLoadingScreen();
           
            $.getJSON(context.hc_url+'/api/widget_create?jsoncallback=?',
            {
                site       : context.hc_siteurl,
                title      : (context.hc_blogname.length > 0) ? context.hc_blogname : 'Joomla!',
                plugins    : "comments,rss,login,count_messages,authors,topics,hypercomments,likes,quotes",
                hypertext  : "*",
                limit      : 20,
                template   : "index",
                cluster    : "c1",
                platform   : "Joomla",
                notify_url : context.hc_notify_url,
                time_zone  : time_zone,
                hc_enableParams : true
            },
            function(data)
			{
                if(data.result == 'success')
				{
                    context.save_wid(data);
                }
				else
				{
					context.showMessage('error', data.description);
                }
				HideLoadingScreen();
            });
		},


		// save widget id
		save_wid: function(data){
			if(typeOf(data.wid) == undefined || parseInt(data.wid) == 0){
				this.showMessage('error', 'Widget ID undefined');
				HideLoadingScreen;
			}
			var wid = parseInt(data.wid);
			var secret_key = data.secret_key;

			$.ajax({
				url: this.hc_admin_url+'&task=comments.save_wwidget_id',
				data: {
					wid: wid,
					secret_key: secret_key
				},
				dataType: 'json'
			}).done(function(data) {
				if(data.error == 1)
				{
					HCmanage.showMessage('error', data.msg);
					HideLoadingScreen();
				}
				else
				{
					document.location.reload();
				}
			});
		},

		// show popup
		popup: function(width, height, name, url, callback){
			var x = (640 - width)/2;
			var y = (480 - height)/2;
			if(screen){
				y = (screen.availHeight - height)/2;
				x = (screen.availWidth - width)/2;
			}
			var w = window.open(url, name , "menubar=0,location=0,toolbar=0,directories=0,scrollbars=0,status=0,resizable=0,width=" + width + ",height=" + height + ',screenX='+x+',screenY='+y+',top='+y+',left='+x);
			w.focus();

			if(callback)
				var interval = setInterval(function(){
					if(!w || w.closed){
						clearInterval(interval);
						callback();
					}
				}, 500);
		}

	};
})(jQuery);

var files = {};

function uploadXMLfiles(fileUrl, widget_id, rootUrl)
{
	ShowLoadingScreen();


	jQuery('#articleList input[type=checkbox]:checked').each(function ()
		{
		var fileName = jQuery(this).val();
		var file = fileUrl + fileName;
		var xid = fileName.replace('.xml','');
		window.files[xid] = 1;
		var packet = {
			service: 'hypercomments',
			pageID: xid,
			widget_id: parseInt(widget_id),
			request_url: file,
			result_url: rootUrl+'?option=com_hypercomments&task=notify.delete_xml&xml='+fileName,
			result: 'success'
		};

		jQuery.getJSON(
			'http://www.hypercomments.com/api/import?response_type=callback&callback=?',
			packet,
			function(data){
				delete files[xid];
				var flen = ob_length(files);
				if(flen == 0)
				{
					window.HideLoadingScreen();
					window.printSystemMsg('message', 'Files sent to imort for HyperComments')
				}

				if(data.result == 'success')
				{
					jQuery('#articleList input[value="'+fileName+'"]').remove();
					jQuery.ajax({
						url: rootUrl+'?option=com_hypercomments&task=notify.changeXMLstatus',
						data: {
							xml: fileName
						},
						dataType: 'text'
					}).done(function(data) {});
				}
			});
	});
	//HideLoadingScreen();
}

function ob_length(obj)
{
	var count = 0;
	for(var prs in obj)
	{
		count++;
	}
	return count;
}

function printSystemMsg(type, msg){
	var divClass = (type == 'error') ? 'alert-error' : 'alert-success';
	jQuery('#system-message-container').html(
		'<button type="button" class="close" data-dismiss="alert">×</button>' +
		'<div class="alert '+divClass+'">'+
		'<h4 class="alert-heading">Messsage</h4>' +
		'<p>'+msg+'</p>' +
		'</div>'//+
        //
		//'<dl id="system-message">' +
		//'   <dd class="'+divClass+'">' +
		//'       <ul>' + msg + '</ul>' +
		//'   </dd>' +
		//'</dl>'
	);
}