var loadModules = [];

Lightbox = {
	create: function()
	{
		var height  = YAHOO.util.Dom.getDocumentHeight();
		var boxOverlay = document.createElement( "DIV" );
		
		boxOverlay.id = "overlay";
		boxOverlay.style.height =  height + "px";
		document.body.appendChild( boxOverlay );
		
		if( document.getElementById( "category-category" ))
			document.body.removeChild( document.getElementById( "category-category" ) );
		
		var lightbox = document.createElement( "DIV" );
		lightbox.id = "category-category";
		lightbox.className = "lightbox";
		lightbox.style.left = ( YAHOO.util.Dom.getClientWidth() / 2 - 267 ) + "px";
		document.body.appendChild( lightbox );
		
		dd = new YAHOO.util.DD( 'category-category' );
		dd.setHandleElId( 'drag' );
		
		return lightbox;
	},
	
	destroy: function( div , url )
	{
		document.body.removeChild( document.getElementById( "category-category" ) );
		while( document.getElementById( "overlay" ) )
		{
			try{
				document.body.removeChild( document.getElementById( "overlay" ) );
			}catch(e){}
		}
		
		new Preceptor.util.AjaxUpdate( div , URL + url );
	}
}

Use = {
	text: function( name , id , url )
	{
		var transform = function(){
			var input = document.createElement("input");
			input.value = Preceptor.util.String.trim( document.getElementById( id ).innerHTML );
			input.style.width = "30px";
			
			document.getElementById( id ).innerHTML = "";
			document.getElementById( id ).appendChild( input );
			
			input.focus();
			
			YAHOO.util.Event.on( input , "blur" , function(){
				save( this );
			});
			
			YAHOO.util.Event.on( input , "keypress" , function(ev){
				if ( YAHOO.util.Event.getCharCode(ev) == 13 ){
					save( this );
					YAHOO.util.Event.stopEvent(ev);
				}
			});
			
			YAHOO.util.Event.removeListener( id , "dblclick" );
		}
	
		var save = function( input ){
			if ( !input.value ){
				document.getElementById( id ).innerHTML = "";
			}else{
				document.getElementById( id ).innerHTML = input.value;
			}
			
			new Preceptor.util.AjaxSave( url , '&'+name+'=' + encodeURIComponent( input.value ) );
			
			YAHOO.util.Event.on( id , "dblclick" , function(){
				transform();
			});
		}
		
		YAHOO.util.Event.on( id , "mouseover" , function(){
			document.getElementById( id ).style.width = "100%";
			document.getElementById( id ).style.background = "#3676A6";
		});
		
		YAHOO.util.Event.on( id , "mouseout" , function(){
			document.getElementById( id ).style.background = "";
		});
		
		YAHOO.util.Event.on( id , "dblclick" , function(){
			transform();
		});
	}
}

function hideAll()
{
	for( var i = 1; i < loadModules.length; i++ )
	{
		try{
			document.getElementById( "main" + loadModules[i] ).style.display = 'none';
		}
		catch(e)
		{
			loadModules.splice( i , 1 );
		}
	}
}

function checkAll(o)
{
    var topNodes = o.getRoot().children;

    for(var i=0; i<topNodes.length; ++i)
    {
        topNodes[i].check();
    }
}

function uncheckAll(o)
{
    var topNodes = o.getRoot().children;

    for(var i=0; i<topNodes.length; ++i)
    {
        topNodes[i].uncheck();
    }
}

function showHidden()
{
// 	alert(arguments.length);
	for ( var i = 0; i < arguments.length; i++ )
	{
    	var sState = document.getElementById( arguments[i] ).style.display;
		if( sState == 'none' )
			document.getElementById( arguments[i] ).style.display = '';
		else
			document.getElementById( arguments[i] ).style.display = 'none';
    }
}

function fontsize( value )
{
	var body = document.getElementsByTagName("body")[0];

	if( !body.style.fontSize )
		newValue = (80 + value ) + "%";
	else
		newValue = ( parseInt( body.style.fontSize ) + value ) + "%";

	if( !value )
		body.style.fontSize = "80%";
	else
		body.style.fontSize = newValue;
}

function insertAfter( insertEl , targetEl )
{
    targetEl.parentNode.insertBefore( insertEl , targetEl.nextSibling );
}

function printModule( module )
{
	var cssFrag = " ",cssStr;
	cssFrag += " #box-main div,#content-content{  display: none;  } ";
	cssFrag += " .menu,.bar,.chat{ display: none; } #hd,#ft{ display: none; } #main{ margin: auto; }";
	cssFrag += " #" + module + "{  display: block;  } "
		
	cssStr = " @media print{ " + cssFrag + " } ";

	try{
		var oldStyle = document.getElementById( "printStyle" );
		oldStyle.parentNode.removeChild( oldStyle );
	}
	catch( e ){}

	var style = document.createElement( "STYLE" );

	style.setAttribute("type", "text/css");
	style.id = "printStyle";

	if( style.styleSheet ){// IE
		style.styleSheet.cssText = cssStr;
	}else{// w3c
		var cssText = document.createTextNode( cssStr );
		style.appendChild( cssText );
	}

	document.body.appendChild( style );
	window.print();
}

function tempRegressiveAvaliation( el , ini )
{
	if( ini > 0 )
	{
		var seconds = ini % 60 ;
		if( seconds < 10 )
			seconds = "0" + seconds;
		var minutes = ini / 60;
		var hour = minutes / 60;
		if( hour < 10 && hour >= 1 )
			hour = "0"+parseInt( hour );
		else
		{
			if( hour >= 10 )
				hour = parseInt( hour );
			else
				hour = "00";
		}
		var min = minutes % 60;
		if( min < 10 && min >= 1 )
			min = "0"+parseInt( min );
		else
		{
			if( min >= 10 )
				min  = parseInt( min );
			else
				min = "00";
		}
		
		try{
			document.getElementById( el ).innerHTML = hour+":"+min+":"+seconds;
			clearTime = setTimeout( "tempRegressiveAvaliation('"+ el +"',"+ ( ini-1 )+")", 1000 );
		}catch(e){}
	}
}

function verifyChanged()
{
    try{
        var value = document.getElementById("composer-main-editor").value,
            newvalue = oEditor._getDoc().body.innerHTML;

        if( value != newvalue ){
            return confirm( "Foram feitas alteraçãoes no conteúdo! Deseja realmente mudar de página sem salvá-las?" );
        }
    }catch( e ){}

	return true;
}