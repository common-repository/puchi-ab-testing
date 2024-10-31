<div class="wrap">
        <?php
                $page_id = (isset($_GET['id']) && $_GET['id'] != '' ) ? $_GET['id'] : '';
        ?>
        <h2><?php _e('Statistic','puchi');?> <?php echo ($page_id != '') ? ': '. get_the_title($page_id) : '';?></h2>
        <div class="pch-wrap">
                <?php if($page_id != ''):
                        require_once(plugin_dir_path(__FILE__) . 'statistic-detail.php');
                else:?>
                        <div class="pch-panel">
                                <?php
                                        $data = [
                                                'order' =>  [0, 'asc'],
                                                'columnDefs' => [
                                                        [
                                                                'orderable' => false,
                                                                'targets' => [6]
                                                        ]
                                                ]
                                        ];
                                ?>
                                <div id="pch-stat-page-tbl" class="pch-tbl" data-table='<?php echo wp_json_encode($data);?>'>
                                        <table>
                                                <thead>
                                                        <tr>
                                                                <th><?php _e('Page/Post Title','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Page View','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Visitors','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Conversions','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Conversion Rate','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Bounce Rate','puchi');?> <a href="#"></a></th>
                                                                <th><?php _e('Action','puchi');?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <tr><td colspan="7" style="text-align: center;"><span class="pch-loading"></span></div></td></tr>
                                                </tbody>
                                        </table>
                                </div><!-- end of pch tbl -->
                        </div><!-- end of pch panel -->
                <?php endif;?>
        </div><!-- end of pch wrap -->
        
</div><!-- end of wrap -->