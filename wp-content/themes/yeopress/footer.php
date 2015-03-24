<footer id="page-footer" class="main-column">
  <div class="row">
    <?php // BILDOG WIRD UNTERSTUETZT SPALTE: 8?>
    <div class="col-md-8 smaller-than-md-center">
      bildog wird unterstützt von:<br/><br/>
      <?php foreach (range(1, 20) as $number):
        if(!get_custom('supporter_' . $number)) {continue;} ?>
          <img src="<?php echo get_custom('supporter_' . $number); ?>" />
      <?php endforeach; ?>
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
      <?php // ENDE SOCIAL MEDIA SPALTE ?>
      </div>
    <?php // ENDE ROW ?>
    </div>
  <?php // ENDE KONTAKT-UND-SOCIAL MEDIA SPALTE?>
  </div>
  <div class="row copyright">
    copyright 2014 bildog Bildung ohne Grenzen e.V.
  </div>
</footer>

<!-- END content-wrap -->
</div>      


    <?php wp_footer() ?>

  <script type="text/javascript">
 
    function isOpera() {
      return (window.opera && window.opera.buildNumber);
    }
    function isSafari() {
      return (navigator.userAgent.indexOf("Safari") > -1);
    }
    //var $ =jQuery.noConflict();
    //$( ".projekt-box" ).each(function( index ) {
    //  console.log( index + ": " + $( this ).height() );
    //});
    //function myLoad() {
      var allElements = document.getElementsByClassName('projekt-box');
      var allInnerToggleLinks = document.getElementsByClassName('toggle-link-inner');

      var BOX_HEIGHT = "<?php 
          $file_name = getcwd() . '/' . str_replace(get_bloginfo('url') . '/', '', get_bloginfo('template_directory')) . '/scss/_projekte.scss';
          $variable = read_scss_variable_without_unit
               ($file_name, 'projekte_box_height', 'px');
          echo $variable;
          ?>";
      for (i=0; i < allElements.length; i++) {
        element = allElements[i];
        innerToggleLink = allInnerToggleLinks[i];
        console.log('i=' + i + 
                '|client=' + element.clientHeight +
                '|offset=' + element.offsetHeight +
                '|scroll=' + element.scrollHeight
                );
        if (isOpera() || isSafari()) { 
          // opera and safari give weird (wrong!) values for
          // the client-, offset and scrollheight
          // depending on whether or not the page has been 
          // reloaded or not (weird??)
        } else {
          // we want all the read more buttons to be there in
          // order for the project descriptions to look alike
          //if (element.offsetHeight <= BOX_HEIGHT) {
          //  innerToggleLink.style.display = "none";
          //} 
        }
       allElements[i].style.height = BOX_HEIGHT + 'px';
      }
    //}
  </script>
 
  </body>
</html>
