
<!-- Classic GA Tracking start<?php if ($options['debug_mode'] === true) { ?>, Debugging is on<?php } ?> -->
<script type="text/javascript">
var _gaq = _gaq || [];
<?php
echo $options['custom_trackers']."\n";
echo "_gaq.push(\n".implode(','."\n",$options['gaq'])."\n);\n";
?>
(function () {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = <?php echo $options['ga_src']; ?>;
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
})();
</script>
<!-- GA Tracking ends (v<?php echo ASGA_PLUGIN_VER ?>) -->
