<template>
    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
        xml:space="preserve">

        <path class="circle" stroke="#1C9943" stroke-width="10" fill="#fff" fill-opacity="0" stroke-miterlimit="10" d="M150,47.9c18.4,0,35.4,4.6,51,13.8s28,21.6,37.2,37.2s13.8,32.6,13.8,51s-4.6,35.4-13.8,51s-21.6,28-37.2,37.2
      s-32.6,13.8-51,13.8s-35.4-4.6-51-13.8s-28-21.6-37.2-37.2s-13.8-32.6-13.8-51s4.6-35.4,13.8-51s21.6-28,37.2-37.2
      S131.7,47.9,150,47.9z M150,238.7c16.2,0,31-4," />

        <path class="tick" cx="0" cy="0" r="1" opacity="1" fill="#1C9943" stroke="" stroke-width="10" d="M208.4,118.6c0.8-0.8,1.2-1.9,1.2-3.3c0-1.4-0.4-2.6-1.2-3.7l-3.7-3.3c-0.8-1.1-1.9-1.6-3.3-1.6
  s-2.6,0.4-3.7,1.2l-67,67l-28.4-28.8c-1.1-0.8-2.3-1.2-3.7-1.2c-1.4,0-2.5,0.4-3.3,1.2l-3.7,3.3c-0.8,1.1-1.2,2.3-1.2,3.7
  s0.4,2.5,1.2,3.3l35.4,35.8c1.1,1.1,2.3,1.6,3.7,1.6c1.4,0,2.5-0.5,3.3-1.6L208.4,118.6z" />

    </svg>
</template>


<style scoped>
svg {
    width: 80%;
    height: 260px;
}

.tick {
    transform-origin: center;
    animation: grow 0.8s ease-in-out forwards;
}

@keyframes grow {
    0% {
        transform: scale(0);
        opacity: 0;
    }

    60% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.circle {
    transform-origin: center;
    stroke-dasharray: 1000;
    stroke-dashoffset: 0;
    animation: dash 1s linear;
}

@keyframes dash {
    from {
        stroke-dashoffset: 1000;
        opacity: 0;
    }

    to {
        stroke-dashoffset: 0;
    }
}
</style>