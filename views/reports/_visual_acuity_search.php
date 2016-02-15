<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div id="<?=$report->graphId();?>_search" class="report-search visuallyhidden">
    <form action="/report/reportData" id="<?=$report->graphId();?>_search_form" class="report-search-form">
        <fieldset class="mdl-color-text--grey-600">
            <input type="hidden" value="\OEModule\OphCiExamination\components\VisualAcuity" name="report" >
            <div class="mdl-selectfield">
                <label for="months" class="">Months prior to surgery</label>
                <select id="months" name="months">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option selected>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>12</option>
                    <option>24</option>
                </select>
            </div>
            <div class="mdl-selectfield">
                <label for="method">VA Method</label>
                <?=CHtml::dropDownList('method', null, $methods);?>
            </div>
            <div>
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-1">
                    <input type="radio" id="option-1" class="mdl-radio__button" name="vaType" value="distance" checked>
                    <span class="mdl-radio__label">DVA</span>
                </label>
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-2">
                    <input type="radio" id="option-2" class="mdl-radio__button" name="vaType" value="near">
                    <span class="mdl-radio__label">NVA</span>
                </label>
            </div>
            <div>
                <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </fieldset>
    </form>
</div>