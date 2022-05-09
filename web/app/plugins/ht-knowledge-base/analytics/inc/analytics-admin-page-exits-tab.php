<div class="hkb-grid">

    <div class="hkb-grid__col hkb-grid__6">

        <div id="js-hkba-tranfers-split-panel" class="hkba-panel hkba-panel--equalheight"><!-- start hkba-panel -->

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Transfers Split %', 'ht-knowledge-base'); ?></h3>
            </div>

            <div class="hkba-panel__content hkba-panel__content--canvascontianer">
                
                <canvas id="js-hkba-chart-transferoverview" data-nonce="<?php echo wp_create_nonce('exitsDonut'); ?>" height="200"></canvas>
                <div id="js-hkba-chart-transferoverview-info">
                    <!-- error messages and/or legend goes here -->
                </div>

            </div>
        </div><!-- end hkba-panel -->

    </div><!-- end hkb-grid__col6 -->

    <div class="hkb-grid__col hkb-grid__6">

        <div id="js-hkba-tranfers-metrics-panel" class="hkba-panel hkba-panel--equalheight"><!-- start hkba-panel -->

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Transfer Overview', 'ht-knowledge-base'); ?></h3>
            </div>

            <div class="hkba-panel__content">

                <ul id="js-hkba-transfersummary" class="hkb-transfermetrics" data-nonce="<?php echo wp_create_nonce('exitsOverview'); ?>">
                    <li class="hkb-transfermetrics__totalviews">
                        <div class="hkb-transfermetrics__statswrap">
                            <div id="js-hkba-transfersummary-totalviews" class="hkb-transfermetrics__value" data-nonce="<?php echo wp_create_nonce('totalViewsStats'); ?>">-</div>
                            <div class="hkb-transfermetrics__label">
                                <?php _e('Total Views', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                    <li class="hkb-transfermetrics__totaltransers">
                        <div class="hkb-transfermetrics__statswrap">
                            <div id="js-hkba-transfersummary-totalexits" class="hkb-transfermetrics__value" data-nonce="<?php echo wp_create_nonce('totalExitsStats'); ?>">-</div>
                            <div class="hkb-transfermetrics__label">
                                <?php _e('Total Transfers', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="hkb-transfermetrics__summary">
                    <div class="hkb-transfermetrics__summaryfeedback">
                        <div class="hkb-transfermetrics__summaryvalue">
                            <span id="js-hkba-transfersummary-success" data-nonce="<?php echo wp_create_nonce('exitPercentageStats'); ?>">-</span>% <?php _e('Transfer Percentage', 'ht-knowledge-base'); ?>
                        </div>
                    </div>
                </div>
                
            </div>

        </div>

    </div><!-- end hkb-grid__col6 -->

</div><!-- end hkb-grid -->


<div class="hkb-grid">

        <div class="hkb-grid__col hkb-grid__6">

            <div id="js-hkba-tranfers-category-panel" class="hkba-panel" style="padding:0 0 20px;"><!-- start hkba-panel -->

                <div class="hkba-panel__content">
                    <table class="category-exits-result result responsive" data-nonce="<?php echo wp_create_nonce('categoryExits'); ?>" width="100%">
                        <thead>
                            <tr class="items">
                                <th class="">
                                    <?php  _e('Category Transfers','ht-knowledge-base'); ?> 
                                </th>
                                <th class="">
                                    <?php _e('Views', 'ht-knowledge-base'); ?>
                                </th>
                                <th class="">
                                    <?php _e('Transfers', 'ht-knowledge-base'); ?>
                                </th>
                                <th class="">
                                    <?php _e('Transfer %', 'ht-knowledge-base'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- category exits will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div><!-- end hkba-panel --> 

        </div><!-- hkb-grid__col grid__6 --> 

        <div class="hkb-grid__col hkb-grid__6">

            <div id="js-hkba-tranfers-article-panel" class="hkba-panel" style="padding:0 0 20px;"><!-- start hkba-panel -->

                <div class="hkba-panel__content">

                    <table class="article-exits-result result responsive" data-nonce="<?php echo wp_create_nonce('articleExits'); ?>" width="100%">
                        <thead>
                            <tr class="items">
                                <th class="">
                                    <?php  _e('Articles Transfers','ht-knowledge-base'); ?> 
                                </th>
                                <th class="">
                                    <?php _e('Views', 'ht-knowledge-base'); ?>
                                </th>
                                <th class="">
                                    <?php _e('Transfers', 'ht-knowledge-base'); ?>
                                </th>
                                <th class="">
                                    <?php _e('Transfer %', 'ht-knowledge-base'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- category exits will be populated here -->
                        </tbody>
                    </table>

                </div>

            </div><!-- end hkba-panel --> 

        </div><!-- hkb-grid__col grid__6 --> 

</div>