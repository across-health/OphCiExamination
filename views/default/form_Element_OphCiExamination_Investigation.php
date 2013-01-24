<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div id="div_<?php echo get_class($element)?>_description"
	class="eventDetail">
	<div class="data">
		<div class="textMacros">
			<?php foreach(OphCiExamination_Attribute::model()->findAllByElement($element) as $attribute) {
				echo $form->dropDownTextSelection($element, 'description',
					CHtml::listData($attribute->findAllOptionsForSubspecialty($this->firm->serviceSubspecialtyAssignment->subspecialty_id), 'value', 'label'),
					array('empty' => '-- '.$attribute->label.' --','class' => 'textMacro','nowrapper'=>true));
			}?>
		</div>
		<?php echo $form->textArea($element, 'description', array('rows' => "3", 'cols' => "80", 'class' => 'autosize', 'nowrapper'=>true)) ?>
	</div>
</div>
