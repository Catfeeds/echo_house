<div class="page-bar">
	<ul class="page-breadcrumb">
		<?php foreach($links as $k=>$v): ?>
			<li>
				<?php if(!$k): ?>
					<i class="fa fa-home"></i>
				<?php endif;?>
				<?php echo $v; ?>
				<?php if(count($links)!=($k+1)): ?>
					<i class="fa fa-angle-right"></i>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>