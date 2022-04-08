<html>
    <body>
        <div id="timer-text"></div>
        <button id="start">start</button>
        <button id="restart">restart e</button>
        <button id="stop">stop</button>
    </body>
    <script>
    const timerEl = document.getElementById("timer-text")
    const startBtn = document.getElementById("start")
    const restartBtn = document.getElementById("restart");
    const stopBtn = document.getElementById('stop');

    let runTheClock;
    let seconds = 0;
    render(seconds);

    function makeTwoNumbers(num) {
        return ((num < 10) ? "0" : "") + num;
    }

    function tick() {
        seconds++;
        render(seconds);
    }
    
    function render(secs) {

        const hours = Math.floor(secs / 3600);
        const minutes = Math.floor(secs / 60) - (hours * 60);
        const seconds = secs % 60;

        const val = [hours, minutes, seconds].map(makeTwoNumbers).join(":");
        console.log(val);
        timerEl.textContent = val;
    }
    
    function runTimer() {
        runTheClock = setInterval(tick, 1000);
    }
    
    function stopTimer() {
        clearInterval(runTheClock)
    }
    
    function resetTimer() {
        seconds = 0;
        render(seconds);
    }
    
    restartBtn.addEventListener("click", resetTimer);
    stopBtn.addEventListener("click", stopTimer);
    startBtn.addEventListener("click", runTimer);

    </script>
</html>
