/**
 * $(selector).navigation( json , options );
 */
(function ($) {

$.fn.navigation = function(contentJson , options)
{
	var content,current,
		defaults = {
			baseUrl: "/trails",
			url: "/",
			cache: true
		},
		context = this,
		options = $.extend({}, defaults, options),

		next = function(){
			if( content[(current+1)] ){
				current++;
				update();
			}
			return false;
		},

		previous = function(){
			if( content[current-1] ){
				current--;
				update();
			}
			return false;
		},

		createTreeview = function(){
			var i,j,html = '<ul class="first">',
				length = content.length,
				hierachLength = 0;

			for( i = 0; i < length ; i++ ){
				if( content[i+1] ){
					if( content[i+1].level > content[i].level ){
						html += '<li><img src="' + options.baseUrl + '/img/icons/minus.jpg" />&nbsp;';
						html += '<a href="#" id="content_' + i + '">';
						html += content[i].title;
						html += '</a><ul>';
					}else{
						html += '<li><a href="#" id="content_' + i + '">';
						html += content[i].title;
						html += '</a></li>';
					}

					if( content[i+1].level < content[i].level ){
						hierachLength = content[i].level - content[i+1].level;
						for( j = 0; j < hierachLength ; j++ ){
							html += '</ul></li>';
						}
					}
				}else{
					html += '<li><a href="#" id="content_' + i + '">' + content[i].title + '</a></li>';

                    if (content[i-1]) {
                        if (content[i-1].level > content[i].level) {
                            hierachLength = content[i-1].level - content[i].level;
                            for( j = 0; j < hierachLength ; j++ ){
                                html += '</ul></li>';
                            }
                        }
                    }
				}
			}

			html += '</ul>';

			$('.nav .bread .treeview',context).html(html);

			$('.nav .bread .treeview img',context).click(function(){
				var $img = $(this);

                $img.parent().children().eq(2).slideToggle('fast');
				
				if( $img.attr( 'src').indexOf('minus') > -1 ){
                    $img.attr( 'src' , $img.attr( 'src').replace( 'minus' , 'plus' ) );
                }else{
                    $img.attr( 'src' , $img.attr( 'src').replace( 'plus' , 'minus' ) );
                }
				return false;
			})
			.trigger('click')
			.css('cursor','pointer');

			$('.nav .bread .treeview',context).toggle();
			
			$('.nav .bread .treeview a',context).click(function(){
				current = parseInt( this.id.replace('content_','') );
				$('.nav .bread .treeview',context).hide('fast');
				update();
				return false;
			});
		},

		update = function(){
			var $cache = $('.content_text_'+current,context),
				$cacheDiv = $('.cache',context),
				$content = $('.content',context);
			
			if( $cache.length ){
				$content.html( $cache.html() );
			}else{
//				$content.load(
//					options.baseUrl + options.url + content[current].id,
//					null,
//					function(){
//						if( context.cache ){
//							$cacheDiv.append('<div class="content_text_'+current+'">'
//											+ $content.html() + '</div>' );
//						}
//					}
//				);
                new Preceptor.util.AjaxUpdate($content[0].id, options.baseUrl + options.url + content[current].id);
			}

			$.data( window ,'current_id'   , content[current].id );
			$.data( window ,'current_index', current );
			
			updateBreadCrumb();
		},

		updateBreadCrumb = function(){
			var $bread = $('.nav .bread span',context),
				itens = getParents(),
				item = null,
				i = 0,
				tmp = [];

			for( i=0;i<itens.length;i++ ){
				item = '<a href="#"  id="bread_content_' + itens[i].index + '">';
				item += itens[i].title + '</a>';
				tmp.push( item );
			}

			tmp.push( content[current].title );
			
			$bread.html( tmp.join( ' - ' ) );
			$bread.find('a').click(function(){
				current = parseInt( this.id.replace('bread_content_','') );
				update();
				return false;
			});
		},

		getParents = function(){
			var i,
				response = [],
				level = content[current].level;
			
			for( i = current-1; i > 0; i-- ){
				if( content[i] ){
					if( content[i].level < level ){
						content[i].index = i;
						response.push( content[i] );
						level--;
					}
				}
			}

			return response.reverse();
		};

	return this.each(function(){
		var $breadImg = $('.bread img',this),
			$buttons = $('.buttons a',this),
			$previousButton = $buttons.eq(0),
			$nextButton = $buttons.eq(1);
			
		content = YAHOO.lang.JSON.parse(contentJson) || ['No content'];
		current = options.current || 0;
		
		$nextButton.click( function(){
			next.apply(context);
			return false;
		});
		
		$previousButton.click( function(){
			previous.apply(context);
			return false;
		});

		$(this).append('<div class="cache" style="display: none">');

		update();
		createTreeview();

		$breadImg.click( function(){
			$('.nav .bread .treeview',context).slideToggle('fast');
			return false;
		}).css('cursor','pointer');
	});
}

})(jQuery);