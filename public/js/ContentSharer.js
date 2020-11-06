const jsData = $('#jsData');

const sessionId = jsData.data('sessionId');
const lastContentId = jsData.data('lastContentId');

const startTime = Date.now();
const timeoutTable = {
    0: 2,
    1: 4,
    5: 10,
    15: 30,
}

function getTimeoutInMs(msFromStart) {
    const minFromStart = msFromStart/1000/60;
    let delaySec = timeoutTable[0];
    for(const entry of Object.entries(timeoutTable)) {
        if(minFromStart > entry[0]) {
            delaySec = Math.max(delaySec, entry[1])
        }
    }

    return delaySec*1000;
}

function requestLoop() {
    fetch('/xhr/latest/'+sessionId+'/'+lastContentId)
        .then(data => data.json())
        .then(hasLatestContent => {
            if(hasLatestContent === false) {
                location.reload();
            }
            const timeFromStart = Date.now() - startTime;
            setTimeout(requestLoop, getTimeoutInMs(
                timeFromStart
            ));
        });
}

setTimeout(requestLoop, 3000);