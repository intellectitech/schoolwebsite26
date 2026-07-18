//==============================
// ELEMENTS
//==============================

const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuBtn");
const main = document.querySelector(".main");
const menuItems = document.querySelectorAll(".menu li[data-page]");
const pages = document.querySelectorAll(".page");
const pageTitle = document.getElementById("pageTitle");
const themeToggle = document.getElementById("themeToggle");


//==============================
// SIDEBAR TOGGLE
//==============================

menuBtn.addEventListener("click", () => {

    if(window.innerWidth <= 900){

        sidebar.classList.toggle("show");

    }else{

        sidebar.classList.toggle("close");

        main.classList.toggle("expand");

    }

});


//==============================
// PAGE SWITCHING
//==============================

menuItems.forEach(item=>{

    item.addEventListener("click",()=>{

        menuItems.forEach(link=>{

            link.classList.remove("active");

        });

        item.classList.add("active");

        let page=item.dataset.page;

        pages.forEach(p=>{

            p.classList.remove("active");

        });

        document.getElementById(page).classList.add("active");

        pageTitle.innerHTML=

        page.charAt(0).toUpperCase()+page.slice(1);

        if(window.innerWidth<=900){

            sidebar.classList.remove("show");

        }

    });

});


//==============================
// DARK MODE
//==============================

if(localStorage.getItem("theme")=="dark"){

    document.body.classList.add("dark");

    themeToggle.innerHTML="<i class='bx bx-sun'></i>";

}

themeToggle.addEventListener("click",()=>{

    document.body.classList.toggle("dark");

    if(document.body.classList.contains("dark")){

        localStorage.setItem("theme","dark");

        themeToggle.innerHTML="<i class='bx bx-sun'></i>";

    }

    else{

        localStorage.setItem("theme","light");

        themeToggle.innerHTML="<i class='bx bx-moon'></i>";

    }

});


//==============================
// ANIMATED COUNTERS
//==============================

const counters=document.querySelectorAll(".counter");

counters.forEach(counter=>{

    let target=parseInt(counter.innerText);

    let count=0;

    let speed=Math.ceil(target/120);

    counter.innerText="0";

    function update(){

        count+=speed;

        if(count>=target){

            counter.innerText=target.toLocaleString();

        }

        else{

            counter.innerText=count.toLocaleString();

            requestAnimationFrame(update);

        }

    }

    update();

});


//==============================
// SEARCH
//==============================

const search=document.querySelector(".search input");

search.addEventListener("keyup",()=>{

    let value=search.value.toLowerCase();

    document.querySelectorAll("table tbody tr").forEach(row=>{

        row.style.display=

        row.innerText.toLowerCase().includes(value)

        ? ""

        : "none";

    });

});


//==============================
// CARD HOVER
//==============================

document.querySelectorAll(".card").forEach(card=>{

    card.addEventListener("mouseenter",()=>{

        card.style.transform="translateY(-8px) scale(1.02)";

    });

    card.addEventListener("mouseleave",()=>{

        card.style.transform="";

    });

});


//==============================
// LIVE CLOCK
//==============================

const clock=document.createElement("div");

clock.style.fontWeight="600";

clock.style.marginLeft="20px";

document.querySelector(".left").appendChild(clock);

function updateClock(){

    let now=new Date();

    clock.innerHTML=now.toLocaleTimeString();

}

setInterval(updateClock,1000);

updateClock();


//==============================
// GREETING
//==============================

let hour=new Date().getHours();

let greeting="Welcome";

if(hour<12){

    greeting="Good Morning";

}

else if(hour<18){

    greeting="Good Afternoon";

}

else{

    greeting="Good Evening";

}

console.log(greeting);


//==============================
// BUTTON EFFECT
//==============================

document.querySelectorAll("button").forEach(button=>{

button.addEventListener("mousedown",()=>{

button.style.transform="scale(.95)";

});

button.addEventListener("mouseup",()=>{

button.style.transform="";

});

});


//==============================
// RANDOM COLORS
//==============================

const colors=[

"#0f4c81",

"#1976d2",

"#2e7d32",

"#ef6c00",

"#8e24aa"

];

document.querySelectorAll(".card i").forEach((icon,index)=>{

icon.style.color=colors[index%colors.length];

});


//==============================
// PAGE FADE
//==============================

pages.forEach(page=>{

page.style.animation="fade .4s";

});


//==============================
// MOBILE RESIZE
//==============================

window.addEventListener("resize",()=>{

if(window.innerWidth>900){

sidebar.classList.remove("show");

}

});


//==============================
// LOADING EFFECT
//==============================

window.addEventListener("load",()=>{

document.body.style.opacity="0";

setTimeout(()=>{

document.body.style.transition=".4s";

document.body.style.opacity="1";

},100);

});