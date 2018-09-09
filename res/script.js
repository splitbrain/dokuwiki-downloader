var TIMER;

/**
 * Start a timer, show error after it runs out
 * @param seconds
 */
function startTimer(seconds) {
    TIMER = window.setTimeout(function () {
        document.querySelector('div.progress').style.display = 'none';
        document.querySelector('blockquote.progress').style.display = 'block';
    }, seconds * 1000);
}

/**
 * Stop the timer and hide the progress bar
 */
function stopTimer() {
    if(TIMER) window.clearTimeout(TIMER);
    document.querySelector('div.progress').style.display = 'none';
}