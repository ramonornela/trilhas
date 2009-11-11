var Chat = {
    dialogs: [],
    id: 0,
    ids: [0],
    html: null,
    time: null,
    url: null,
    hasIframe:false,
	
    init: function(url, id)
    {
        this.url = url;
        this.id = id;
        this.initEvents();
        this.verify();
    },

    initEvents: function()
    {
        $('#'+this.id+' ul li').live('click', function(){
            Chat.Dialog.open($(this).text(), $(this).children('input').val());
        });

        $('#'+Chat.id+' .dialogs textarea').live('keypress', Chat.submit)
    },
	
    verify: function()
    {
        $.get(this.url, {'ids[]': this.ids}, function(data){
            Chat.write(data);
            Chat.verify();
        });
    },

    write: function(data) {
        var i = 0, html = '';
        data = eval(data);
        for (;i < data.length;i++) {
            this.ids.push(data[i].id);

            if (data[i].person_id != 6) {
                if(!this.Dialog.exists(data[i].person_id) > -1) {
                    this.Dialog.open(data[i].name, data[i].person_id, data[i].chat_group_id);
                }
            }
            html = '<p><strong>'+shortName(data[i].name)+':</strong> '+data[i].ds+'</p>';
            $('#chat_text_'+data[i].chat_group_id).append(html);
        }
    },
	
    submit: function()
    {
        console.log('press');
    }
}

Chat.Dialog = {
    opened: [],
    open: function(name, id, chat_group_id)
    {
        var position = this.exists(id);
        
        if (!chat_group_id) {
            if (position > -1) {
                chat_group_id = $('#message_'+id).children('input').val();
            } else {
                $.get('chat/index/create-group/id/'+id, function(response){
                    chat_group_id = response;
                });
            }
        } 
        
        if (position > -1) {
            this.activate(position);
        } else {
            var item = "<h3><a href='#'>"+name+"</a></h3>\n\
                        <div id='message_"+id+"' class='message'>\n\
                            <div id='chat_text_"+chat_group_id+"'></div>\n\
                            <textarea name='ds'></textarea>\n\
                            <input name='id' type='hidden' value='"+chat_group_id+"' />\n\
                        </div>",
                $dialogs = $('#'+Chat.id+' .dialogs');

            $dialogs.prepend(item);
            $dialogs.accordion('destroy');
            $dialogs.accordion({header:'h3', autoheight:false, height:100});
            this.opened.push(id);
        }
    },

    exists: function(id) {
        var position = $.inArray(id, this.opened);
        if (position > -1) {
            return position;
        } else {
            return -1;
        }
    },

    close: function(){
        
    },

    activate: function(position){
        var $dialogs = $('#'+Chat.id+' .dialogs'),
            position = (this.opened.length - position) - 1;
            
        $dialogs.accordion('activate',position);
    }
}
function shortName(name)
{
    return name.split(" ")[0];
}