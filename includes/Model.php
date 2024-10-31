<?php

namespace Puchi;
use Puchi\Medoo;

class Model {
        
        private $db, $tbl;
        private static $instance;
        
        public function __construct(){
                global $wpdb;
                $this->db = new Medoo([
                        'database_type' => 'mysql',
                        'database_name' => DB_NAME,
                        'server' => 'localhost',
                        'username' => DB_USER,
                        'password' => DB_PASSWORD,
                        'charset' => DB_CHARSET,
                        'prefix' => $wpdb->prefix,
                ]);
                $this->tbl = [
                        'statistic' => 'puchi_statistic',
                        'posts' => 'posts'
                ];
        }
        
        public static function get_instance(){
                if(!isset(self::$instance)) {
                        self::$instance = new Model();
                }
                return self::$instance;
        }
        
        public function add_split_data($data){
                return $this->db->insert($this->tbl['statistic'], $data);
        }
        
        public function get_split_from_page($page_id){
                return $this->db->select($this->tbl['statistic'], [
                                $this->tbl['statistic'].".split_id"
                        ],
                        Medoo::raw("where <puchi_statistic.page_id> = $page_id group by <puchi_statistic.split_id> asc")
                );
        }
        
        public function get_content_from_split($page_id, $split_id){
                return $this->db->select($this->tbl['statistic'], [
                                $this->tbl['statistic'].".content_title",
                                $this->tbl['statistic'].".weight"
                        ],
                        Medoo::raw("where <puchi_statistic.page_id> = $page_id and <puchi_statistic.split_id> = $split_id group by <puchi_statistic.content_title> asc")
                );
        }
        
        public function get_page_statistic_data(){
                $results = [];
                $data =  $this->db->select($this->tbl['statistic'],
                        [
                                "[>]".$this->tbl['posts'] => ["page_id" => "id"]
                        ],[
                                $this->tbl['statistic'].".page_id",
                                $this->tbl['posts'].".post_title",
                                $this->tbl['statistic'].".type",
                                $this->tbl['statistic'].".ip_address",
                        ]
                );
                
                if(is_array($data) && !empty($data)){
                        foreach($data as $d => $v){
                                if(!isset($results[$v['page_id']])){
                                        $results[$v['page_id']] = [
                                                'page_id' => $v['page_id'],
                                                'post_title' => $v['post_title'],
                                                'click' => [],
                                                'visit' => []
                                        ];
                                }
                                if($v['type'] == 'click'){
                                        array_push($results[$v['page_id']]['click'], $v['ip_address']);
                                }else{
                                        array_push($results[$v['page_id']]['visit'], $v['ip_address']);
                                }
                        }
                }
                
                return $results;
        }
        
        public function delete_page_statistic_data($id){
                return $this->db->delete($this->tbl['statistic'],[ 'AND' => ['page_id' => $id] ]);
        }
        
        public function count_data($tbl, $where){
                return $this->db->count($this->tbl[$tbl], $where);
        }
        
        public function delete_statistic_data($post_id){
                return $this->db->delete($this->tbl['statistic'],[ "AND" => ['page_id' => $post_id] ]);
        }
        
