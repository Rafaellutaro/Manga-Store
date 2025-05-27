const shopLua = document.getElementById('shop_lua');
const images = [
  "../img/posters/poster.jpg",
  "../img/posters/poster-news.jpg",
  "../img/banner01.jpg",
  "../img/banner02.jpg",
];

let index = 0;
let isBefore = true;

function updateBackground() {
  const nextImage = `url('${images[index]}')`;
  
  if (isBefore) {
    shopLua.style.setProperty('--before-image', nextImage);
    shopLua.classList.add('fade-before');
    shopLua.classList.remove('fade-after');
  } else {
    shopLua.style.setProperty('--after-image', nextImage);
    shopLua.classList.add('fade-after');
    shopLua.classList.remove('fade-before');
  }

  isBefore = !isBefore;
  index = (index + 1) % images.length;
}

setInterval(updateBackground, 5000);

// Initialize first image
shopLua.style.setProperty('--before-image', `url('${images[0]}')`);
shopLua.classList.add('fade-before');
