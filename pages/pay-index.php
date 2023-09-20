<p class="error">This page has been disabled. For assistance with existing payments, please email <a href="mailto:support@dawn-tdm.com">support@dawn-tdm.com</a>.</p>
<?php

/*

$fail = false;

if ($gdata["Locked"] == 1) {
	echo "<p class=\"error\">Your account is locked and cannot complete purchases.</p>";
    $fail = true;
}

if (isset($args[2]) && !empty($args[2]) && is_numeric($args[2])) {
    $g = $game->getMemberData($args[2]);
}
else {
    $g = $gdata;
}

$add_to_eop = "";
$ipnurl = "http://dev.dawn-tdm.com/ipn.php?id=";
$style = "";
if ($g["IsLogged"] == 1) {
	echo "<p class=\"error\">Please leave the server before donating.</p>";
    $fail = true;
}
if ($g["Locked"] == 1) {
    echo "<p class\"error\">This account is locked.</p>";
    $fail = true;
}
if(!$fail) {
    $you = "Account " . $g["user"];

    if ($g["VIPDate"] == "2038-01-01") {
        $add_to_eop = "</div>";
        $style = " style=\"display:none\"";
	    echo <<<EOD
	    <p class="error">$you already has permanent VIP. If you simply want to donate, use the button below.</p>
	    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		    <input type="hidden" name="cmd" value="_s-xclick">
		    <input type="hidden" name="hosted_button_id" value="CM74UBDPZ3GYQ">
		    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
		    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	    </form>
        <div>
EOD;
    }
    ?>
        <div>
            <p>Your VIP expires <strong><?=date('Y-m-d', strtotime($gdata["VIPDate"] . " +1 day"))?></strong></p>
        </div>
        <div>
            <label for="buyto">Currently buying for: </label><input type="text" name="buyto" id="buyto" value="<?=htmlspecialchars($g["user"])?>"/>
        </div>
	    <div class="donation"<?=$style?>>
		    <input id="ppinput" type="number" value="5"/>&euro;
		    <p id="ppinfo">5&euro; will get <?=$you?> 40 days VIP</p>
		    <script src="/external/paypal-button.min.js?merchant=3ventic@dawn-tdm.com" data-button="buynow" data-name="New Dawn VIP" data-quantity="1" data-custom="<?=$g['ID']?>" data-amount="5.00" data-currency="EUR" data-callback="<?=$ipnurl?><?=$g['ID']?>" async></script>
	    </div>
    <script>
    $(document).ready(function () {
	    $('#ppinput').change(function () {
		    $('#ppinfo').html(parseFloat($('#ppinput').val()).toFixed(2) + '&euro; will get <?=$you?> ' + Math.floor(parseFloat($('#ppinput').val()).toFixed(2) * 8) + ' days VIP');
		    $('input[name="amount"]').attr('value', parseFloat($('#ppinput').val()).toFixed(2));
	    });
        $('#buyto').change(function () {
            $('div.donation').hide();
            $.get('/api/idfromname.php?name=' + encodeURIComponent($('#buyto').val()), function (data) {
                if (data != '') {
                    if (data.error) {
                        alert(data.code + ": " + data.message);
                    }
                    else {
                        window.location.href = "/payments/" + data.id;
                    }
                }
            });
        });
    });
    </script>
	    <div class="clear"></div>
    <?php
}
echo $add_to_eop;
?><div>
    <p>Issues? Contact support at <a href="mailto:support@dawn-tdm.com">support@dawn-tdm.com</a></p>
</div>
*/