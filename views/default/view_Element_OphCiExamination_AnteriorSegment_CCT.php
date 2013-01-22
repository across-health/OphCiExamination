<div class="element <?php echo $element->elementType->class_name ?>">
	<h4 class="elementTypeName">
		<?php  echo $element->elementType->name ?>
	</h4>
	<div class="cols2 clearfix">
		<div class="left eventDetail">
			<?php if($element->hasRight()) { ?>
				<?php echo $element->right_value; ?> &micro;m
				(<?php echo $element->right_method->name; ?>)
			<?php } else { ?>
			Not recorded
			<?php } ?>
		</div>
		<div class="right eventDetail">
			<?php if($element->hasLeft()) { ?>
				<?php echo $element->left_value; ?> &micro;m
				(<?php echo $element->left_method->name; ?>)
			<?php } else { ?>
			Not recorded
			<?php } ?>
		</div>
	</div>
</div>
