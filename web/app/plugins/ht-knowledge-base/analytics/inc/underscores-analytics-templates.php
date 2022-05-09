<!-- Feedback Card Template -->
<script type="text/html" id="ht-analytics-card-template">

        <div class="hkba-panel hkb-feedbackcard hkb-feedbackcard--<%= feedback.rating %>">

            <header class="hkb-feedbackcard__header">
                <a href="<%= feedback.articleEditUrl %>" class="hkb-feedbackcard__articletitle"><%= feedback.articleTitle %></a>
            </header>

            <div class="hkb-feedbackcard__content">
                <%= feedback.fullFeedback %>
            </div>    

            <footer class="hkb-feedbackcard__footer"> 
                <div class="hkb-feedbackcard__avatar">
                    <%= feedback.authorImg %>
                </div>
                <div class="hkb-feedbackcard__author">
                    <%= feedback.authorName %>
                </div> 
                <div class="hkb-feedbackcard__time">
                    <%= feedback.datetime %>
                </div>
            </footer>
        </div>

</script>

<script type="text/html" id="ht-analytics-card-nofeedback">

        <div class="hkba-nofeedback">
            <h3 class="hkba-nofeedback__title">
                <?php _e('No comments', 'ht-knowledge-base'); ?>
            </h3>

            <div class="hkba-nofeedback__content">
                    <?php _e('No one has left any comments for this period, please try selecting another date range.', 'ht-knowledge-base'); ?>
            </div>    
        </div>

</script>


<!-- Feedback Row Item template -->
<script type="text/html" id="ht-analytics-item-template">

    <div id="hkba-feedback-item__<%= feedback.feedbackID %>" data-feedback-id="<%= feedback.feedbackID %>" class="hkba-feedback-item">
        <div class="hkba-feedback-item__rating">            
            <svg>
                <% if ('helpful' == feedback.rating ) { %>
                    <use xlink:href="#icon-feedbackup"></use>
                <% } else { %>
                    <use xlink:href="#icon-feedbackdown"></use>
                <% } %>
            </svg>
            
        </div>
        <div class="hkba-feedback-item__article">
            <a href="<%= feedback.articleViewUrl %>" target="_blank"><%= feedback.articleTitle %>
                <svg>
                    <use xlink:href="#icon-link"></use>
                </svg>
            </a>
        </div>
        <div class="hkba-feedback-item__feedback" title="<%= feedback.fullFeedback %>"><%= feedback.snippet %></div>
        <div class="hkba-feedback-item__author"><%= feedback.authorName %></div>
        <div class="hkba-feedback-item__date" title="<%= feedback.datetime %>"><%= feedback.humantime %></div>
        <div class="hkba-feedback-item__actions">
        <div class="hkba-dd">
            <svg><use xlink:href="#icon-edit"></use></svg>
            <div class="hkba-dd__content">
                <ul>
                  <li><a href="" class="hkba-feedback-remove" data-feedback-id="<%= feedback.feedbackID %>" data-article-id="<%= feedback.articleID %>"  ><?php _e('Remove Response', 'ht-knowledge-base'); ?></a></li>
                  <li><a href="<%= feedback.articleViewUrl %>"><?php _e('View Article', 'ht-knowledge-base'); ?></a></li>
                  <li><a href="<%= feedback.articleEditUrl %>"><?php _e('Edit Article', 'ht-knowledge-base'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>

</script>

<!-- No items -->
<script type="text/html" id="ht-analytics-no-items">

    <div class="hkba-no-items" id="no-items" data-page="0">
        <?php _e('No items to display', 'ht-knowledge-base'); ?>
    </div>
    
</script>

<!-- Spinner/Loader -->
<script type="text/html" id="ht-analytics-spinner">
    
    <div class="hkba-loading">
        <img src="<%= hkbAnalyticsChart.spinner %>" />
    </div>
    
</script>

<!-- Pagination template -->
<script type="text/html" id="ht-analytics-pagination-template">

    <div class="hkba-pagination" id="hkba-feedback-pagination" data-page="1">
        <button id="hkba-feedback-pagination__prev_a" class="hkba-pagination__prev" data-page-target="<%= prevPage %>" <%= hasPrev ?  '' : 'disabled' %>><?php _e('Previous', 'ht-knowledge-base'); ?></button>
        <button id="hkba-feedback-pagination__next_a" class="hkba-pagination__next" data-page-target="<%= nextPage %>" <%= hasNext ?  '' : 'disabled' %>><?php _e('Next', 'ht-knowledge-base'); ?></button>
    </div>

</script>