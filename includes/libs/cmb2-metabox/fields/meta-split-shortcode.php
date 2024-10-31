<?php
//CREATE METABOX FOR SHORTCODE
add_action('add_meta_boxes', 'puchi_split_shortcode_metabox');

function puchi_split_shortcode_metabox(){
        add_meta_box(
                'puchi_split_shortcode_metabox', // $id
                __('Split Test Shortcode', 'puchi'),
                'puchi_split_shortcode_view', //callback
                'puchi-split-test', // $page
                'side', // $context
                'high'); // $priority
}

function puchi_split_shortcode_view(){
        global $post;
        $post_id = $post->ID;
        $title = get_the_title($post_id);
        if($title && $title != "Auto Draft"):
                $shortcode = ($post_id != '')  ? '[puchi id="'.$post_id.'" title="'.$title.'"]': '';
        ?>
        <div class="meta-wrap-split-shortcode">
                <label><?php _e('Copy this shortcode and paste to your page','puchi');?></label>
                <p>
                        <input type="text" class="widefat" name="puchi_split_shortcode" id="puchi_split_shortcode" value='<?php echo $shortcode;?>' readonly />
                </p>
        </div>
        <?php else:?>
                <p><?php _e('You need to add content and publish or save as draft first to generate the shortcode for this split.','puchi');?></p>
        <?php endif;
}