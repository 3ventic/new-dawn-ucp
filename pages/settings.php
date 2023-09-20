<?php
$sigurl = BASEURL . "sig/{$gdata['ID']}/1/signature.png";
if($gdata['IsLogged'] == 1) {
    echo '<p class="alert">You cannot access your settings while your account is logged in on the server.</p>';
}
else
{
?>
<table class="summary left-narrow">
    <thead>
        <th class="left">Setting</th>
        <th class="right">Value</th>
    </thead>
    <tbody>
        <tr>
            <td class="left">Signature</td>
            <td class="right">
                <select id="signature-style" class="wide">
                    <?php
                    
                    for ($i = 1; file_exists("signatures/bgimg$i.png"); $i++) {
                        echo "<option value=\"$i\">#$i by " . signatureAuthor($i) . "</option>";
                    }
                    
                    ?>
                </select><br/><br/>
                <input type="text" id="signature-url" class="wide" value="<?=$sigurl?>" readonly/><br/><br/>
                <img id="signature-img" src="<?=$sigurl?>" alt=""/>
            </td>
        </tr>
        <tr>
            <td class="left">In-Game Name</td>
            <td class="right"><input type="text" id="user" class="wide" value="<?=$gdata['user']?>"/></td>
        </tr>
        <tr>
            <td class="left">Skin ID</td>
            <td class="right"><input type="number" id="skin" class="wide" value="<?=$gdata['Skin']?>" min="0" max="299"/></td>
        </tr>
        <tr>
            <td class="left">Change Password <span class="note">leave empty to keep the current one</span></td>
            <td class="right">
                <input type="password" id="passforum" class="wide" value="" placeholder="Your Current Forum Password"/>
                <input type="password" id="pass1" class="wide" value="" placeholder="New Password"/>
                <input type="password" id="pass2" class="wide" value="" placeholder="New Password Again"/>
            </td>
        </tr>
    </tbody>
</table>
<div class="actions">
    <button id="save">Save</button>
</div>
<script>
$(document).ready(function(){$('#save').click(function(e){$.post('/save/settings',{user:$('#user').val(),skin:$('#skin').val(),pass1:$('#pass1').val(),pass2:$('#pass2').val(),passforum:$('#passforum').val()}).done(function(d){alert(d)})});$("#signature-style").change(function(){$("#signature-url").val("<?=BASEURL?>sig/<?=$gdata['ID']?>/"+$(this).val()+"/signature.png");$("#signature-img").attr("src","<?=BASEURL?>sig/<?=$gdata['ID']?>/"+$(this).val()+"/signature.png")})});
</script>
<?php
}
?>