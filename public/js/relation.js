var Relation = function( data ){
	
	this.relations = [];
	this.init( eval( data ) );
}

Relation.prototype = {
	init: function( json ){
		if ( json ){ 
			for( var i = 0; i < json.length; i++ )
			{
				this.add( json[i].id , json[i].type , json[i].text ,  json[i].model );
			}
		}
	},
	
	add: function( id , type , text , model ){
		
		if( !id )
			return;
			
		for( var i = 0; i < this.relations.length; i++ )
		{
			if( this.relations[i].id == id && this.relations[i].type == type  )
			{
				alert( "Registro já adicionado!" );
				return;
			}
		}	
		
		var div  	= document.createElement( "DIV" );
		var divtext = document.createElement( "DIV" );
		var a 	 	= document.createElement( "A" );
		var random	= "added_" + Math.random();
		
		div.className = "added";
		div.id = random;
		divtext.innerHTML = text;
		
		
		a.href = "#this";
		a.innerHTML = "x";
		
		YAHOO.util.Event.on( a , "click" , function(){
			this.remove( id , type , random );
		} , this, true )
		
		div.appendChild( divtext );
		div.appendChild( a );
		
		document.getElementById( "added-"+model ).parentNode.insertBefore( div , document.getElementById( "added-"+model ) );
		
		this.relations.push( { id: id , type: type } );
	},
	
	remove: function( id , type , div ){
		for( var i = 0; i < this.relations.length; i++ )
		{
			if( this.relations[i].id == id && this.relations[i].type == type  )
			{
				document.getElementById( div ).parentNode.removeChild( document.getElementById( div ) );
				this.relations.splice( i , 1 );
				break;
			}
		}	
	},
	
	save: function(){
		return YAHOO.lang.JSON.stringify( this.relations );
	}
}