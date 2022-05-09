<div class="hkb-grid">
    <div class="hkb-grid__col hkb-grid__12">    
        <div id="hkba_null_searches" class="hkb-dashbox"><!-- start dashbox -->
            <div class="hkb-dashbox__header">
                <h3><span><?php _e('Article Views', 'ht-knowledge-base'); ?></span></h3>
            </div>

            <div class="hkb-dashbox__content">
                <table class="article-views-result result responsive" data-nonce="<?php echo wp_create_nonce('articleViews'); ?>" width="100%">
                    <thead>
                        <tr class="terms">
                            <th class="">
                                <?php  _e('Article','ht-knowledge-base'); ?> 
                            </th>
                            <th class="">
                                <?php _e('User', 'ht-knowledge-base'); ?>
                            </th>
                            <th class="">
                                <?php _e('Duration', 'ht-knowledge-base'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- feedback results will be populated here -->
                    </tbody>
                </table>
            </div>
        </div><!-- end dashbox --> 
    </div>
</div>

