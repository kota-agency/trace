<div class="hkb-grid">
    <div class="hkb-grid__col hkb-grid__12">

        <!-- start hkba-panel -->
        <div id="js-hkba-dash-views-panel" class="hkba-panel">

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Knowledge Base Searches vs Transfers Overview', 'ht-knowledge-base'); ?></h3>
            </div>

            <div id="js-hkba-dash-views-panel__content" class="hkba-panel__content hkb-dashbox--monthly-views" data-hkba="chart-kbviews-info">

                <div id="js-hkba-chart-kbviews-info">
                    <!-- error messages and/or legend goes here -->
                </div>

                <canvas id="js-hkba-chart-kbviews" data-hkba="chart-kbviews" data-nonce="<?php echo wp_create_nonce('monthlyKBOverviewChart'); ?>" height="550"></canvas>
            </div>

        </div>
        <!-- end hkba-panel -->

    </div>
</div>
<!-- /.hkb-grid -->

<div class="hkb-grid">
    <div class="hkb-grid__col hkb-grid__6">

        <!-- start hkba-panel -->
        <div id="js-hkba-dash-articles-panel" class="hkba-panel hkba-panel--equalheight hkb-dashboardfeedback">

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Article Feedback', 'ht-knowledge-base'); ?></h3>
            </div>

            <div id="js-hkba-dash-articles-panel__content" class="hkba-panel__content">

                <ul id="js-hkba-feedbacksummary" class="hkb-dashboardfeedback__stats" data-nonce="<?php echo wp_create_nonce('feedbackOverview'); ?>">
                    <li class="hkb-dashboardfeedback__statsgood">
                        <div class="hkb-dashboardfeedback__statswrap">
                            <div id="js-hkba-feedbacksummary-helpful" class="hkb-dashboardfeedback__value hkb-dashboardfeedback__good_value">-</div>
                            <div class="hkb-dashboardfeedback__label hkb-dashboardfeedback__good_label">
                                <?php _e('Voted Helpful', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                    <li class="hkb-dashboardfeedback__statsbad">
                        <div class="hkb-dashboardfeedback__statswrap">
                            <div id="js-hkba-feedbacksummary-unhelpful" class="hkb-dashboardfeedback__value hkb-dashboardfeedback__bad_value">-</div>
                            <div class="hkb-dashboardfeedback__label hkb-dashboardfeedback__bad_label">
                                <?php _e('Voted Unhelpful', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="hkb-dashboardfeedback__summary hkb-dashboardfeedback__summary--neutral">
                    <div class="hkb-dashboardfeedback__summaryfeedback">
                        <div class="hkb-dashboardfeedback__summaryvalue">
                            <span id="js-hkba-feedbacksummary-success">-</span>% <?php _e('Article Success', 'ht-knowledge-base'); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- end hkba-panel --> 


    </div>

    <div class="hkb-grid__col hkb-grid__6">

        <!-- start hkba-panel -->
        <div id="js-hkba-dash-search-panel" class="hkba-panel hkba-panel--equalheight hkb-dashboardsearcheffect">

            <div class="hkba-panel__header">
                <h3 class="hkba-panel__title"><?php _e('Search Effectiveness', 'ht-knowledge-base'); ?></h3>
            </div>
            
            <div id="js-hkba-dash-search-panel__content"  class="hkba-panel__content">
            
                <ul id="js-hkba-searchsummary" class="hkb-dashboardsearcheffect__stats" data-nonce="<?php echo wp_create_nonce('searchesOverview'); ?>">
                    <li class="hkb-dashboardsearcheffect__total">
                        <div class="hkb-dashboardsearcheffect__statswrap">
                            <div id="js-hkba-searchsummary-total" class="hkb-dashboardsearcheffect__value hkb-dashboardsearcheffect__total_value">-</div>
                            <div class="hkb-dashboardsearcheffect__label hkb-dashboardsearcheffect__total_label">
                                <?php _e('Total Searches', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                    <li class="hkb-dashboardsearcheffect__failed">
                        <div class="hkb-dashboardsearcheffect__statswrap">
                            <div id="js-hkba-searchsummary-failed" class="hkb-dashboardsearcheffect__value hkb-dashboardsearcheffect__failed_value">-</div>
                            <div class="hkb-dashboardsearcheffect__label hkb-dashboardsearcheffect__failed_label">
                                <?php _e('Failed Searches', 'ht-knowledge-base'); ?>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="hkb-dashboardsearcheffect__summary hkb-dashboardsearcheffect__summary--neutral">
                    <div class="hkb-dashboardsearcheffect__summaryfeedback">
                        <div class="hkb-dashboardsearcheffect__summaryvalue">
                            <span id="js-hkba-searchsummary-success" data-hkba="animatecounts">-</span>% <?php _e('Search Effectiveness', 'ht-knowledge-base'); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- end hkba-panel -->

    </div>

</div>
<!-- /.hkb-grid -->