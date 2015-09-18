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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_management_deferralreason".
 *
 * @property integer $id
 * @property string $name
 * @property integer $display_order

 */
class OphCiExamination_Management_DeferralReason extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     * @return OphCiExamination_Risks_Assignment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_management_deferralreason';
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return array validation rules for model OphCiExamination_Risks_Assignment.
     */
    public function rules()
    {
        return array(
                array('name, display_order', 'required'),
                array('id, name, display_order', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }

    /**
     * default the sort order
     *
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope($scope=array())
    {
        $alias = $this->getTableAlias(false, false);

        return array(
            'order' => $alias . '.display_order ASC',
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new \CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('display_order', $this->display_order, true);
        return new \CActiveDataProvider(get_class($this), array(
                'criteria'=>$criteria,
        ));
    }
}
