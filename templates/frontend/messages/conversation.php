<div id="goc_conversation" class="goc_conversation">
    <h3>Gesprek met <?php echo $this->otherUser->getName(); ?></h3>
    <div class="converstation_container">

        <div v-for="m in messages" class="message" v-bind:class="m.type">
            <span>{{m.sender}} - {{m.date}}</span>
            <div class="theMessage"  v-html="m.message">
            </div>
        </div>

    </div>

    <?php if($this->otherUser->getId() != 0){ ?>
    <div class="send_message">

        <div class="control  has-icons-right">
          <input class="input is-large" id="message" type="text" placeholder="Type hier je bericht">
            <input type="hidden" id="receiver" value="<?php echo $_GET["id"]; ?>" />
          <span class="icon is-medium is-right">
            <i class="far fa-paper-plane"></i>
          </span>
        </div>
    </div>
<?php
    }else{
        echo vsprintf(__("Je kan geen berichten sturen naar %s","xe_goc"),array($this->otherUser->getName()));

}
 ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(){
       var div = document.querySelector(".converstation_container");
   div.scrollTop = div.scrollHeight - div.clientHeight;
},false);
</script>
<script>
var conversationVue = new Vue({
  el: '#goc_conversation',
  data: {
    messages: <?php echo $this->messages; ?>
  },
updated(){
           var div = document.querySelector(".converstation_container");
   div.scrollTop = div.scrollHeight - div.clientHeight;
}
})
</script>
<script type="text/javascript">
  (function($) {

      let pause = 0;
      // Get the messages, every 2 seconds
      setInterval(function(){
          
          Goc.Messages.getMessages();

      }, 4000);



        $('#message').on('keypress', function (e) {
         if(e.which === 13){

             if($(this).val() != ''){
            //Disable textbox to prevent multiple submit
            $(this).attr("disabled", "disabled");

            //Do Stuff, submit, etc..
             Goc.Messages.addMessage();

             $(this).val("");

            }

         }
        });

        if (typeof (Goc) == "undefined") { Goc = {}; }

        Goc.Messages = {

            lastTime: 0,

            addMessage: function(){

                pause = 1;

                let theMessage = $("#message").val();
                let theReceiver = $("#receiver").val();


                $.ajax( {
                    url: '<?php echo esc_url_raw( rest_url() ); ?>goc/v1/messages/add',
                    method: 'POST',
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' ); ?>' );
                    },
                    data:{
                        'message' : theMessage,
                        'user' : theReceiver
                    }
                } ).done( function ( response ) {

                    if(response["status"] === true) {
                        //Enable the textbox again if needed.
                      $("#message").removeAttr("disabled");
                        $("#message").focus();
                        // Reload the messages
                        Goc.Messages.getMessages();


                    }else{ // set error message
                        // $(".message").html(response["message"]);
                    $("#message").removeAttr("disabled");
                    }

                } );

            },

            getMessages: function(){

                let theReceiver = $("#receiver").val();
                var date = new Date();

                if(Goc.Messages.lastTime === 0){
                    var timestamp = date.getTime();
                }else{
                    var timestamp = Goc.Messages.lastTime;
                }

                $.ajax( {
                    url: '<?php echo esc_url_raw( rest_url() ); ?>goc/v1/messages/get',
                    method: 'GET',
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' ); ?>' );
                    },
                    data:{
                        'timestamp' : timestamp,
                        'user' : theReceiver
                    }
                } ).done( function ( response ) {

                    // Reset pause
                    pause = 0;

                    if(response["messages"]) {


                        var ids = new Set(conversationVue.messages.map(d => d["id"]));

                        let m = JSON.parse(response["messages"]);
                        m = m.filter(d => !ids.has(d["id"]));

                        let old = conversationVue.messages;
                        let newM = old.concat(m);

                        conversationVue.messages = newM;

                        Goc.Messages.lastTime = response["timestamp"];


                    }else{ // set error message
                        // $(".message").html(response["message"]);
                    }

                } );

            }

        }



    })( jQuery );
</script>
<style>
    .goc_conversation .converstation_container {
        min-height: 60vh;
        max-height: 60vh;
        overflow-y: scroll;
    }
    .goc_conversation .converstation_container .message{
        display: block;
        margin: 10px;
        max-width: 50%;
        background-color: transparent;
        clear: both;
    }
    .goc_conversation .converstation_container .message span {
        display: block;
        font-size: 0.7em;
        padding-bottom: 1px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .goc_conversation .converstation_container .message .theMessage{
        display: inline-block;
        padding: 10px;
        margin: 10px;
        background-color: #7F8C8D;
        border:1px solid #7F8C8D;
        border-radius: 10px;
    }
    .goc_conversation .converstation_container .message.sender {
        float: right;
        text-align: right;
    }
    .goc_conversation .converstation_container .message.sender .theMessage{
        background-color: #3BBC4C;
        border-color: #3BBC4C;
    }

</style>