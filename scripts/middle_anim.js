const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');


const image = new Image(60, 45);
image.onload = drawImageActualSize;

image.src = '../images/splashes/Io.png';


function drawImageActualSize() {
    // Use the intrinsic size of image in CSS pixels for the canvas element
    canvas.width = this.naturalWidth;
    canvas.height = this.naturalHeight;

    // Will draw the image as 300x227, ignoring the custom size of 60x45
    // given in the constructor
    ctx.drawImage(this, 0, 0);
}