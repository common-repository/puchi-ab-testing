<?php

namespace Puchi;

class Controller{
        
        private $model;
        private static $instance;
        
        public function __construct(){
                $this->model = \Puchi\Model::get_instance();
            
                //CREATE REST ROUTE
                add_action('rest_api_init', [$this, 'puchi_api']);
            
        }
        
        public static function get_instance(){
                if(!isset(self::$instance)) {
                        self::$instance = new Controller();
                }
                return self::$instance;
        }
        
        public function puchi_api(){
                $namespace = 'puchi/v1';
            
                $route = [
                        'add_split_click' => [
                                'method' => 'POST',
                                'args' => [
                                        'trigger' => ['required' => true],
                                        'data' => ['required' => true]
                                ]
                        ],
                        'get_page_statistic_data' => [
                                'method' => 'GET',
                                'args' => []
                        ],
                        'delete_page_statistic_data' => [
                                'method' => 'POST',
                                'args' => [
                                        'id' => ['required' => true]
                                ]
                        ],
                        'get_range_statistic_data' => [
                                'method' => 'POST',
                                'args' => [
                                        'type' => ['required' => true],
                                        'page' => ['required' => true],
                                        'split' => ['required' => true],
                                        'content' => ['required' => true]
                                ]
                        ],
                        'get_chart_statistic_data' => [
                                'method' => 'POST',
                                'args' => [
                                        'page' => ['required' => true],
                                        'split' => ['required' => true],
                                        'range' => ['required' => true],
                                        'custom' => ['required' => false]
                                ]
                        ],
                        'get_table_statistic_data' => [
                                'method' => 'POST',
                                'args' => [
                                        'page' => ['required' => true],
                                        'split' => ['required' => true],
                                        'range' => ['required' => true],
                                        'custom' => ['required' => false]
                                ]
                        ],
                        'set_settings' => [
                                'method' => 'POST',
                                'args' => [
                                        'key' => ['required' => true],
                                        'value' => ['required' => true]
                                ]
                        ]
                ];
            
                foreach ($route as $r => $v) {
                        register_rest_route($namespace, '/' . $r . '/', [
                                'methods' => $v['method'],
                                'callback' => [$this, $r],
                                'args' => $v['args']
                        ]);
                }
        }
        
        public function add_split_click($request){
                $params = $request->get_params();
                $data = json_decode(base64_decode($params['data']), true);
                $data['tracker'] = $params['trigger'];
                return $this->add_split_data($data, 'click');
        }
        
        public function add_split_data($data, $type = 'visit'){
                $data['date'] = current_time('Y-m-d H:i:s');
                $data['type'] = $type;
                
                if($type == 'visit'){
                        unset($data['tracker']);
                        $data['tracker'] = 'none';        
                }
                return $this->model->add_split_data($data);
        }
        
        public function get_page_statistic_data(){
                $results = [
                        'status' => 'fail',
                        'content' => '<tr><td colspan="7" style="text-align: center;">'.__('No data available','puchi').'</td></tr>'
                ];
                $data =  $this->model->get_page_statistic_data();
                if(is_array($data) && !empty($data)){
                        $results['content'] = $this->get_page_statistic_dom($data);
                        $results['status'] = 'ok';
                }
                
                return $results;
        }
        
        private function get_page_statistic_dom($data){
                ob_start();
                foreach($data as $d):
                        $page_view = count($d['visit']);
                        $visitors = count(array_unique($d['visit']));
                        $conversions = count($d['click']);
                        $conversion_rate = (count($d['click']) != 0 && count($d['visit']) != 0) ? number_format((count($d['click']) / count($d['visit'])) * 100, 2, '.', '') : 0;
                        $bounce_rate = ($page_view != 0) ? number_format(($page_view - $conversions) / $page_view * 100, 2, '.', '') : 0;
                ?>
                        <tr data-id="<?php echo $d['page_id'];?>">
                                <td><a href="<?php echo admin_url( "admin.php?page=puchi_statistic");?>&id=<?php echo $d['page_id'];?>"><?php echo $d['post_title'];?></a></td>
                                <td><?php echo $page_view;?></td>
                                <td><?php echo $visitors;?></td>
                                <td><?php echo $conversions;?></td>
                                <td><?php echo $conversion_rate;?>%</td>
                                <td><?php echo $bounce_rate;?>%</td>
                                <td class="action">
                                        <a href="<?php echo admin_url( "admin.php?page=puchi_statistic");?>&id=<?php echo $d['page_id'];?>"><?php _e('Detail','puchi');?></a>
                                        <a class="delete" href="<?php echo $d['page_id'];?>"><?php _e('Delete','puchi');?></a>
                                </td>
                        </tr>
                <?php endforeach;
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
        }
        
