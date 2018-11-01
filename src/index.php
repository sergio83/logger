
 <?php

  require_once("config.php");
  $selectedLevel = isset($_GET["level"])?$_GET["level"]:0;
  
  if($selectedLevel>4)
    $selectedLevel=0;

  $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $actual_link = strtok($actual_link,'?');
  $services_link = str_replace("index","services",$actual_link);
  
//------------------------------------------------------------------------------------
  function shouldShowLog($level,$selectedLevel){
    if($selectedLevel==0)
      return true;  

    $str = "";
    switch ($selectedLevel) {
      case 1:{
          if ( strcasecmp( $level, "[NOTICE]" ) == 0 ){
            return true;
          }else return false;
        }
      case 2:{
          if ( strcasecmp( $level, "[WARNING]" ) == 0 ){
            return true;
          }else return false;
        }              
      case 3:{
          if ( strcasecmp( $level, "[ERROR]" ) == 0 ){
            return true;
          }else return false;
        }              
      case 4:{
          if ( strcasecmp( $level, "[FATAL]" ) == 0 ){
            return true;
          }else return false;
        }                
      default:
        return true;            
    }    
  }
//------------------------------------------------------------------------------------
  function colorForLevel($level){

	if($level === "[NOTICE]"){
          return "#428bca";	
	}else if($level === "[WARNING]"){
          return "#f0ad4e";	
	}else if($level === "[ERROR]"){
          return "#FF5722";	
	}else if($level === "[FATAL]"){
          return "#F44336";	
	}
	
   return "#428bca";            
  }
//------------------------------------------------------------------------------------
function drawLog($currentLine,$selectedLevel){
	$array = explode("]", $currentLine);
	$date="";
	$level="";
	$log="";
	if(count($array)>=3){
		$date = substr($currentLine, 0, strlen($array[0])+1);
		$level = substr($currentLine, strlen($date), strlen($array[1])+1);
		$log = substr($currentLine, strlen($date) + strlen($level)+2, strlen($currentLine)-2);
	}
	if(shouldShowLog($level,$selectedLevel)){
	  echo("<tr>");
	  echo('<td class="col-md-2">');
	  echo($date);
	  echo("</td>");
	  echo('<td class="col-md-2" style="color:'.colorForLevel($level).'">');
	  echo($level);
	  echo("</td>");
	  echo('<td class="col-md-8">');
	  echo($log);
	  echo("</td>");
	  echo("</tr>");
	}
}
//------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="Track user behavior to discover issues in your mobile apps.">
  <meta name="author" content="Sergio Cirasa">
  <link rel="icon" href="images/fav.png">

  <title>Remote Logger</title>

  <!-- Bootstrap core CSS -->
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">


</head>

