// CALLBACK FUNCTION AFTER THE DOM HAS BEEN LOADED
jQuery('document').ready(function(){
	
	/* Lazy Loading of the List */
    jQuery.fn.ajax_loading = function() {
		
        return this.each(function() {

            var btn = jQuery(this);
			
			var ul = jQuery(btn.data('list'));
			
			
			
			
			ul.sanitize_url = function() {
                var url = ul.attr('data-url') ? ul.attr('data-url') : location.href;
                var hash_index = url.indexOf('#');
                if (hash_index > 0) {
                    url = url.substring(0, hash_index);
                }
				
                url = encodeURI(url);
                
				/* add page parameter to the request */
                var page = ul.page_inc();
				
                var paged_attr = btn.attr('data-paged-attr') ? btn.attr('data-paged-attr') : 'paged';

                url += (url.split('?')[1] ? '&' : '?') + paged_attr + '=' + page;
                return url;
            };

            ul.page_inc = function() {
                var page = ul.attr('data-page') ? parseInt(ul.attr('data-page')) : 1;
                page += 1;
                ul.attr('data-page', page);
                return page;
            };

            ul.append_children = function(result) {
				
                if (jQuery(result).find(ul.attr('data-target')).length) {
                    ul.attr('data-load-flag', '');
                    btn.html( btn.data('html') )
                } else {
                    btn.hide();
                }
				
				jQuery(result).find(ul.attr('data-target')).each(function() {
                    var list = jQuery(this);
                    list.hide();
                    list.appendTo(ul);
                    list.show('slow');
                    list.trigger('sjax:init', [list]);
                });
            };

            ul.ajax_load = function() {

                ul.attr('data-load-flag', 'ajax');

                var url = ul.sanitize_url();

               
                jQuery.ajax({
                    'url': url,
                    'success': function(result) {
                        //alert(result);
                        console.log('lazy loading from sjax');
                        ul.append_children(result);
                    },
                    'error': function() {
                        console.log('lazy loading from cache');
					}
                });

            };
			
            btn.click(function(ev) {
				
				btn.data('html', btn.html());
				
				btn.html('Loading ...');
				
                ul.ajax_load();
            });

			

        });
    };
    jQuery('body').find("[data-behaviour~=oq-ajax-loading]").ajax_loading();
	
	
	
	jQuery.fn.reload_html = function(){
        return this.each(function(){
			var el = jQuery(this);
            
            jQuery.ajax({
    			url : el.attr('data-url'),
        		success : function(result){
        			//console.log(result);
        			//console.log('reload html');
					el.html(result);
					el.find("[data-behaviour~=oq-ajax-loading]").ajax_loading();
            	},
        		error : function(){
        			el.hide();
        		}
        	});
			
			
			
			
		});
    };
    jQuery('body').find("[data-behaviour~=oq-reload-html]").reload_html();
	
});