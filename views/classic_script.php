
<!-- Classic GA Tracking start<?php if ($options['debug_mode'] === true) { ?>, Debugging is on<?php } ?> -->
<script type="text/javascript">
var _gaq = _gaq || [];
<?php
if($options['js_load_later'] === true){ ?>
function _loadGA() {
<?php }

echo $options['custom_trackers']."\n";
echo "_gaq.push(\n".implode(','."\n",$options['gaq'])."\n);\n";

?>
(function () {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = <?php echo $options['ga_src']; ?>;
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
})();
<?php if($options['js_load_later'] === true) {?>
}
window.addEventListener ? window.addEventListener("load", _loadGA, !1) : window.attachEvent ? window.attachEvent("onload", _loadGA) : window.onload = _loadGA;
<?php }?>
</script>
<!-- GA Tracking ends (v<?php echo ASGA_PLUGIN_VER ?>) -->
