/**
 * Wait until the test condition is true or a timeout occurs. Useful for waiting
 * on a server response or for a ui change (fadeIn, etc.) to occur.
 *
 * @param testFx javascript condition that evaluates to a boolean,
 * it can be passed in as a string (e.g.: "1 == 1" or "$('#bar').is(':visible')" or
 * as a callback function.
 * @param onReady what to do when testFx condition is fulfilled,
 * it can be passed in as a string (e.g.: "1 == 1" or "$('#bar').is(':visible')" or
 * as a callback function.
 * @param timeOutMillis the max amount of time to wait. If not specified, 3 sec is used.
 */
function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 30000; //< Default Max Timout is 30s
    var start = new Date().getTime();
    var condition = false;
    var interval = setInterval(function() {
        if ( (new Date().getTime() - start <= maxtimeOutMillis) && !condition ) {
            // If not time-out yet and condition not yet fulfilled
            condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
        } else {
            if(!condition) {
                // If condition still not fulfilled (timeout but condition is 'false')
                console.log("Tests timeout");
                phantom.exit(1);
            } else {
                // Condition fulfilled (timeout and/or condition is 'true')
                typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                clearInterval(interval); //< Stop this interval
            }
        }
    }, 100); //< repeat check every 100ms
};


if (phantom.args.length === 0 || phantom.args.length > 2) {
    console.log('Usage: test-runner.js URL');
    phantom.exit(1);
}

var page = new WebPage();

page.open(phantom.args[0], function(status) {
    if (status !== "success") {
        console.log("Unable to access network");
        phantom.exit(1);
    } else {
        page.injectJs(phantom.args[1]);
        
        waitFor(function() {
            return page.evaluate(function() {
                var el = document.getElementById('qunit-testresult');
                return (el && el.innerText.match('completed'));
            });
        }, function() {
            var passedNum = page.evaluate(function() {
                var el = document.getElementById('qunit-testresult');
                try { return parseInt(el.getElementsByClassName('passed')[0].innerHTML, 10); }
                catch (e) { }
                return -1;
            });
            var totalNum = page.evaluate(function() {
                var el = document.getElementById('qunit-testresult');
                try { return parseInt(el.getElementsByClassName('total')[0].innerHTML, 10); }
                catch (e) { }
                return -1;
            });
            var failedNum = page.evaluate(function() {
                var el = document.getElementById('qunit-testresult');
                try { return parseInt(el.getElementsByClassName('failed')[0].innerHTML, 10); }
                catch (e) { }
                return -1;
            });
            
            console.log('Tests completed');
            console.log(passedNum);
            console.log(totalNum);
            console.log(failedNum);
            
            phantom.exit(failedNum);
        });
    }
});
