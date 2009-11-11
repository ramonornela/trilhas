function runLayout()
{
	var checkbox = document.getElementById( "formLayout" ).elements;
	var relations = [];
			
	for ( var i = 0; i< checkbox.length; i++ )
	{
		if ( checkbox[i].type == "checkbox" && checkbox[i].checked  )
		{
			relation = { "div": checkbox[i].id  , "url":  URL + '/' + checkbox[i].value };;
			relations.push( relation );
		}
	}
	
	document.getElementById( "relation" ).value = YAHOO.lang.JSON.stringify( relations );
	
	new Preceptor.util.AjaxUpdate( 'configuration-layout' , URL + "/configuration/layout/save" , { formId:"formLayout" } );
}

function runSearchView( id , url )
{
	var main = document.getElementById( "main" ),
		div = document.getElementById( id );

	if( !div ){
		div = document.createElement( "div" );
		div.id = id;
		main.appendChild( div );
	}

	new Preceptor.util.AjaxUpdate( id , url );
}

function runElementMeio( url , id  )
{
	var main = document.getElementById( "main" ),
		div = document.getElementById( id );

	if( !div ){
		div = document.createElement( "div" );
		div.id = id;
		main.insertBefore( div , main.firstChild );
	}
	
	new Preceptor.util.AjaxUpdate( id , url );
}