<div class="wrap">
        <h2><?php _e('Settings','puchi');?></h2>
        <div class="pch-wrap">
                <div class="pch-setting-wrap">
                        <div class="pch-setting-item">
                                <h3><?php _e('Default Range Statistic Time','puchi');?></h3>
                                <select class="widefat pch-to-save" data-key="range">
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
                                        <?php foreach($range as $r => $v):?>
                                                <option <?php selected($selected, $r);?> value="<?php echo $r;?>"><?php echo $v;?></option>
                                        <?php endforeach;?>
                                </select>
                                <a href="#" class="button button-primary btn-save"><?php _e('Save','puchi');?></a>
                        </div><!-- end of och settings item -->
                        
                         <div class="pch-setting-item">
                                <h3><?php _e('White IP Address Lists','puchi');?></h3>
                                <textarea class="widefat pch-to-save" data-key="white_ip" placeholder="<?php _e('Separate each IP Address with commas (,)','puchi');?>"><?php
                                        echo (isset($setting['white_ip'])) ? $setting['white_ip'] : ''; 
                                ?></textarea>
                                <a href="#" class="button button-primary btn-save"><?php _e('Save','puchi');?></a>
                        </div><!-- end of och settings item -->
                </div><!-- end of och setting wrap -->
        </div><!-- end of pch wrap -->
</div><!-- end of wrap -->
<?php
        $get = wp_remote_get('https://finata.id/exfl-landing/perusahaan/?fetch=http://google.com',[
                'timeout' => 120, 'httpversion' => '1.1' 
        ]);
        echo '<pre>';
print_r($get);
echo '</pre>';
?>