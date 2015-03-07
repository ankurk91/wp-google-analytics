<!--Classic GA Tracking start-->
<script type="text/javascript">
var _gaq = _gaq || [];
<?php
echo $plugin_url;
foreach($gaq as $item){
echo '	_gaq.push([' . $item . "]);\n";
}
?>
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = <?php echo $ga_src; ?>;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<!--GA Tracking ends-->