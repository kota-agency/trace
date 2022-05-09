<?php 
    global $wpdb; 
    $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ) : '';
    $analytics_history_days_limit = apply_filters('hkb_analytics_history_days_limit', 3650);
?>

<div id="hkba-admin-container">
<div class="hkba-container">

    <div class="hkba-header">

        <h1 class="hkba-header__title"><?php _e('Analytics', 'ht-knowledge-base'); ?></h1>

        <div>
            <ul class="hkba-period">
                <?php if($analytics_history_days_limit>999): ?>
                    <li>
                        <a id="hkb-period__all-time-btn" href="#"><?php _e('All Time', 'ht-knowledge-base'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if($analytics_history_days_limit>366): ?>
                    <li>
                        <a id="hkb-period__last-12-months-btn"  href="#"><?php _e('Last 12 Months', 'ht-knowledge-base'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if($analytics_history_days_limit>89): ?>
                    <li>
                        <a id="hkb-period__last-90-days-btn" href="#"><?php _e('Last 90 Days', 'ht-knowledge-base'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if($analytics_history_days_limit>29): ?>
                    <li>
                        <a id="hkb-period__last-30-days-btn" href="#"><?php _e('Last 30 Days', 'ht-knowledge-base'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if($analytics_history_days_limit>6): ?>
                    <li>
                        <a id="hkb-period__last-7-days-btn" href="#"><?php _e('Last 7 Days', 'ht-knowledge-base'); ?></a>
                    </li>
                <?php endif; ?>
                <?php if($analytics_history_days_limit>999): ?>
                    <li>
                        <a id="hkb-period__custom-period-btn" href="#" data-toggle="dropdown"><?php _e('Custom Period', 'ht-knowledge-base'); ?></a>
                        <ul id="hkb-period__custom-period-ul" data-ht-visibility="false">
                            <li>
                                <span class="hkb-analyticsdate__inputwrap"><input type="text" id="filterDate1" name="filterDate1" class="datepicker" /></span>
                                    <span class="hkb-analyticsdate__div">-</span>
                                <span class="hkb-analyticsdate__inputwrap"><input type="text" id="filterDate2" name="filterDate2" class="datepicker" /></span>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="hkb-analyticsdate" data-nonce="<?php echo wp_create_nonce('updateUserMetaDates'); ?>" style="display: none;">
            <span class="hkb-analyticsdate__inputwrap"><input type="text" id="filterDate1" name="filterDate1" class="datepicker" /></span>
                <span class="hkb-analyticsdate__div">-</span>
            <span class="hkb-analyticsdate__inputwrap"><input type="text" id="filterDate2" name="filterDate2" class="datepicker" /></span>
        </div>

    </div>

    <ul class="hkba-nav">
        <li class="<?php if(empty($tab)||'dashboard'==$tab) echo 'active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=dashboard'); ?>">
                <?php _e('Dashboard', 'ht-knowledge-base'); ?>
            </a>
        </li>
        <!--
        <li class="<?php if(!empty($tab)&&'articles'==$tab) echo 'active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=articles'); ?>">
                <?php _e('Articles', 'ht-knowledge-base'); ?>
            </a>
        </li>
        -->
        <li class="<?php if(!empty($tab)&&'feedback'==$tab) echo 'active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=feedback'); ?>">
                <?php _e('Feedback', 'ht-knowledge-base'); ?>
            </a>
        </li>
        <li class="<?php if(!empty($tab)&&'search'==$tab) echo 'active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=search'); ?>">
                <?php _e('Search', 'ht-knowledge-base'); ?>
            </a>
        </li>
        <li class="<?php if(!empty($tab)&&'exits'==$tab) echo 'active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=exits'); ?>">
                <?php _e('Transfers', 'ht-knowledge-base'); ?>
            </a>
        </li>
        <!--
        <li class="nav-tab <?php if(!empty($tab)&&'authors'==$tab) echo 'hkba-nav__tab--active'; ?>">
            <a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=hkb-analytics&tab=authors'); ?>">
                <?php _e('Authors', 'ht-knowledge-base'); ?>
            </a>
        </li>
        -->
    </ul>

    <div class="hkba-content">

        <?php switch ($tab) {
                case 'articles':
                    include_once('analytics-admin-page-articles-tab.php');
                    break;
                case 'feedback':
                    include_once('underscores-analytics-templates.php');
                    include_once('analytics-admin-page-feedback-tab.php');
                    break;
                case 'search':
                    include_once('analytics-admin-page-search-tab.php');
                    break;
                case 'authors':
                    include_once('analytics-admin-page-authors-tab.php');
                    break;
                case 'exits':
                    include_once('analytics-admin-page-exits-tab.php');
                    break;
                case 'dashboard':
                default:
                    include_once('analytics-admin-page-dash-tab.php');
                    break;
            } ?>

    </div>
    <!-- end hkb-analyticsdash -->

    <div class="hkba-footer">
        <?php printf( __('Version %s', 'ht-knowledge-base'), HT_KB_VERSION_NUMBER ); ?>
    </div>

    <!-- SVG include -->
    <?php include_once('analytics-admin-svgs.php'); ?>

</div>
</div>
<!-- end wrap -->