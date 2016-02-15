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


class RefractiveOutcomeReport extends \Report implements \ReportInterface {

    /**
     * @var string
     */
    protected $searchTemplate = 'OphCiExamination.views.reports._refractive_outcome_search';

    /**
     * @var array
     */
    protected $graphConfig = array(
        'chart' => array(
            'renderTo' => '',
            'type' => 'column',
            'zoomType' => 'xy'
        ),
        'title' => array('text' => 'Refractive Outcome'),
        'xAxis' => array(
            'categories' => array(),
            'title' => array('text' => 'PPOR - POR'),
            //'labels' => array('style' => array('fontSize' => '0.5em'))
        ),
        'yAxis' => array(
            'min' => 0,
            'title' => 'Patient Number'
        )
    );

    /**
     * @var int
     */
    protected $months = 4;

    /**
     * @var bool
     */
    protected $onlyCataract = true;


    /**
     * Gets details from the request
     *
     * @param $request
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->months = $app->getRequest()->getQuery('months', 4);
        $this->onlyCataract = $app->getRequest()->getQuery('onlyCataract', true);

    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $data = $this->queryData($this->surgeon, $this->from, $this->to, $this->months);

        $output = array();

        foreach($data as $row){
            $eyeSphere = strtolower($row['name']).'_sphere';
            if(is_null($eyeSphere)){
                //No reading for the eye we need so continue
                continue;
            }

            $value = (string)($row['predicted_refraction'] - $row[$eyeSphere]);

            if(!array_key_exists($value, $output)){
                $output[$value] = 0;
            }
            $output[$value]++;
        }

        ksort($output);
        $this->graphConfig['xAxis']['categories'] = array_keys($output);

        return array_values($output);
    }

    /**
     * @return string
     */
    public function dataSetJson()
    {
        return json_encode($this->dataSet());
    }

    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series[] = array(
            'data' => $this->dataSet(),
            'name' => 'Number of Patients'
        );

        return json_encode($this->series);
    }

    /**
     * @return string
     */
    public function graphConfig()
    {
        $this->graphConfig['chart']['renderTo'] =  $this->graphId();
        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

    /**
     * @param $surgeon
     * @param string $fromDate
     * @param string $toDate
     * @param int $postSurgery
     * @param bool $onlyCataract
     * @return array|\CDbDataReader
     */
    protected function queryData($surgeon, $fromDate = '', $toDate = '', $postSurgery = 4, $onlyCataract = true)
    {
        $this->command->reset();
        $examinationEvent = $this->getExaminationEvent();
        $this->command->select('note_event.id as event_id, note_event.event_date, op_procedure.eye_id, post_examination.event_date as post_date,
                                right_sphere, left_sphere, eye.name, et_ophtroperationnote_cataract.predicted_refraction'
        )
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id')
            ->join('episode', 'note_event.episode_id = episode.id')
            ->join('event post_examination',
                'post_examination.episode_id = note_event.episode_id AND post_examination.event_type_id = :event
                AND post_examination.event_date >= DATE_ADD(note_event.event_date, INTERVAL :postSurgery MONTH)',
                array('event' => $examinationEvent['id'], 'postSurgery' => $postSurgery)
            )
            ->join('et_ophtroperationnote_cataract', 'et_ophtroperationnote_cataract.event_id = note_event.id')
            ->join('et_ophciexamination_refraction', 'et_ophciexamination_refraction.event_id = post_examination.id')
            ->join('eye', 'op_procedure.eye_id = eye.id')
            ->where('surgeon_id = :user', array('user' => $surgeon));

        if($fromDate){
            $this->command->andWhere('note_event.event_date >= :from', array('from' => $fromDate));
        }

        if($toDate){
            $this->command->andWhere('note_event.event_date <= :to', array('to' => $toDate));
        }

        if($onlyCataract){
            $this->command->join('ophtroperationnote_procedurelist_procedure_assignment proc_assignment', 'op_procedure.id = proc_assignment.procedurelist_id');
            $this->command->andWhere('proc_assignment.proc_id in ( select procedure_id from ophtroperationnote_procedure_element
                                                                     join element_type on element_type_id = element_type.id
                                                                     where element_type.name = "Cataract")');
        }

        return $this->command->queryAll();
    }

    /**
     * @return mixed|string
     */
    public function renderSearch()
    {
        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this));
    }

}