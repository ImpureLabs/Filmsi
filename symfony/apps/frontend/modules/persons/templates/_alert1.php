<div class="normalcell">
	<button class="alert-person-trigger announcement spacer-bottom left spacer-right-l" style="cursor: pointer"></button>
	<p><span class="icon-checkbox-checked"></span> <a href="" class="explanation-link">Cand se lanseaza in cinema</a></p>
	<p><span class="icon-checkbox-checked"></span> <a href="" class="explanation-link">Cand se lanseaza pe DVD</a></p>
	<div id="person-alert-container"></div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	$('.alert-person-trigger').click(function(){
		$('#person-alert-container').dialog({
        show: 'clip',
        hide: 'clip',
        modal: false,
        width: 300,
        height: 250,
        closeOnEscape: false,
        resizable: false,
        title: '<?php echo __('Anunta-ma')?>',
        open: function(){
                // Before requesting the content of the dialog, ad the indicator
                $('#person-alert-container').html('<div class="align-center"><br /><br /><br /><img src="<?php echo image_path('indicator.gif');?>" /></div>');
                $('#person-alert-container').load('<?php echo url_for('@default?module=persons&action=alertAdd');?>?id=<?php echo $personId;?>');
              }
		});
	});
});
</script>