        public function get_statistic_table_data($query, $type, $page, $split, $content){
                $query = ($split != 'all_split') ? $query .= " and <puchi_statistic.split_id> = $split" : $query;
                $query = ($content != 'all_split_content') ? $query .=" and <puchi_statistic.content_title> = '$content'" : $query;
                if($type == 'bounce_rate'){
                        $click = $this->db->select(
                                $this->tbl['statistic'], //TABLE
                                $this->tbl['statistic'].".ip_address",//COLUMN
                                Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.type> = 'click' $query")
                        );
                        
                        $visit = $this->db->select(
                                $this->tbl['statistic'], //TABLE
                                $this->tbl['statistic'].".ip_address",//COLUMN
                                Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.type> = 'visit' $query")
                        );
                        
                        $click = count($click) ;
                        $visit = count($visit);
                        return ($visit != 0) ? number_format(($visit - $click) / $visit * 100, 2, '.', '') : 0;
                        
                }elseif($type == 'conversion_rate'){
                        $click = $this->db->select(
                                $this->tbl['statistic'], //TABLE
                                $this->tbl['statistic'].".ip_address",//COLUMN
                                Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.type> = 'click' $query")
                        );
                        
                        $visit = $this->db->select(
                                $this->tbl['statistic'], //TABLE
                                $this->tbl['statistic'].".ip_address",//COLUMN
                                Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.type> = 'visit' $query")
                        );
                        return (count($click) != 0 && count($visit) != 0) ? number_format((count($click) / count($visit)) * 100, 2, '.', '') : 0;
                
                }else{
                        $query_type = '';
                        switch($type){
                                case 'unique_click' :
                                        $query_type = 'click';
                                        break;
                                case 'unique_visit' :
                                        $query_type = 'visit';
                                        break;
                                default:
                                        $query_type = $type;
                                        break;
                        }
                        $data = $this->db->select(
                                $this->tbl['statistic'], //TABLE
                                $this->tbl['statistic'].".ip_address",//COLUMN
                                Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.type> = '$query_type'  $query")
                        );
                        
                        return ($type == 'unique_click' || $type == 'unique_visit' ) ? count(array_unique($data)) : count($data);      
                }
        }
        
        public function get_chart_statistic_data($query, $page, $split, $range){
                $results = ['status' => 'fail', 'split' => []];
                $label = [];
                $data = [];
                $raw_data = $this->db->select(
                        $this->tbl['statistic'], //TABLE
                        [
                                "date" => ($range != 'today' && $range != 'yesterday') ? Medoo::raw("date(<puchi_statistic.date>)") :  Medoo::raw("hour(<puchi_statistic.date>)") ,
                                $this->tbl['statistic'].".ip_address",
                                $this->tbl['statistic'].".content_title",
                                $this->tbl['statistic'].".type",
                        ],
                        Medoo::raw("where <puchi_statistic.page_id> = $page and <puchi_statistic.split_id> = $split $query order by date desc") //CHANGE TO ASC LATER
                );
                
                if(is_array($raw_data) && !empty($raw_data)){
                        foreach($raw_data as $r => $v){
                                array_push($label, $v['date']);
                                $title = sanitize_title($v['content_title']);
                                $data[$title]['title'] = $v['content_title'];
                                $data[$title]['data'][$v['date']][$v['type']][] = $v['ip_address'];
                        }
                }
                
                $label = array_values(array_unique($label));
                sort($label);
                if(is_array($data) && !empty($data)){
                        foreach($data as $d => $v){
                                $results['split'][$d]['label'] = $v['title'];
                                $content_data = $v['data'];
                                foreach($label as $l){
                                        if(isset($content_data[$l])){
                                                $visit = (isset($content_data[$l]['visit'])) ? count($content_data[$l]['visit']) : 0;
                                                $click = (isset($content_data[$l]['click'])) ? count($content_data[$l]['click']) : 0;
                                                $results['split'][$d]['data']['page_view'][] = $visit;
                                                $results['split'][$d]['data']['conversion'][] = $click;
                                                $results['split'][$d]['data']['visitor'][] = (isset($content_data[$l]['visit']) && !empty($content_data[$l]['visit']) ) ?  count(array_unique($content_data[$l]['visit'])) : 0;
                                                $results['split'][$d]['data']['conversion_rate'][] = ($click != 0 && $visit != 0) ? number_format(($click / $visit) * 100, 2, '.', '') : 0;
                                                $results['split'][$d]['data']['bounce_rate'][] = ($visit != 0) ? number_format(($visit - $click) / $visit * 100, 2, '.', '') : 0;
                                        }else{
                                                $results['split'][$d]['data']['page_view'][] = 0;
                                                $results['split'][$d]['data']['conversion'][] = 0;
                                                $results['split'][$d]['data']['visitor'][] = 0;
                                                $results['split'][$d]['data']['conversion_rate'][] = 0;
                                                $results['split'][$d]['data']['bounce_rate'][] = 0;
                                        }
                                }
                        }
                        $results['status'] = 'ok';
                }
                $results['label'] = $label;
                return $results;
        }
        
}