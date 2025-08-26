// Modified dev tools detection to be less aggressive
(function() {
    // Temporarily disable the aggressive blanking behavior
    // This allows the mobile view to work properly
    console.log("Security script loaded but blanking behavior disabled");
    
    // Original code is commented out to preserve it
    /*
    let threshold = 160;
    let wasBlanked = false;
    let originalHTML = '';
    let originalBG = '';
    function isDevToolsOpen() {
        // Heuristic: window size
        let widthThreshold = window.outerWidth - window.innerWidth > threshold;
        let heightThreshold = window.outerHeight - window.innerHeight > threshold;

        // Timing attack: debugger statement
        let start = performance.now();
        debugger;
        let end = performance.now();
        let timingThreshold = end - start > 100;

        return widthThreshold || heightThreshold || timingThreshold;
    }
    function checkDevTools() {
        if (isDevToolsOpen() && !wasBlanked) {
            originalHTML = document.body.innerHTML;
            originalBG = document.body.style.background;
            document.body.innerHTML = '';
            document.body.style.background = '#fff';
            wasBlanked = true;
        } else if (!isDevToolsOpen() && wasBlanked) {
            document.body.innerHTML = originalHTML;
            document.body.style.background = originalBG;
            wasBlanked = false;
            location.reload();
        }
    }
    setInterval(checkDevTools, 500);
    */
})();




// Disable right-click
window.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});
// Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
window.addEventListener('keydown', function(e) {
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
        (e.ctrlKey && e.key === 'U')
    ) {
        e.preventDefault();
    }
});


//laravel == <script src="{{ asset('js/dev-tools-security.js') }}"></script>
//<script src="resources/js/dev-tools-security.js"></script>

