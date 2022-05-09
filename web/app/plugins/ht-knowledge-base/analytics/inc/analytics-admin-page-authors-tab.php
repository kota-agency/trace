<div class="hkb-grid">
    <div class="hkb-grid__col hkb-grid__12">    
        <div id="hkba_null_searches" class="hkb-dashbox"><!-- start dashbox -->
            <div class="hkb-dashbox__header">
                <h3><span><?php _e('Author Rankings', 'ht-knowledge-base'); ?></span></h3>
            </div>

            <div class="hkb-dashbox__content">
                <table class="author-rankings-result result responsive" data-nonce="<?php echo wp_create_nonce('authorStats'); ?>" width="100%">
                    <thead>
                        <tr class="terms">
                            <th class="">
                                <?php  _e('Author','ht-knowledge-base'); ?> 
                            </th>
                            <th class="">
                                <?php _e('Articles Published with votes', 'ht-knowledge-base'); ?>
                            </th>
                            <th class="">
                                <?php _e('Score', 'ht-knowledge-base'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- restults will be populated here -->
                    </tbody>
                </table>
            </div>
        </div><!-- end dashbox --> 
    </div>
</div>

