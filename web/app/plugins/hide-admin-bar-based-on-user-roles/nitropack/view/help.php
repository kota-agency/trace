<div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          Can I use another page caching plugin and NitroPack.io at the same time?
        </div>
        <div class="card-body">
          <p>WordPress is designed in a way that you can use only a single page cache solution at a time. Such is the case with other page cache solutions too. You can use other non-page cache optimization solutions perfectly well with NitroPack.io (example: ShortPixel).</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          What is an optimization?
        </div>
        <div class="card-body">
          <p>An 'optimization' is when our service optimizes a single version of one of your site URLs. The result is then cached on your website and it is served to all visitors until the cache is deleted or becomes invalid. After that, a second optimization is required for the same URL.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          What is the difference between Invalidate and Purge?
        </div>
        <div class="card-body">
          <p>When our service optimizes the content for a specific page, this content is cached on your site, and it is subsequently served to future visitors. There are two ways to refresh this content - Invalidate and Purge.</p>

          <p>Invalidate only marks the cached content as "stale", but keeps serving it until newly optimized content is available. As a result, your visitors always see optimized content, even though it may be outdated for a short while.</p>

          <p>Purge deletes the cached file from your site, meaning your site visitors will see the updated content immediately, but it will not be optimized for a few requests until NitroPack prepares the newly optimized content. Usually, this takes between a few seconds to a few minutes.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          How often should I purge / invalidate the cache?
        </div>
        <div class="card-body">
          <p>Typically, you should not need to do a cache purge / invalidate manually. You can take these actions in case you want to force NitroPack.io to re-cache the updated site content immediately. In most cases, NitroPack.io takes care of updating the content automatically.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          If I modify an image/CSS/JS file on my site, why is it not updated in the NitroPack.io CDN?
        </div>
        <div class="card-body">
          <p>To avoid excessive traffic to your site, we need to cache your image/CSS/JS files on our servers. If you modify the file contents on your end, this will not update it on our end. This expected behavior is a common practice on many other caching solutions (CDN, browser caching, etc.)</p>

          <p>To force NitroPack to download the new file, you can either:</p>

          <p>Option 1) Save the file with a different name, and update your site content to use the new name. After that, please make sure to invalidate all NitroPack cache. Doing this is the best way to ensure the updated content will be re-cached while avoiding excessive traffic to your server.</p>

          <p>Option 2) Do a complete purge of your NitroPack cache, forcing our service to re-download all resources.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          What are the advantages and disadvantages of Medium, Strong, and Ludicrous?
        </div>
        <div class="card-body">
          <p>Ludicrous mode ensures you receive the highest possible PageSpeed score, as it uses a resource loader script which alters the way your CSS and JS resources are loaded/rendered (in some cases, some resources are delayed). The resource loader is generally stable, but on some themes, it may cause unexpected behavior.</p>

          <p>Strong mode also has a resource loader, but it is loading your resources without delaying them - this produces better stability, but also gives lower PageSpeed scores compared to Ludicrous mode.</p>

          <p>Finally, Medium does not have the resource loader enabled, ensuring better stability, but it does not give the best PageSpeed scores.</p>

          <p>Which option is best for you depends on the way your theme is coded, and the 3rd party CSS and JS resources it utilizes. As a rule of thumb, the best mode is the one which produces the highest scores with the least user experience issues.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-faq-item">
        <div class="card-header">
          Which optimization mode would you recommend me to use?
        </div>
        <div class="card-body">
          <p>We recommend you to use the Strong mode. If your score is below 80 points on it, we recommend trying the Ludicrous mode. If Ludicrous mode seems too aggressive (as it is delaying the loading of most of the resources), you can stick to using Strong mode.</p>
        </div>
      </div>
    </div>
  </div>
</div>
