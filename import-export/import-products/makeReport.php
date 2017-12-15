<?php
include( "ObjectGenerator.php" );
include( "Log.php" );
include( dirname( __DIR__ ) . "/Timers.php" );

Log::enable();
ini_set( "memory_limit", "2048M" );

makePage();

function content() {
	Timers::start( "overall" );
	$generator = new ObjectGenerator();
	$generator->generateObjects();
	$generator->createCSVReport();
	$generator->createXMLReport();
	Timers::stop( "overall" );
}

function makePage() {
	?>
  <!DOCTYPE html>
  <html>
  <head><title>Импорт</title>
    <!-- Latest compiled and minified CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
    
  </head>
  <body>
  <div class="container">
    <div class='row'>
      <div class='col-sm-12'>

        <ul class="nav nav-pills">
          <li class="active"><a data-toggle="pill" href="#config">Config</a></li>
          <li><a data-toggle="pill" href="#products">Products</a></li>
          <li><a data-toggle="pill" href="#tags">Tags</a></li>
          <li><a data-toggle="pill" href="#SQL">SQL</a></li>
        </ul>

        <div class="tab-content">
          <div id="config" class="tab-pane fade in active">
	          <?php content(); ?>
          </div>
          <div id="products" class="tab-pane fade">
            <h3>Products</h3>
            <p>Some content in menu 1.</p>
          </div>
          <div id="tags" class="tab-pane fade">
            <h3>Tags</h3>
            <p>Some content in menu 2.</p>
          </div>
          <div id="SQL" class="tab-pane fade">
            <h3>SQL</h3>
            <p>Some content in menu 2.</p>
          </div>
        </div>
        
		  
      </div>
    </div>
  </div>
  
  </body>
  </html>
	<?php
}