        public function delete_page_statistic_data($request){
                $params = $request->get_params();
                return $this->model->delete_page_statistic_data($params['id']);
        }
        
        private function get_table_time_range($range){
                 $time_range = [
                        "today" => [
                                "current" => " and date(<puchi_statistic.date>) = date(now())",
                                "compare" => " and date(<puchi_statistic.date>) = date(date_sub(now(), interval 1 day))"
                        ],
                        "yesterday" => [
                                "current" => " and date(<puchi_statistic.date>) = date(date_sub(now(), interval 1 day))",
                                "compare" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 3 day) and date_sub(curdate(),interval 2 day)"
                        ],
                        "7day" => [
                                "current" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 7 day) and curdate()",
                                "compare" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 15 day) and date_sub(curdate(),interval 8 day)"
                        ],
                        "30day" => [
                                "current" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 30 day) and curdate()",
                                "compare" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 61 day) and date_sub(curdate(),interval 31 day)"
                        ],
                        "90day" => [
                                "current" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 90 day) and curdate()",
                                "compare" => "  and date(<puchi_statistic.date>) between date_sub(curdate(),interval 181 day) and date_sub(curdate(),interval 91 day)"
                        ],
                        "180day" => [
                                "current" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 181 day) and curdate()",
                                "compare" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 365 day) and date_sub(curdate(),interval 182 day)"
                        ],
                        "365day" => [
                                "current" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 365 day) and curdate()",
                                "compare" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 731 day) and date_sub(curdate(),interval 366 day)"
                        ]
                ];
                return ($range != 'all_time') ? [$time_range[$range]] : $time_range;
        }
        
        private function get_statistic_table_data($range, $type, $page, $split, $content){
                $data = [];
                foreach($range as $t => $v){
                        $current = $this->model->get_statistic_table_data($v['current'], $type, $page, $split, $content);
                        $current = ($current == NULL) ? 0 : $current;
                        $data[$t] = [
                                "current" => $current
                        ];
                        $compare = $this->model->get_statistic_table_data($v['compare'], $type, $page, $split, $content);
                        $compare = ($compare == NULL) ? 0 : $compare;
                        $data = $this->get_statistic_compared_table_data($data, $t, $current, $compare);
                }
                return $data;
        }
        
         private function get_statistic_compared_table_data($data, $index, $current, $compare){
                $stats = "";
                if((float)$current > (float)$compare){
                        $stats = 'up';
                }else if((float)$current < (float)$compare){
                     $stats = 'down';   
                }else{
                        $stats = 'equal';
                }
                $percentage = ($compare != 0 && $current != 0) ? ((float)$current / (float)$compare ) * 100  - 100 : 100;
                $percentage = ($current == 0) ? '-'.$percentage.'%' : number_format((float)$percentage, 2, '.', '') . '%';
                $data[$index]['compare'] = $compare;
                $data[$index]['stats'] = $stats;
                $data[$index]['percent'] = ($stats != 'equal') ? $percentage : '0.00%';
                
                return $data;
        }
        
        public function get_range_statistic_data($request){
                $params = $request->get_params();
                $result = [
                        'status' => 'fail'     
                ];
                $data = $this->get_statistic_table_data($this->get_table_time_range('all_time'),$params['type'], $params['page'], $params['split'], $params['content']);
                if(is_array($data) && !empty($data)):
                        ob_start();
                        $title = [
                                'today' => __('Today','puchi'),
                                'yesterday' => __('Yesterday','puchi'),
                                '7day' => __('7 Days Ago','puchi'),
                                '30day' => __('1 Month Ago','puchi'),
                                '90day' => __('3 Months Ago','puchi'),
                                '180day' => __('6 Months Ago','puchi'),
                                '365day' => __('12 Months Ago','puchi')
                        ];
                        foreach($data as $d => $v):
                        ?>
                                <div class="pch-item">
                                        <div class="layer">
                                                <h3><?php echo $title[$d];?></h3>
                                                <div class="info">
                                                        <p><strong class="value">
                                                                <?php echo $v['current'];?><?php echo ($params['type'] == 'conversion_rate' || $params['type'] == 'bounce_rate') ? '%' : '';?>
                                                        </strong></p>
                                                        <span class="arrow-<?php echo $v['stats'];?>">
                                                                <i class="puchicon-arrow_drop_<?php echo $v['stats'];?>"></i><b><?php echo $v['percent'];?></b>
                                                        </span>
                                                </div><!-- end of info -->
                                        </div><!-- end of layer -->
                                </div><!-- end of pch item -->
                        <?php
                        endforeach;
                        $result['status'] = 'ok';
                        $result['content'] = ob_get_contents();
                        ob_end_clean();
                else:
                        $result['content'] = '<div class="pch-table-stat-empty"><p>'. __('No data available', 'puchi').'</p></div>';
                endif;
                return $result;
        }
        public function get_split_from_page($page_id){
                return $this->model->get_split_from_page($page_id);
        }
        public function get_content_from_split($page_id, $split_id){
                return $this->model->get_content_from_split($page_id, $split_id);
        }
        public function delete_statistic_data($post_id){
                return $this->model->delete_statistic_data($post_id);
        }
        
        private function get_chart_time_range($range, $custom = ""){
                 $time_range = [
                        "today" => " and date(<puchi_statistic.date>) = date(now())",
                        "yesterday" => " and date(<puchi_statistic.date>) = date(date_sub(now(), interval 1 day))",
                        "last_seven_days" => " and date(<puchi_statistic.date>) between date_sub(curdate(),interval 7 day) and curdate()",
                        "this_week" => " and yearweek(<puchi_statistic.date>, 1) = yearweek( curdate(), 1)",
                        "last_week" => " and yearweek(<puchi_statistic.date>, 1) = yearweek( curdate() - interval 1 week, 1)",
                        "this_month" => " and year(<puchi_statistic.date>) = year(current_date ) and month(<puchi_statistic.date>) = month(current_date )",
                        "last_month" => " and year(<puchi_statistic.date>) = year(current_date - interval 1 month) and month(<puchi_statistic.date>) = month(current_date - interval 1 month)",
                        "this_year" => " and year(<puchi_statistic.date>) = year(now())",
                        "last_year" => " and year(<puchi_statistic.date>) = year(now() - interval 1 year)",
                ];
                 if(!empty($custom) && $range == 'custom'){
                        $time_range["custom"] = " and date(<puchi_statistic.date>) >= date('$custom[0]') and date(<puchi_statistic.date>) <= date('$custom[1]')";
                 }
                
                return ($range != 'all_time') ? $time_range[$range] : " ";
        }
        
        public function get_chart_statistic_data($request){
                $params = $request->get_params();
                $page_id = $params['page'];
                $split_id = $params['split'];
                $range = $params['range'];
                $custom = (isset($params['custom'])) ? $params['custom'] : '';
                return $this->model->get_chart_statistic_data($this->get_chart_time_range($range, $custom), $page_id,$split_id, $range);
        }
        
        public function get_table_statistic_data($request){
                $params = $request->get_params();
                $data = [];
                $results = ['status' => 'fail'];
                $page_id = $params['page'];
                $split_id = $params['split'];
                $range = $params['range'];
                $custom = (isset($params['custom'])) ? $params['custom'] : '';
                $type = [
                        'visit',
                        'unique_visit',
                        'click',
                        'conversion_rate',
                        'bounce_rate'
                ];
                $content = $this->get_content_from_split($page_id, $split_id);
                if(is_array($content) && !empty($content)){
                        foreach($content as $c => $v){
                                $data[sanitize_title($v['content_title'])]['title'] = $v['content_title'];
                                $data[sanitize_title($v['content_title'])]['weight'] = $v['weight'] .'%';
                                foreach($type as $t){
                                        $data_type = $this->model->get_statistic_table_data($this->get_chart_time_range($range, $custom), $t, $page_id, $split_id, $v['content_title']);
                                        $data_type = ($t == 'conversion_rate' || $t == 'bounce_rate') ? $data_type.'%' : $data_type;
                                        $data[sanitize_title($v['content_title'])][$t] = $data_type;
                                }
                        }
                        $results['status'] = 'ok';
                        $results['content'] = $this->get_table_statistic_dom($data);
                }
                return $results;
        }
        
        private function get_table_statistic_dom($data){
                ob_start();
                if(is_array($data) && !empty($data)){
                        $max = max(array_column($data, 'conversion_rate'));
                        foreach($data as $d):?>
                               <tr <?php echo ($d['conversion_rate'] == $max) ? 'class="biggest"' : '';?>>
                                       <td><?php echo $d['title'];?></td>
                                       <td><?php echo $d['weight'];?></td>
                                       <td><?php echo $d['visit'];?></td>
                                       <td><?php echo $d['unique_visit'];?></td>
                                       <td><?php echo $d['click'];?></td>
                                       <td><?php echo $d['conversion_rate'];?></td>
                                       <td><?php echo $d['bounce_rate'];?></td>
                                       <td><i class="dashicons dashicons-star-filled"></i></td>
                               </tr>
                        <?php endforeach;
                }
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
        }
        
        public function set_settings($request){
                $params = $request->get_params();
                $key = $params['key'];
                $value = $params['value'];
                $settings = get_option('puchi_settings', []);
                $settings[$key] = $value;
                update_option('puchi_settings', $settings);
                return [
                        'title' => __('Info', 'puchi'),
                        'content' => __('Settings sucessfully saved!', 'puchi')
                ];
        }
}