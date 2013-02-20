<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" type="text/css" href="/phantomTestPlugin/css/qunit.css" />
  <script type="text/javascript" src="/phantomTestPlugin/js/steal/steal.js"></script>
</head>

<body>
  <h1  id="qunit-header"></h1>
  <h2  id="qunit-banner"></h2>
  <div id="qunit-testrunner-toolbar"></div>
  <h2  id="qunit-userAgent"></h2>
  <ol  id="qunit-tests"></ol>
  
  <iframe name="funcunit"></iframe>
  <style type="text/css">
    iframe {
      position: absolute;
      top: 20px;
      right: 20px;
      
      width: 1000px;
      height: 500px;
      
      border: none;
      background: #FFF;
      
      box-shadow: 0 0 5px -1px #333;
    }
  </style>
  
  <script type="text/javascript">
    steal("funcunit", function() {

FuncUnit.timeout = 5000;

S.listen = function(eventName, successCallback) {
    var ok = false;
    
    S(S.win.document)
    .one(eventName, function() {
        ok = true;
    })
    .wait(
        function() {
            return ok;
        },
        FuncUnit.timeout,
        successCallback,
        "Wait for " + eventName + " event to be triggered"
    );
}

<?php echo $sf_data->getRaw("content") ?>

    });
  </script>
</body>

</html>
