<!-- Template for Heroic KB Embed v2 -->
<script id="hkbfe-script" src="<?php echo site_url('/?kbembed=script'); ?>" async defer></script> 
<link id="hkbfe-style" rel="stylesheet" href="<?php echo site_url('/?kbembed=style'); ?>" media="all">

<div id="ht-kb-fe-embed-container" class="ht-kb-fe-embed-container">
  <div id="hkbembed-button" style="
  --hkbembed-bg: <?php echo apply_filters('kb_fe_embed_get_embed_color', ''); ?>;;
  --hkbembed-color: #ffffff;">
    <span class="hkbembed-button-open">      
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><g fill="#fff"><circle cx="12" cy="21.5" r="2"/><path d="M10.5 17.5V16c0-2.918 1.939-4.513 3.5-5.794S16.5 8.07 16.5 6.5c0-1.822-1.57-3-4-3a7.59 7.59 0 00-4.108 1.206l-1.2.877-1.78-2.39L6.6 2.3A10.514 10.514 0 0112.5.5c4.122 0 7 2.468 7 6 0 3.067-1.994 4.707-3.6 6.023-1.535 1.262-2.4 2.045-2.4 3.477v1.5z"/></g></svg>
    </span>
    <span class="hkbembed-button-close">
      <svg xmlns="http://www.w3.org/2000/svg" stroke-width="2" viewBox="0 0 24 24" height="24" width="24"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-miterlimit="10" stroke-linejoin="round"><path d="M19 5L5 19M19 19L5 5"/></g></svg>
    </span>
  </div>
  <div id="ht-kb-fe-embed-contents" style="display:none;">
    <iframe id="ht-kb-fe-embed-iframe" src="<?php echo site_url('/?kbembed=content'); ?>" loading="lazy" style="display:block;"></iframe>
  </div>
</div>
<!-- /end Template for Heroic KB Embed v2 -->