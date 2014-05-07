{if $os_popupbox_data !=''}
<div class="home_popup" style="display: none">{$os_popupbox_data}</div>
<script type="text/javascript">
var reop_time=1;
var c='';
{literal}
    $(document).ready(function() { 
        if ( $.getCookie('home_popup') == 'open') {
        } else {$.setCookie('home_popup','open',reop_time);
         $.fancybox({
            'content' : $(".home_popup").html()
        });
    }
});
</script>
{/literal}
</script>
{/if}

        		