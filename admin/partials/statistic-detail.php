<?php
        $page_id = (isset($_GET['id']) && $_GET['id'] != '' ) ? $_GET['id'] : '';
        if($page_id != ''):
                $split = pch_controller()->get_split_from_page($page_id);
                if(is_array($split) && !empty($split)):
                        $split_id = (isset($_GET['split_id']) && $_GET['split_id'] != '') ? $_GET['split_id'] : $split[0]['split_id'];
                        $split_content = pch_controller()->get_content_from_split($page_id, $split_id);
                        $data_tabel = [
                                'page' => $page_id,
                                'type' => 'visit',
                                'split' => $split_id,
                                'content'  => 'all_split_content'
                        ];
                ?>
                <div class="pch-split-select pch-color-head">
                         <div class="pch-panel">
                                <div class="pch-widget-head">
                                        <h2><?php _e('Split Test', 'puchi');?></h2>
                                        <div class="util">
                                                <div class="pch-dropselect pch-split-test">
                                                        <span><b><?php echo get_the_title($split_id);?></b><i class="puchicon-arrow_drop_down"></i></span>
                                                        <div class="dropholder">
                                                                <ul>
                                                                        <?php if(is_array($split) && !empty($split)):?>
                                                                                <li>
                                                                                        <div class="split-holder">
                                                                                                <input type="text" value="" placeholder="<?php _e('Input split test title','puchi');?>" />
                                                                                                <i class="puchicon-close-outline hidden"></i>
                                                                                                <ul>
                                                                                                        <?php foreach($split as $s):?>
                                                                                                                <li <?php echo ($split_id == $s['split_id']) ? 'class="active"' : '';?> data-filter="<?php echo strtolower(get_the_title($s['split_id']));?>">
                                                                                                                        <a href="<?php echo admin_url( "admin.php?page=puchi_statistic");?>&id=<?php echo  $page_id;?>&split_id=<?php echo $s['split_id'];?>">
                                                                                                                                <?php echo get_the_title($s['split_id']);?>
                                                                                                                        </a>
                                                                                                                </li>
                                                                                                        <?php endforeach;?>
                                                                                                </ul>
                                                                                        </div><!-- end of split holder -->
                                                                                </li> 
                                                                        <?php endif;?>
                                                                </ul>
                                                        </div><!-- end of dropholder -->
                                                </div><!-- end of pch drop -->
                                        </div>
                                </div><!-- end of pch widget head -->
                         </div>
                </div><!-- end of pch panel -->
                
                <div class="pch-range-widget" data-table='<?php echo wp_json_encode($data_tabel);?>'>
                        <div class="pch-panel">
                                <div class="pch-widget-head">
                                        <h2 class="pch-upper"><?php _e('Page View','puchi');?></h2>
                                        <div class="util">
                                                <?php if(is_array($split_content) && !empty($split_content)):?>
                                                        <div class="pch-dropselect pch-split-content">
                                                                <span><b><?php _e('All Split Content','puchi');?></b><i class="puchicon-arrow_drop_down"></i></span>
                                                                <div class="dropholder">
                                                                        <ul>
                                                                                <li class="active"><a href="#" data-split-content="all_split_content"><?php _e('All Split Content','puchi');?></a></li>
                                                                                <li>
                                                                                        <div class="split-holder">
                                                                                                <input type="text" value="" placeholder="<?php _e('Input split content title','puchi');?>" />
                                                                                                <i class="puchicon-close-outline hidden"></i>
                                                                                                <ul>
                                                                                                        <?php foreach($split_content as $s => $v):?>
                                                                                                                <li data-filter="<?php echo strtolower(get_the_title($v['content_title']));?>">
                                                                                                                        <a href="#" data-split-content="<?php echo $v['content_title'];?>"><?php echo $v['content_title'];?></a>
                                                                                                                </li>
                                                                                                        <?php endforeach;?>
                                                                                                </ul>
                                                                                        </div><!-- end of split holder -->
                                                                                </li> 
                                                                        </ul>
                                                                </div><!-- end of dropholder -->
                                                        </div><!-- end of pch drop -->
                                                <?php endif;?>
                                                <div class="pch-dropselect pch-stat-type">
                                                        <span><b><?php _e('Page View','puchi');?></b><i class="puchicon-arrow_drop_down"></i></span>
                                                        <div class="dropholder">
                                                                <ul>
                                                                        <li class="active"><a href="#" data-stat="visit"><?php _e('Page View','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="unique_visit"><?php _e('Visitors','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="click"><?php _e('Conversion','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="conversion_rate"><?php _e('Conversion Rate','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="bounce_rate"><?php _e('Bounce Rate','puchi');?></a></li>
                                                                </ul>
                                                        </div><!-- end of drop select -->
                                                </div><!-- end of pch drop -->
                                        </div><!-- end of util -->
                                </div><!-- end of pch widget head -->
                                <div class="pch-widget-body fetching"></div><!-- end of pch widget body -->
                        </div><!-- end of pch panel -->
                </div><!-- end of pch range widget -->
                <div class="pch-spacer"></div>
                <div class="pch-range-select  pch-color-head">
                         <div class="pch-panel">
                                <div class="pch-widget-head">
                                        <h2><?php _e('Time Range', 'puchi');?></h2>
                                        <div class="util">
                                                <div class="custom-range">
                                                        <fieldset>
                                                                <label><?php _e('From','puchi');?>:</label>
                                                                <input type="text" value="" class="pch-datepicker" name="from"/>
                                                                <i class="dashicons dashicons-calendar"></i>
                                                        </fieldset>
                                                        <fieldset>
                                                                <label><?php _e('To','puchi');?>:</label>
                                                                <input type="text" value="" class="pch-datepicker" name="to"/>
                                                                <i class="dashicons dashicons-calendar"></i>
                                                        </fieldset>
                                                </div><!-- end of custom range -->
                                                <?php
                                                        $setting = get_option('puchi_settings', []);
                                                        $selected = (isset($setting['range'])) ? $setting['range'] : 'all_time';
                                                        $range = [
                                                                'today' => __('Today', 'puchi'),
                                                                'yesterday' =>  __('Yesterday', 'puchi'),
                                                                'last_seven_days' =>  __('Last 7 Days', 'puchi'),
                                                                'this_week' =>  __('This Week', 'puchi'),
                                                                'last_week' =>  __('Last Week', 'puchi'),
                                                                'this_month' =>  __('This Month', 'puchi'),
                                                                'last_month' =>  __('Last Month', 'puchi'),
                                                                'this_month' =>  __('This Month', 'puchi'),
                                                                'last_month' =>  __('Last Month', 'puchi'),
                                                                'this_year' =>  __('This Year', 'puchi'),
                                                                'last_year' =>  __('Last Year', 'puchi'),
                                                                'all_time' =>  __('All Time', 'puchi'),
                                                                'custom' =>  __('Custom', 'puchi')
                                                        ];
                                                ?>
                                                <div class="pch-dropselect pch-chart-range">
                                                        <span><b><?php echo $range[$selected];?></b><i class="puchicon-arrow_drop_down"></i></span>
                                                        <div class="dropholder">
                                                                <ul>
                                                                        <?php foreach($range as $r => $v):?>
                                                                                <li <?php echo ($selected == $r) ? 'class="active"' : '';?>><a href="#" data-range="<?php echo $r;?>"><?php echo $v;?></a></li>
                                                                        <?php endforeach;?>
                                                                </ul>
                                                        </div><!-- end of dropholder -->
                                                </div><!-- end of pch drop -->
                                        </div>
                                </div><!-- end of pch widget head -->
                         </div>
                </div><!-- end of pch panel -->
                <?php
                        $config = [
                                'type' => 'page_view',
                                'page' => $page_id,
                                'split' => $split_id,
                                'range' => $selected,
                                'custom' => ''
                        ];
                ?>
        
                <div class="pch-chart-widget" data-chart='<?php echo wp_json_encode($config);?>'>
                        <div class="pch-panel">
                                <div class="pch-widget-head">
                                        <h2 class="pch-upper"><?php _e('Page View','puchi');?></h2>
                                        <div class="util">
                                                <div class="pch-dropselect pch-chart-type">
                                                        <span><b><?php _e('Page View','puchi');?></b><i class="puchicon-arrow_drop_down"></i></span>
                                                        <div class="dropholder">
                                                                <ul>
                                                                        <li class="active"><a href="#" data-stat="page_view"><?php _e('Page View','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="visitor"><?php _e('Visitors','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="conversion"><?php _e('Conversion','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="conversion_rate"><?php _e('Conversion Rate','puchi');?></a></li>
                                                                        <li><a href="#" data-stat="bounce_rate"><?php _e('Bounce Rate','puchi');?></a></li>
                                                                </ul>
                                                        </div><!-- end of drop select -->
                                                </div><!-- end of pch drop -->
                                        </div><!-- end of util -->
                                </div><!-- end of pch widget head -->
                                <div class="pch-widget-body fetching">
                                        <div class="no-data"><p><?php _e('No data available','puchi');?></p></div>
                                        <canvas id="pch-chart"></canvas>
                                </div><!-- end of pch widget body -->
                        </div><!-- end of pch panel -->
                </div><!-- end of pch range widget -->
                
                <div class="pch-table-widget">
                        <div class="pch-panel">
                                <div class="pch-widget-body">
                                         <?php
                                                $data = [
                                                        'order' =>  [0, 'asc'],
                                                        'columnDefs' => [
                                                                [
                                                                        'orderable' => false,
                                                                        'targets' => [7]
                                                                ]
                                                        ]
                                                ];
                                        ?>
                                        <div id="pch-stat-content-tbl" class="pch-tbl" data-table='<?php echo wp_json_encode($data);?>'>
                                                <table>
                                                        <thead>
                                                                <tr>
                                                                        <th><?php _e('Split Content','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Weight(%)','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Page View','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Visitors','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Conversions','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Conversion Rate','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Bounce Rate','puchi');?> <a href="#"></a></th>
                                                                        <th><?php _e('Info','puchi');?></th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                               <tr><td colspan="8" style="text-align: center;"><span class="pch-loading"></span></div></td></tr>
                                                        </tbody>
                                                </table>
                                        </div><!-- end of pch tbl -->
                                </div><!-- end of pch widget body -->
                        </div><!-- end of pch panel -->
                </div><!-- end of pch table widget -->
        <?php endif;?>
<?php endif;?>