<body>     

    <div class="container">
      <div class="form-apns">      
        <h2 class="form-signin-heading">Remote Logger</h2>              
        <div class="row" style="margin-top:40px; margin-bottom:20px;">
          <div class="col-md-8">
            <div id="levelgroup" class="btn-group" data-toggle="buttons">        
              <label class="btn btn-primary <?php if($selectedLevel==0)echo('active')?>">
                <input type="radio" name="level_options" id="0" autocomplete="off" checked> All
              </label>
              <label class="btn btn-primary <?php if($selectedLevel==1)echo('active')?>">
                <input type="radio" name="level_options" id="1" autocomplete="off"> Notice
              </label>
              <label class="btn btn-primary <?php if($selectedLevel==2)echo('active')?>">
                <input type="radio" name="level_options" id="2" autocomplete="off"> Warning
              </label>
              <label class="btn btn-primary <?php if($selectedLevel==3)echo('active')?>">
                <input type="radio" name="level_options" id="3" autocomplete="off"> Error
              </label>
              <label class="btn btn-primary <?php if($selectedLevel==4)echo('active')?>">
                <input type="radio" name="level_options" id="4" autocomplete="off"> Fatal
              </label>
            </div>
          </div>
          <div class="col-md-4">    
            <button id="clearBtn" style="float: right;" type="button" class="btn btn-danger" style>Clear logs</button>
            <button id="clearBtn" style="float: right; margin-right:10px;" data-toggle="modal" data-target="#loggerModal" type="button" class="btn btn-success" style>Code Snippet</button>
          </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Level</th>
                  <th>Log</th>
                </tr>
              </thead>
              <tbody id="body">

                <?php                  
                  if(file_exists(log_file_path)){
                    $fp = fopen(log_file_path,'r');
                    $pos = -1; // Skip final new line character (Set to -1 if not present)        
                    $currentLine = '';
                    
                    while (-1 !== fseek($fp, $pos, SEEK_END)) {
                        $char = fgetc($fp);         
                        if (PHP_EOL == $char) {
							if(strlen($currentLine)!=0) 
	                            drawLog($currentLine,$selectedLevel);
                            $currentLine = '';
                        } else {
                            $currentLine = $char . $currentLine;
                        }
                        $pos--;
                    }
                
                    if($currentLine !== ""){ 
                        drawLog($currentLine,$selectedLevel);        
                        $currentLine = '';
                    }
                    
                    fclose($fp);
                  }
                ?>
              </tbody>
            </table>

        </div><!-- /table -->
      </div> <!-- / -->
    </div> <!-- /container -->


    <!-- Modal -->
    <div class="modal fade" id="loggerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title" id="myModalLabel">How to send logs:</h3>
          </div>
          <div class="modal-body">
	        <div class="alert alert-danger" role="alert"> <strong>Don't use it in the production environment!!!</strong> Only the last 50 records will be saved.</div>
            <h3>Request:</h3>
              <ul>
              <li><b>URL:</b> <?php echo($services_link."?action=add")?></li>
              <li><b>Http Method:</b> POST</li>
              <li><b>Http Body:</b> log=XX&level=YY</li>
              </ul>
              <h4>Levels values:</h4> 
              <ul>
              <li>1 - Notice</li>
              <li>2 - Warning</li>
              <li>3 - Error</li>
              <li>4 - Fatal</li>
              </ul>
            <hr class="">
            <h3>IOS Snippet:</h3> 
            <h4 style="padding-top: 16px;">1 - Add on AppDelegate the following method:</h4> 
            <div class="zero-clipboard" id="copyBtn" data-clipboard-demo="" data-clipboard-target="#method-sample"><span class="btn-clipboard">Copy</span></div>
              <figure class="highlight">
                <pre><code class="language-js" data-lang="js" id="method-sample">-(void)sendLog:(NSString*)log level:(NSInteger)level
{
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURLSession *session = [NSURLSession sessionWithConfiguration:configuration delegate:nil delegateQueue:[NSOperationQueue mainQueue]];
    
    NSURL *URL = [NSURL URLWithString:[NSString stringWithFormat:@"<?php echo($services_link.'?action=add')?>"]];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:URL];
    [request setHTTPMethod:@"POST"];
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
    NSString *postString = [NSString stringWithFormat:@"log=%@&level=%d",log,level];
    [request setHTTPBody:[postString dataUsingEncoding:NSUTF8StringEncoding]];
    NSURLSessionDataTask *dataTask = [session dataTaskWithRequest:request completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
  
    }];
    [dataTask resume];
}</code></pre></figure>        
            <h4 style="padding-top: 16px;">2 - And use it, for example, to register the token:</h4> 
            <div class="zero-clipboard" id="copyBtn" data-clipboard-demo="" data-clipboard-target="#call-sample"><span class="btn-clipboard">Copy</span></div>
              <figure class="highlight">
                <pre><code class="language-js" data-lang="js" id="call-sample">- (void)application:(UIApplication *)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)devToken {
    NSString *deviceToken = [[[[devToken description]
                               stringByReplacingOccurrencesOfString:@"<"withString:@""]
                              stringByReplacingOccurrencesOfString:@">" withString:@""]
                             stringByReplacingOccurrencesOfString: @" " withString: @""];
    NSLog(@"%@",deviceToken);
    
    [self sendLog:deviceToken level:2];
}</code></pre></figure>        

        </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.blockUI.js"></script>    
    <script src="js/clipboard.min.js"></script>
    <script src="js/helper.js"></script>
    <script>
      var clipboard = new Clipboard("#copyBtn");
    
      clipboard.on('success', function(e) {
          $(".btn-clipboard").attr('class', 'btn-clipboard');
          if(e.trigger.childNodes[0]!=null)
            e.trigger.childNodes[0].className = "btn-clipboard btn-clipboard-hover";        
          console.log(e);
      });

      clipboard.on('error', function(e) {
          console.log(e);
      });
  </script>
  </body>
</html>
