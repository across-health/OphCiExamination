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

namespace OEModule\OphCiExamination\components;

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;

class VisualAcuityReport extends \Report implements \ReportInterface {

    /**
     * @var int
     */
    protected $months;

    /**
     * @var int
     */
    protected $method;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $searchTemplate = 'OphCiExamination.views.reports._visual_acuity_search';

    /**
     * @var array
     */
    protected $graphConfig = array(
        'chart' => array(
            'renderTo' => '',
            'type' => 'scatter',
            'zoomType' => 'xy'
        ),
        'title' => array('text' => 'Visual Acuity'),
        'xAxis' => array(
            'gridLineWidth' => 1,
            'title' => array('text' => 'At Surgery'),
            'labels' => array('format' => '{value}'),
        ),
        'yAxis' => array(
            'gridLineWidth' => 1,
            'title' => array('text' => 'After Surgery'),
            'labels' => array('format' => '{value}'),
        ),
        'plotOptions' => array(
            'scatter' => array(
                'marker' => array(
                    'radius' => 5,
                    'states' => array(
                        'hover' => array('enabled' => true, 'lineColor' => 'rgb(100,100,100)')
                    )
                ),
                'states' => array(
                    'hover' => array('enabled' => false)
                ),
                'tooltip' => array(
                    'headerFormat' => '<b>Visual Acuity</b><br>',
                    'pointFormat' => 'Before Surgery {point.x}, And after {point.y}'
                )
            )
        )
    );

    /**
     * @var array
     */
    protected $vaTypes = array(
        'distance' => 'et_ophciexamination_visualacuity',
        'near' => 'et_ophciexamination_nearvisualacuity',
    );

    /**
     * Gets details from the request
     *
     * @param $request
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->months = $app->getRequest()->getQuery('months', 4);
        $this->method = $app->getRequest()->getQuery('method', 0);
        $vaType = $app->getRequest()->getQuery('vaType', 'distance');
        if(array_key_exists($vaType, $this->vaTypes)){
            $this->table = $this->vaTypes[$vaType];
        } else {
            $this->table = $this->vaTypes['distance'];
        }

    }

    /**
     * Sets up the data this report will require
     *
     * @return array
     */
    public function dataSet()
    {
        $data = $this->queryData(
            $this->surgeon,
            $this->from,
            $this->to,
            $this->months,
            $this->method,
            $this->table
        );

        $dataSet = array();
        $eventAndMethods = array();
        $readingModel = OphCiExamination_VisualAcuity_Reading::model();
        $logMarUnit = OphCiExamination_VisualAcuityUnit::model()->find('`name` = ?', array('logMAR'));

        foreach($data as $row){

            if(array_key_exists($row['event_id'], $eventAndMethods) && in_array($row['method_id'], $eventAndMethods[$row['event_id']])){
                //if we already have this method for the event then the rest of the readings are later ones.
                continue;
            }

            if(!array_key_exists($row['event_id'], $eventAndMethods) || !is_array($eventAndMethods[$row['event_id']])){
                $eventAndMethods[$row['event_id']] = array();
            }

            $eventAndMethods[$row['event_id']][] = $row['method_id'];

            $dataSet[] = array(
                (float)$readingModel->convertTo($row['pre_value'], $logMarUnit->id),
                (float)$readingModel->convertTo($row['post_value'], $logMarUnit->id)
            );
        }

        return $dataSet;
    }

    /**
     * JSON encodes the data for the chart
     *
     * @return string
     */
    public function dataSetJson()
    {
        return json_encode($this->dataSet());
    }

    public function seriesJson()
    {
        $this->series[] = array(
            'data' => $this->dataSet()
        );

        return json_encode($this->series);
    }

    public function graphConfig()
    {
        $this->graphConfig['chart']['renderTo'] =  $this->graphId();
        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

    /**
     * Queries the database for the data the chart will require
     *
     * @param int $surgeon
     * @param string $fromDate
     * @param string $toDate
     * @param int $postSurgery
     * @param int $method
     * @param string $vaTable
     * @return array|\CDbDataReader
     */
    protected function queryData($surgeon, $fromDate = '', $toDate = '', $postSurgery = 4, $method = 0, $vaTable = 'et_ophciexamination_visualacuity')
    {
        $this->command->reset();
        $examinationEvent = $this->getExaminationEvent();

        $this->command->select('note_event.id as event_id, pre_reading.value as pre_value, post_reading.value as post_value, pre_examination.event_date as pre_date, post_examination.event_date as post_date, pre_reading.method_id')
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id')
            ->join('episode', 'note_event.episode_id = episode.id')
            ->join('event pre_examination',
                'pre_examination.episode_id = note_event.episode_id AND pre_examination.event_type_id = :event
                AND pre_examination.event_date <= note_event.event_date',
                array('event' => $examinationEvent['id'])
            )
            ->join('event post_examination',
                'post_examination.episode_id = note_event.episode_id AND post_examination.event_type_id = :event
                AND post_examination.event_date >= DATE_ADD(note_event.event_date, INTERVAL :postSurgery MONTH)',
                array('event' => $examinationEvent['id'], 'postSurgery' => $postSurgery)
            )
            ->join($vaTable.' pre_acuity',
                'pre_examination.id = pre_acuity.event_id AND (pre_acuity.eye_id = op_procedure.eye_id OR pre_acuity.eye_id = 3)'
            )
            ->join($vaTable.' post_acuity',
                'post_examination.id = post_acuity.event_id AND (post_acuity.eye_id = op_procedure.eye_id OR post_acuity.eye_id = 3)'
            )
            ->join('ophciexamination_visualacuity_reading pre_reading',
                'pre_acuity.id = pre_reading.element_id  AND IF(op_procedure.eye_id = 1, pre_reading.side = 1, IF(op_procedure.eye_id = 2, pre_reading.side = 0, pre_reading.side IS NOT NULL))')
            ->join('ophciexamination_visualacuity_reading post_reading',
                'post_acuity.id = post_reading.element_id AND post_reading.side = pre_reading.side AND post_reading.method_id = pre_reading.method_id')
            ->where('surgeon_id = :user', array('user' => $surgeon))
            ->order('note_event.id, pre_examination.event_date, post_examination.event_date');

        if($method){
            $this->command->andWhere('pre_reading.method_id = :method', array('method' => $method));
        }

        if($fromDate){
            $this->command->andWhere('note_event.event_date >= :from', array('from' => $fromDate));
        }

        if($toDate){
            $this->command->andWhere('note_event.event_date <= :to', array('to' => $toDate));
        }

        return $this->command->queryAll();
    }

    /**
     * Renders the search template
     *
     * @return mixed|string
     */
    public function renderSearch()
    {
        $methods = array_merge(array(0 => 'All'), \CHtml::listData(OphCiExamination_VisualAcuity_Method::model()->findAll(), 'id', 'name'));

        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'methods' => $methods));
    }
}