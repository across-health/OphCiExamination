<h4 class="elementTypeName">
	<?php  echo $element->elementType->name ?>
</h4>
<h5>Left</h5>
<p>
	<?php
	$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
			'identifier' => 'left_'.$element->elementType->id,
			'side' => 'L',
			'mode' => 'view',
			'size' => 200,
			'model' => $element,
			'attribute' => 'left_eyedraw',
	));
	?>
</p>
<p>
	Description:
	<?php echo $element->left_description ?>
</p>
<p>
	Diagnosis:
	<?php if($element->left_diagnosis) { 
		echo $element->left_diagnosis->term;
	} else { echo 'None';
} ?>
</p>
<h5>Right</h5>
<p>
	<?php
	$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
			'identifier' => 'right_'.$element->elementType->id,
			'side' => 'R',
			'mode' => 'view',
			'size' => 200,
			'model' => $element,
			'attribute' => 'right_eyedraw',
	));
	?>
</p>
<p>
	Description:
	<?php echo $element->right_description ?>
</p>
<p>
	Diagnosis:
	<?php if($element->right_diagnosis) { 
		echo $element->right_diagnosis->term;
	} else { echo 'None';
} ?>
</p>