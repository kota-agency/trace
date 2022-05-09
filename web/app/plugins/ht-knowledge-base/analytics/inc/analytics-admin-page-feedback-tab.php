<div id="js-hkba-feedback-overview-panel" class="hkba-panel hkb-feedbackbar__block" data-nonce="<?php echo wp_create_nonce('feedbackResponses'); ?>">
    <h2 class="hkb-feedbackbar__title"><span class="hkb-feedbackbar_count">0</span> <?php _e( 'Responses' , 'ht-knowledge-base' ); ?></h2>
    <div class="hkb-feedbackbar">
        <div class="hkb-feedbackbar_good" data-hkb-feedbacknum="" style="width: 50%">&nbsp;</div>
        <div class="hkb-feedbackbar_bad" data-hkb-feedbacknum="" style="width: 50%"> &nbsp;</div>
    </div>
</div>


<!-- new feedback section -->

<div class="hkba-feedbacktable">

  <div class="hkba-feedbacktable__filter">

    <!-- Feedback filter -->
    <div class="hkba-feedbackfilter">
      <div class="hkba-feedbackfilter__up active">
        <svg>
          <use xlink:href="#icon-feedbackup"></use>
        </svg>
      </div>
      <div class="hkba-feedbackfilter__down active">
        <svg>
          <use xlink:href="#icon-feedbackdown"></use>
        </svg>
      </div>
      
      <span><label><input type="checkbox"  class="hkba-feedbackfilter__comments" / autocomplete="off"><?php _e('With Feedback Only', 'ht-knowledge-base'); ?></label></span>
    </div>
    


  </div>

  <div class="hkba-feedbacktable__head">
    <div class="hkba-feedbacktable__rating"><?php _e('Rating', 'ht-knowledge-base'); ?></div>
    <div class="hkba-feedbacktable__article"><?php _e('Article', 'ht-knowledge-base'); ?></div>
    <div class="hkba-feedbacktable__feedback"><?php _e('Feedback', 'ht-knowledge-base'); ?></div>
    <div class="hkba-feedbacktable__author"><?php _e('Author', 'ht-knowledge-base'); ?></div>
    <div class="hkba-feedbacktable__date orderedby-asc"><?php _e('Date', 'ht-knowledge-base'); ?></div>
    <div class="hkba-feedbacktable__actions" id="js-hkba-feedbackactions" data-nonce="<?php echo wp_create_nonce('deleteFeedbackItem'); ?>"><?php _e('Actions', 'ht-knowledge-base'); ?></div>
  </div>
  <div id="js-hkba-feedbacktable" class="hkba-feedbacktable__body" data-nonce="<?php echo wp_create_nonce('feedbackItems'); ?>">
    <!-- underscore item(s) template loaded here -->
  </div>

  <div id="js-hkba-feedbackpagination" class="hkba-feedbacktable__pagination">
    <!-- underscore pagination template loaded here -->
  </div>

</div>