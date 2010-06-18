Preceptor.widget.Editor = function( id , options ) 
{
	this.options    = options || {};
	this.width      = this.options.width || "99%";
	this.height     = this.options.height || "300px";
	this.addButton  = this.options.addButton || {};
    this.autoHeight = ( this.options.noAutoHeight ) ? false : true;
    
	var editor = new YAHOO.widget.Editor( id , this.configButton() ) , fn;
		
	this.configLabel( editor );
	
	for ( var i = 0; i < this.addButton.length; i++ ){
		fn = eval( "this."+ this.addButton[i] );
		fn.call( this , editor , id );	
	}
	
	editor.render(); 
	
	return editor;
}

Preceptor.widget.Editor.prototype = {
	
	configLabel: function( editor ){
		editor.STR_IMAGE_HERE       = "Insira aqui o endereço da imagem";
		editor.STR_IMAGE_PROP_TITLE = "Imagem";
		editor.STR_IMAGE_URL        = "URL";
		editor.STR_IMAGE_TITLE      = "Descrição";
		editor.STR_IMAGE_SIZE       = "Tamanho";
		editor.STR_IMAGE_PADDING    = "Espaçamento";
		editor.STR_IMAGE_BORDER     = "Borda";
		editor.STR_IMAGE_TEXTFLOW   = "Alinhamento";
		
		editor.STR_LINK_URL         = "Link";
		editor.STR_LINK_NEW_WINDOW  = "Abrir em nova janela";
		editor.STR_LINK_PROP_TITLE  = "Link";
		editor.STR_LINK_PROP_REMOVE = "Remover link";
		editor.STR_LINK_TITLE       = "Descriçao";
		
		editor.STR_SPIN_UP          = "&nbsp;";
		editor.STR_SPIN_DOWN        = "&nbsp;";
		editor.STR_SPIN_LABEL       = "&nbsp;";
		
	},
	
	configButton: function(){
		var conf =
	 	{ 
	 		collapse: true,
			height: this.height,
		    width: this.width,
		    autoHeight: this.autoHeight ,
		    animate: false,
		    dompath: false,
		    focusAtStart: true,
			toolbar: {
				buttons: [
				    { group: 'fontstyle', label: false,
				        buttons: [
				            { type: 'select', label: 'Arial', value: 'fontname', disabled: true,
				                menu: [
				                    { text: 'Arial', checked: true },
				                    { text: 'Arial Black' },
				                    { text: 'Comic Sans MS' },
				                    { text: 'Courier New' },
				                    { text: 'Lucida Console' },
				                    { text: 'Tahoma' },
				                    { text: 'Times New Roman' },
				                    { text: 'Trebuchet MS' },
				                    { text: 'Verdana' }
				                ]
				            },
				            { type: 'spin', label: '13', value: 'fontsize', range: [ 9, 75 ], disabled: true }
				        ]
				    },
				    { type: 'separator' },
				    { group: 'textstyle', label: false,
				        buttons: [
				            { type: 'push', label: 'Negrito CTRL + SHIFT + B', value: 'bold' },
				            { type: 'push', label: 'Itálico CTRL + SHIFT + I', value: 'italic' },
				            { type: 'push', label: 'Sublinhado CTRL + SHIFT + U', value: 'underline' },
				            { type: 'separator' },
				            { type: 'color', label: 'Cor da fonte', value: 'forecolor', disabled: true },
				            { type: 'color', label: 'Realçar', value: 'backcolor', disabled: true },
				            { type: 'separator' },
				            { type: 'push', label: 'Remove Formatação', value: 'removeformat', disabled: true }
				        ]
				    },
				    { type: 'separator' },
				    { group: 'undoredo', label: false,
                        buttons: [
                            { type: 'push', label: 'Desfazer', value: 'undo', disabled: true },
                            { type: 'push', label: 'Refazer', value: 'redo', disabled: true }
                        ]
                    },
				    { type: 'separator' },
				    { group: 'alignment', label: false,
				        buttons: [
				            { type: 'push', label: 'Alinha à esquerda CTRL + SHIFT + [', value: 'justifyleft' },
				            { type: 'push', label: 'Alinhar ao centro CTRL + SHIFT + |', value: 'justifycenter' },
				            { type: 'push', label: 'Alinhar à direita CTRL + SHIFT + ]', value: 'justifyright' },
				            { type: 'push', label: 'Alinhar justificado', value: 'justifyfull' }
				        ]
				    },
				    { type: 'separator' },
				    { group: 'indentlist', label: false,
				        buttons: [
	           				{ type: 'push', label: 'Diminuir recuo', value: 'outdent', disabled: true },
	           				{ type: 'push', label: 'Aumentar recuo', value: 'indent', disabled: true },
				            { type: 'push', label: 'Marcadores', value: 'insertunorderedlist' },
				            { type: 'push', label: 'Numeração', value: 'insertorderedlist' }
				        ]
				    },
				    { type: 'separator' },
				    { group: 'insertitem', label: false,
				        buttons: [
				            { type: 'push', label: 'Inserir Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
				            { type: 'push', label: 'Inserir Imagem', value: 'insertimage' }
				        ]
				    }
				]
			}
		}
		
		return conf;
	},
	
	uploadImage: function( editor )
	{
		editor.on('toolbarLoaded', function() { 
	        var imgConfig = {
	            type: 'push', 
	            label: 'Upload de imagem', 
	            value: 'uploadimage'
	        };
		      		
		    editor.toolbar.addButtonToGroup( imgConfig , 'insertitem' );
		
	       	editor.toolbar.on('uploadimageClick', function() {
	            runElementMeio( URL + '/file/folder/' , 'file-folder' );
	       	}, editor , true);
	   	});
	},
	
	insertHtml: function( editor , id ){
		editor.on('toolbarLoaded', function() { 
		   	var insertHtml = {
			   	type: 'push', 
			    label: 'Inserir html/embed', 
			   	value: 'inserthtml',
			   	onclick: { 
			   		fn: function(e){
						if ( !document.getElementById( 'iconMenu_' + id ) ){
							var div         = document.createElement( "DIV" ),
								textarea 	= document.createElement('TEXTAREA'),
								input 		= document.createElement('INPUT'), 
								top,left;

							textarea.id 	= 'text_' + id;
							textarea.style.width   = "177px";
							textarea.style.height  = "50px";

							input.type  = "button";
							input.value = "Inserir";
							input.id    = "insertembed_" + id;

							YAHOO.util.Event.on( 'insertembed_' + id , 'click', function(){
								var text = document.getElementById( "text_" + id ).value;

								if( text ){
									this.execCommand( 'inserthtml', text );
								}

								document.getElementById( "text_" + id ).value = "";

								Preceptor.widget.Editor.showHidden( 'iconMenu_' + id );
							}, editor , true );
							
							div.id 			     = 'iconMenu_' + id;
							div.className	     = 'pre_editor_inserthtml_table';
							div.style.position   = 'absolute';
							div.style.display    = 'none';
							div.style.background = 'white';
							div.style.border     = '1px solid black';
							div.style.padding    = '5px';

							div.appendChild( textarea );
							div.appendChild( document.createElement('BR') );
							div.appendChild( input );
					    
							document.body.appendChild(div);
						}
						
						top = YAHOO.util.Dom.getY( YAHOO.util.Event.getTarget( e ) ) + 20 + "px";
						left = YAHOO.util.Dom.getX( YAHOO.util.Event.getTarget( e ) ) + "px";
						
						document.getElementById( 'iconMenu_' + id ).style.left = left;
						document.getElementById( 'iconMenu_' + id ).style.top  = top;

						Preceptor.widget.Editor.showHidden( 'iconMenu_' + id );
					} 
				} 
	    	};
	    	
	    	editor.toolbar.addButtonToGroup( insertHtml , 'insertitem' );
	    });
	},
	
	createPaleta: function( editor , id ){
		
		editor.on('toolbarLoaded', function() {
			
			var browser = navigator.userAgent.toLowerCase() ,
				isIE = ((browser.indexOf( "msie" ) != -1) && (browser.indexOf( "opera" ) == -1) && (browser.indexOf( "webtv" ) == -1)),
				colors = [ "yellow"	   , "orange"	  , "red"		 , "pink"  , "#9FA0FF"  , "#5F7F5F"		 , "#00FFFF"			, "#6CBF6B"		   , "#00FF00"		    , "#AAA"   , "#FFF"             , "#D500FF" ] ,
				types  = [ "Pontuação" , "Ortografia" , "Acentuação" , "Crase" , "Regência" , "Concordância" , "Coesão e coerência" , "Rima/cacofonia" , "Clichê/repetição" , "Outros" , "Remover correção" , "Acordo ortográfico" ],
				msgs   = [ "Pontuação" , "Ortografia" , "Acentuação" , "Crase" , "Regência" , "Concordância" , "Coesão e coerência" , "Rima/cacofonia" , "Clichê/repetição" , "Outros" , "Remover correção" , "Acordo ortográfico" ],
				paleta = document.createElement("DIV") , 
				i , span , space , button , div , el;
			
			paleta.className = "paletas";
			paleta.id        = "paleta_id";
				
			for( i = 0; i < colors.length; i++ ){
				span 	= document.createElement("SPAN");	
				space 	= document.createElement("SPAN");
				button 	= document.createElement("A");
				div 	= document.createElement("DIV");
				
				div.className = "pre_editor_paleta";
				
				span.style.backgroundColor = colors[i];
				span.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;";
				space.innerHTML = "&nbsp;";
				
				button.href      = "#" + colors[i];
				button.title	 = msgs[i];
				button.cor	 	 = colors[i];
				button.innerHTML = types[i];
				
				YAHOO.util.Event.on( button , 'click' , function( ev ){
					el = YAHOO.util.Event.getTarget( ev );
					
					this.execCommand( (isIE)?("backcolor"):("hilitecolor") , el.cor );
				}, editor , true );
				
				
				div.appendChild( span );
				div.appendChild( space );
				div.appendChild( button );
				paleta.appendChild( div );
				paleta.appendChild( document.createElement("BR") );
			}
			
			insertAfter( paleta , document.getElementById('text_update') );
			
		});
	}
}

Preceptor.widget.Editor.showHidden = function()
{
	for ( var i = 0; i < arguments.length; i++ ) {
		$( "#" + arguments[i] ).toggle();
	}
}