var rec_id='';
$my_id="{{Auth::id()}}";
$(document).ready(function(){

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('2f15ef133242b437736d', {
      cluster: 'ap2'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
      alert(JSON.stringify(data));
      //check if i was the sender
      if($my_id== data.from){
          alert('sender');
      }
      else if($my_id == data.to){
          //chek if the selected user was who send the message
          if(rec_id == data.from){
              $('#' + data.from).click();
          }
          else{
              var pending= parseInt($('#' + data.from).find('.pending').html());
              if(pending){
                  $('#' + data.from).find('.pending').html(pending +1);
              }//this else will happen one time at the frist time any user send data
              else{
                  $('#' + data.from).append('<span class="pending">1</span>');
              }
          }
      }
    });

    $('.user').click(function(){
        $('.user').removeClass('active');
        $(this).addClass('active');
        rec_id=$(this).attr('id');
        $.ajax({
            type: "get",
            url:"massege/"+ rec_id,
            data:'',
            cache:false,
            success:function(data){
                $('#messages').html(data);
            }
        });
    });
    $.ajaxSetup({

        headers: {
      
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      
        }
      
      });
    $(document).on('keyup' ,' .input-text input' ,function(e){
        var message=$(this).val();
        //check if the user press in the enter button and the input text is not empty and the user_is is not null=>
        if(e.keyCode==13 && message !='' && rec_id !=''){
            //after press enter the input will be empty
            $(this).val('');
            var datastr= "rec_id=" + rec_id + "&message=" + message; 
            //post the message to messgae page using ajax
            $.ajax({
                type:"POST",
                url:"messages", //this post route
                data: datastr,
                dataType: 'string',
                cache:false,
                success: function(data){

                },
                error:function(jqXHR ,status,err){

                },
                complete:function(){

                },

            });
            
        }

    });
});
