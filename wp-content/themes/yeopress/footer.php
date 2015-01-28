  <footer id="page-footer" class="main-column">

            <div class="row">
                <div class="col-md-11">

                    <div class="row">
                        <div class="col-md-4">
                            <?php echo get_custom('organisation'); ?><br/><br/>

                            <?php echo get_custom('street'); ?><br/>
                            <?php echo get_custom('zip'); ?> <?php echo get_custom('city'); ?>
                        </div>
                        <div class="col-md-4">
                            bildog wird unterstützt von:<br/><br/>
                            <?php foreach (range(1, 20) as $number):
                                if(!get_custom('supporter_' . $number)) {continue;} ?>
                                <img src="<?php echo get_custom('supporter_' . $number); ?>" />
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo getPageLinkByTitle('Impressum') ?>">Impressum</a><br/>
                            <a href="<?php echo getPageLinkByTitle('Haftungsausschluss') ?>">Haftungsausschluss</a><br/>
                            <a href="<?php echo getPageLinkByTitle('Datenschutzerklärung') ?>">Datenschutzerklärung</a><br/>
                        </div>
                    </div>

                </div>
                <div class="col-md-1">
                    <div class="row">
                        <div class="col-md-12 icon-fb">
                            <a href="https://de-de.facebook.com/bildung.ohnegrenzen">
                                <img src="<?php echo bloginfo('template_directory') ?>/images/icon_fb.png" />
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 icon-yt">
                            <img src="<?php echo bloginfo('template_directory') ?>/images/icon_yt.png" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row copyright">
                copyright 2014 bildog Bildung ohne Grenzen e.V.
            </div>

        </footer>
  <!-- END content-wrap -->
  </div>      


    <?php wp_footer() ?>
    <!-- initialize parallax scrolling script for parallaxing background images -->
    <script type="text/javascript">
      function isNonMobileBrowser() {
        if(navigator.userAgent.indexOf("Android") != -1 ) {
          return false;
        }

        if(navigator.userAgent.indexOf("Chrome") != -1 ) {
          // Chrome
          return true;
        } else if(navigator.userAgent.indexOf("Opera") != -1 ) {
          // Opera
          return true;
        } else if(navigator.userAgent.indexOf("Firefox") != -1 ) {
          // FireFox
          return true;
        } else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
          {
          // IE 
          return true;
        } else if(navigator.userAgent.indexOf("Safari")) {
          return true;
        } else {
          // unknown
          return false;
        }
      }
      if (isNonMobileBrowser()) {
        skrollr.init({
          smoothScrolling: false,
        });
      }
    </script>	
  </body>
</html>
