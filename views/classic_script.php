<!--Classic GA Tracking start<?php if($debug_mode===true){?>, Debugging is on<?php }?>-->
<script type="text/javascript">
var _gaq = _gaq || [];
<?php
echo $ela_plugin_url;
foreach($gaq as $item){
echo "_gaq.push([" . $item . "]);\n";
}
if($user_engagement==1){ ?>
setTimeout(function(){_gaq.push(['_trackEvent','Page Read',document.location.href,document.referrer])},15E3);
<?php } ?>
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = <?php echo $ga_src; ?>;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<!--GA Tracking ends-->