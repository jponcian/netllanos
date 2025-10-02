<table align="center" background="../imagenes/fondo_principal.png" height="600" >
 <tr ><td width="520">
 <!--<p align="center"><img src="../imagenes/fondo_principal.png" width="589" height="427" ></p>-->
</td>
<td width="300">
 <?php 
 if (trim($_SESSION['TWITTER'])<>'')
 	{
	$twitter = trim($_SESSION['TWITTER']);
	}
else
 	{
	$twitter = 'SENIAT_LLANOS';
	}

 ?>
<!-- <a class="twitter-timeline" href="https://twitter.com/<?php echo $twitter; ?>" data-theme="LIGHT" data-chrome="nofooter " data-border-color="#16A085" data-link-color="#cc0000" data-aria-polite="assertive" width="100%" height="590" lang="ES">Tweets por @<?php echo $twitter; ?></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>--></td>
 
 </tr>
</table>