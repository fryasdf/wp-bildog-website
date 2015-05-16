<?php

?>
<footer id="page-footer" class="main-column">
  <div class="row">
    <?php // BILDOG WIRD UNTERSTUETZT SPALTE: 8?>
    <div class="col-md-8 smaller-than-md-center">
      bildog wird unterstützt von:<br/><br/>
      <?php 
        //foreach (range(1, 20) as $number):
        //if(!get_custom('supporter_' . $number)) {continue;} 
      ?>
        <!-- <img src="<?php echo get_custom('supporter_' . $number); ?>" /> -->
      <?php 
          $imageDirPath = get_bloginfo('template_directory') . '/images/supporter/';
      ?>
      
      <a href="http://www.willer.de"><img src=<?php echo $imageDirPath . "willer.png" ?> height="68" width="150"></a>
      <a href="http://www.steuerberater-tijssen.de"><img src=<?php echo $imageDirPath . "stb_tijssen.png" ?> height="68" width="150"></a>
      <a href="http://www.felix-roth.de"><img src=<?php echo $imageDirPath . "frd.png" ?> height="68" width="150"></a>
        
      <?php //endforeach; ?>
    </div>
    <?php // KONTAKT-UND-SOCIAL MEDIA SPALTE: 4?>
    <div class="col-md-4">
      <div class="row">
        <?php // KONTAKT SPALTE : 10?>
        <div class="col-xs-10">
          <a href="<?php 
             echo getPageLinkByTitle('Kontakt') 
             ?>">Kontakt</a><br/>
          <a href="<?php 
             echo getPageLinkByTitle('Impressum') 
             ?>">Impressum</a><br/>
          <a href="<?php 
             echo getPageLinkByTitle('Haftungsausschluss') 
             ?>">Haftungsausschluss</a><br/>
          <a href="<?php 
             echo getPageLinkByTitle('Datenschutzerklärung') 
             ?>">Datenschutzerklärung</a><br/>
        </div>
        <?php // SOCIAL MEDIA SPALTE : 2?>
        <div class="col-xs-2">
          <div class="row">
            <div class="col-md-12 icon-fb">
              <a href="https://www.facebook.com/bildogEV" target="_blank">
                <img src="<?php echo bloginfo('template_directory') ?>/images/icon_fb.png" />
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 icon-yt">
              <a href="https://www.youtube.com/channel/UChgdT1Lw9UPJRoFcjcu8-ww" target="_blank">
                <img src="<?php echo bloginfo('template_directory') ?>/images/icon_yt.png" /></a>
            </div>
          </div>
        </div>
      <?php // ENDE SOCIAL MEDIA SPALTE ?>
      </div>
    <?php // ENDE ROW ?>
    </div>
  <?php // ENDE KONTAKT-UND-SOCIAL MEDIA SPALTE?>
  </div>
  <div class="row copyright">
    copyright 2015 bildog Bildung ohne Grenzen e.V.
  </div>
</footer>

<!-- END content-wrap -->
</div>      

  <?php wp_footer() ?>
  </body>
</html>
