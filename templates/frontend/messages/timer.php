
<div class="columns">
    <div class="column is-one-third"></div>
    <div class="column is-one-third" style="text-align:center;">
        <h5>
            <?php

            echo $this->message;

            ?>
        </h5>
        <img src="https://www.goodoldcriminals.com/wp-content/uploads/2020/04/time-management.png"/>
    </div>
</div>


<script type="text/javascript">
    (function($) {

        window.onload = function () {

            $(".gocTimer").each(function(index){

                let time = $(this).data("time") - 1; // To fix the interval :)
                let theItem = $(this);

                let currentTimer = setInterval(function () {

                    let hours = 0;
                    let minutes = 0;
                    let seconds = time;
                    if(time > 59){
                        minutes = Math.floor(seconds/60);
                        hours = Math.floor(minutes/60);
                        minutes = minutes - (hours * 60);
                        seconds = time - (60* minutes) - (60 * 60 * hours);
                    }

                    // Set time
                    theItem.html(n(hours)+":"+n(minutes)+":"+n(seconds));

                    if(time > 0) {
                        time--;
                    }else{
                        location.reload();
                            clearInterval(currentTimer);
                    }

                }, 1000);

            });

        };
    })( jQuery );

    function n(n){
        return n > 9 ? "" + n: "0" + n;
    }
</script>
