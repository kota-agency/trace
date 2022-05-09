<div class="hkb-grid">
    <div class="hkb-grid__col hkb-grid__4">

        <div id="js-hkba-search-overview-panel" class="hkba-panel hkba-panel--equalheight"><!-- start hkba-panel -->

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Search Overview', 'ht-knowledge-base'); ?></h3>
            </div>

            <div class="hkba-panel__content hkba-panel__content--canvascontianer">

                <canvas id="js-hkba-chart-searchoverview" data-nonce="<?php echo wp_create_nonce('searchDonut'); ?>" height="180"></canvas>

                <div id="js-hkba-chart-searchoverview-info">
                    <!-- error messages and/or legend goes here -->
                </div>

            </div>

        </div><!-- end hkba-panel -->
    </div><!-- end hkb-grid__col -->

    <div class="hkb-grid__col hkb-grid__8">

        <div id="js-hkba-search-period-panel" class="hkba-panel hkba-panel--equalheight"><!-- start hkba-panel -->
            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Searches This Period', 'ht-knowledge-base'); ?></h3>
            </div>

            <div class="hkba-panel__content hkb-dashbox--search-monthly">
                
                <canvas id="js-hkba-chart-kbsearches" data-nonce="<?php echo wp_create_nonce('monthlySearchesChart'); ?>" height="200"></canvas>
                <div id="js-hkba-chart-kbsearches-info">
                    <!-- error messages and/or legend goes here -->
                </div>

            </div>

        </div><!-- end hkba-panel -->
    </div><!-- end hkb-grid__col -->
</div><!-- end hkb-grid -->

<div class="hkb-grid">

    <div class="hkb-grid__col hkb-grid__6">
        <div id="js-hkba-search-null-panel" class="hkba-panel" style="padding:0 0 20px;"><!-- start hkba-panel -->

            <div class="hkba-panel__content">
                <table class="null-searches-result result responsive" data-nonce="<?php echo wp_create_nonce('nullSearches'); ?>" width="100%">
                    <thead>
                        <tr class="terms">
                            <th>
                                <?php  _e('Null Searches','ht-knowledge-base'); ?> 
                            </th>
                            <th class="count">
                                <?php _e('Count', 'ht-knowledge-base'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- null searches will be populated here -->
                    </tbody>
                </table>
            </div>
        </div><!-- end hkba-panel -->
    </div><!-- end hkb-grid__col -->

    <div class="hkb-grid__col hkb-grid__6">
        <div id="js-hkba-search-popular-panel" class="hkba-panel" style="padding:0 0 20px;"><!-- start hkba-panel -->

            <div class="hkba-panel__content">
                <table class="top-searches-result result responsive" data-nonce="<?php echo wp_create_nonce('topSearches'); ?>" width="100%">
                    <thead>
                        <tr class="terms">
                            <th>
                                <?php  _e('Popular Searches','ht-knowledge-base'); ?> 
                            </th>
                            <th class="count">
                                <?php _e('Count', 'ht-knowledge-base'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- top searches will be populated here -->
                    </tbody>
                </table>
            </div>
        </div><!-- end hkba-panel -->
    </div>
</div><!-- end hkb-grid -->

