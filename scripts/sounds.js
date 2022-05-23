function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function run_audio(champ) {
    document.getElementById("sound").pause();
    document.getElementById("sound2").src = champ;
    document.getElementById("sound").currentTime = 0;
    document.getElementById("sound").load();
    document.getElementById("sound").play();
}