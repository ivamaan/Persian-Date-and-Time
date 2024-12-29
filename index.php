<?php
// Dont touch these! =>Calibare time from top of get_jalali_time.php!
$year_offset = 0;
$day_offset = 0;
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="امروز">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <meta name="theme-color" content="#000">
    <meta http-equiv="refresh" content="864000">
    <title>تاریخ و زمان به شمسی</title>
    <style>

        @font-face {
            font-family: 'Cairo';
            src: url("./CairoPlayRegular.woff") format('woff');
        }
        @font-face {
            font-family: 'Vazir';
            src: url("./Vazir-Bold-FD.woff") format('woff');
        }
        @font-face {
            font-family: 'Yekan';
            src: url("./Yekan.woff") format('woff');
        }
        	
        body {  
            background-color: black;
            background-size: cover;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height of the viewport */
            margin: 0; /* Remove default margin */
            align-items: center;
            text-align: center;
            direction: rtl;
            font-family: "Vazir", sans-serif;
            cursor: default;
            user-select: none;
            transition: background-color 0.5s;
            text-shadow: 2px 4px 10px #111;
            transition: transform 0.2s;
            touch-action: manipulation;
            margin-top: -20px !important;
        }
        .content {
            padding: 0px;
        }
        .date {
            line-height: 1;
        }

        #startButton {
            opacity: 0.4;
            filter: grayscale(1);
            cursor: pointer;
            width: 50px;
            margin: auto;
            display: block;
        }
        
        #startButton.active {
            opacity: 0.8;
            filter: grayscale(0);
        }
        
        .blinking-colon {
            animation: blink 3s infinite;
            font-size: 70px;
            color:#fff;
            text-shadow: 0px 0px 0px #fff;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        
    </style>
</head>

<body>
        <div class="content">
        <div id="datetime" class="date" style="font-size: 90px;"></div>

        <div id="weather" style="font-size: 18px;" title="Current Weather Data"></div>
        <a id="startButton"><img src="bell.svg" title="Turn on to chime every hour"></a>
        
        <p id="location" style="line-height:1.5;font-size: 14px;font-weight:400" title="This is your IP Address"></p>
           <script>
        // Fetch the location immediately on page load
        fetchUserLocation();
        // Set an interval to fetch the location every 30s
        setInterval(fetchUserLocation, 60000);

        async function fetchUserLocation() {
            try {
                const response = await fetch('https://api.ipapi.is');
                const data = await response.json();
                const countryCode = data.location.country_code.toUpperCase();
                const countryName = translateCountryName(data.location.country);
                const IP = data.ip;
                const org = data.company.name;
                const flagEmoji = getFlagEmoji(countryCode);

                let innerHTML = `${flagEmoji} ${countryName}<br> ${IP}`;
                if (org && org.includes("Amazon")) {
                    innerHTML += `<span style="font-size:8px"><br>✨</span>`;
                } else {
                    innerHTML += `<span style="font-size:8px"><br>:)<br></span>`;
                }

                document.getElementById('location').innerHTML = innerHTML; 
            } catch (error) {
                console.error('خطا در دریافت داده‌های موقعیت:', error);
                document.getElementById('location').innerHTML = '‌ ‌';
            }
        }

        function getFlagEmoji(countryCode) {
            return String.fromCodePoint(...[...countryCode].map(c => 0x1F1E6 + c.charCodeAt(0) - 65));
        }

        function translateCountryName(countryName) {
            const translations = {
                'United States': 'ایالات متحده',
                'Canada': 'کانادا',
                'United Kingdom': 'انگلستان',
                'Germany': 'آلمان',
                'France': 'فرانسه',
                'Italy': 'ایتالیا',
                'Spain': 'اسپانیا',
                'Iran': 'ایران',
                'Sweden': 'سوئد',
                'Ireland': 'ایرلند',
                'Australia': 'استرالیا',
                'Brazil': 'برزیل',
                'China': 'چین',
                'India': 'هند',
                'Japan': 'ژاپن',
                'Russia': 'روسیه',
                'Mexico': 'مکزیک',
                'South Africa': 'آفریقای جنوبی',
                'Netherlands': 'هلند',
                'Turkey': 'ترکیه',
            };
            return translations[countryName] || countryName;
        }
    </script>
    
    <div style="display: inline-flex;align-items: center;">
        
    <a id="colorButton" style="cursor: pointer;"><img src="bgicon.svg" style="width: 28px;" title="Change Background Color"></a>
    
    <a id="toggleButton" style="cursor: pointer;margin-right: 10px;">
    <img id="buttonImage" style="width: 24px;" src="play.svg" alt="Play" title="Play Classical Stream"></a>
    <audio id="radio" src="" preload="none"></audio>

    </div>
    
</div>
        
    
    <audio id="chime" src="bells.mp3" preload="auto"></audio>

    <script>
        const chimeAudio = document.getElementById("chime");
        let chimeInterval;

        function playChime() {
            chimeAudio.currentTime = 0;
            chimeAudio.play().catch(error => {
                console.error('Audio playback failed:', error);
            });
        }

        function checkTime() {
            const now = new Date();
            const minutes = now.getMinutes();
            const seconds = now.getSeconds();
            if (minutes === 0 && seconds === 0) {
                playChime();
            }
        }

        document.getElementById("startButton").addEventListener("click", () => {
            const button = document.getElementById("startButton");
            if (chimeInterval) {
                clearInterval(chimeInterval);
                chimeInterval = null;
                button.classList.remove("active");
            } else {
                chimeInterval = setInterval(checkTime, 1000);
                playChime(); // Optionally play chime immediately on start
                button.classList.add("active");
            }
        });
    </script>
<script>
    const radio = document.getElementById('radio');
    const toggleButton = document.getElementById('toggleButton');
    const buttonImage = document.getElementById('buttonImage');
    const streamUrl = 'https://cast1.torontocast.com:2085/stream';

    toggleButton.addEventListener('click', () => {
        if (radio.paused) {
            radio.src = streamUrl; // Set the stream URL before playing
            radio.play();
            buttonImage.src = 'stop.svg';
        } else {
            radio.pause();
            radio.src = ''; // Clear the source to stop receiving data
            buttonImage.src = 'play.svg';
        }
    });
</script>

<script>
    function updateDateTime() {
        fetch('get_jalali_time.php?year_offset=<?php echo $year_offset; ?>&day_offset=<?php echo $day_offset; ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById('datetime').innerHTML = data;
            })
            .catch(error => {
                const currentTime = new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
                const currentDate = new Date().toLocaleDateString('fa-IR', { weekday: 'long' });
                const dayMonthYear = new Date().toLocaleDateString('fa-IR', { day: 'numeric', month: 'long', year: 'numeric' });

                document.getElementById('datetime').innerHTML = `
                    <div style="text-align: center;">
                        <div style="color:#fff;font-size: 50px;">${currentDate}</div>
                        <div style="color:#58cc60; font-size: 90px; font-weight: bold;">${currentTime}</div>
                        <div style="color:#eccf00;font-size: 40px;">${dayMonthYear}</div>
                    </div>
                    <span style="color: red; font-size: 26px;">No Network  <br></span>
                `;
                console.error('Fetching data failed: ', error);
            });
    }
    // Update the date and time every 5 seconds
    setInterval(updateDateTime, 5000);
    // Initial call to display time immediately
    updateDateTime();
</script>

   <script>
function fetchWeather(latitude, longitude) {
    const xhr = new XMLHttpRequest();
    const url = `weather.php?latitude=${latitude}&longitude=${longitude}`;
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('weather').innerHTML = this.responseText;
        } else {
            console.error('Error fetching weather data.');
        }
    };
    xhr.send();
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            fetchWeather(latitude, longitude);
        }, () => {
            // GPS failed, use default location (Shiraz)
            fetchWeather(29.712912, 52.455597);
        });
    } else {
        // Geolocation not supported, use default location (Shiraz)
        fetchWeather(29.712912, 52.455597);
    }
}

// Refresh every hour (3600000 milliseconds)
setInterval(getLocation, 900000); // Adjusted to refresh every 15min
window.onload = getLocation; // Fetch immediately on load
</script>

  
    <script>
        // Function to set a cookie
        function setCookie(name, value, days) {
            const expires = new Date(Date.now() + days * 864e5).toUTCString();
            document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
        }

        // Function to get a cookie
        function getCookie(name) {
            return document.cookie.split('; ').reduce((r, v) => {
                const parts = v.split('=');
                return parts[0] === name ? decodeURIComponent(parts[1]) : r;
            }, '');
        }

        function getRandomDarkColor() {
            // Generate random dark colors using HSL
            const h = Math.floor(Math.random() * 360);
            const s = Math.floor(Math.random() * 100);
            const l = Math.floor(Math.random() * 50); // Lightness between 0-50 for dark colors
            return `hsl(${h}, ${s}%, ${l}%)`;
        }

        const colorButton = document.getElementById('colorButton');

        // Load background color from cookie
        const savedColor = getCookie('backgroundColor');
        if (savedColor) {
            document.body.style.backgroundColor = savedColor;
            const themeColorMeta = document.querySelector('meta[name="theme-color"]');
            if (themeColorMeta) {
                themeColorMeta.setAttribute('content', savedColor);
            }
        }

        colorButton.addEventListener('click', () => {
            // Change background color
            const newColor = getRandomDarkColor();
            document.body.style.backgroundColor = newColor;

            // Save the color to cookie
            setCookie('backgroundColor', newColor, 7); // Expires in 7 days

            // Update the theme color
            const themeColorMeta = document.querySelector('meta[name="theme-color"]');
            if (themeColorMeta) {
                themeColorMeta.setAttribute('content', newColor);
            }
        });

        document.addEventListener('keydown', (event) => {
            // Prevent default behavior if space key is pressed
            if (event.code === 'Space') {
                event.preventDefault(); // Prevent scrolling
            }
        });
    </script>
    <script>
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault(); // Prevent pinch to zoom
        });
    </script>
        <script>
        // Prevent scrolling on touch devices
        window.addEventListener('touchmove', function(event) {
            event.preventDefault();
        }, { passive: false });
    </script>
    
<script>
    let zoomLevel = 1;
    let startY = 0;

    // Function to get cookie value by name
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Function to set cookie
    function setCookie(name, value, days) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
    }

    // Load zoom level from cookie
    const savedZoomLevel = getCookie('zoomLevel');
    const contentElement = document.querySelector('.content'); // Select the content element

    if (savedZoomLevel && contentElement) {
        zoomLevel = parseFloat(savedZoomLevel);
        contentElement.style.transform = `scale(${zoomLevel})`; // Apply saved zoom level
    }

    if (contentElement) {
        contentElement.addEventListener('touchstart', function(event) {
            startY = event.touches[0].clientY; // Get starting Y position
        });

        contentElement.addEventListener('touchend', function(event) {
            const endY = event.changedTouches[0].clientY; // Get ending Y position
            const distance = startY - endY; // Calculate swipe distance

            if (Math.abs(distance) < 20) {
                // No significant swipe detected
            } else if (distance > 20) {
                // Swipe up detected
                zoomLevel += 0.1; // Zoom in
            } else if (distance < -20) {
                // Swipe down detected
                zoomLevel = Math.max(0.1, zoomLevel - 0.1); // Zoom out
            }

            contentElement.style.transform = `scale(${zoomLevel})`; // Apply zoom
            setCookie('zoomLevel', zoomLevel, 7); // Save zoom level in cookie for 7 days
        });
    }
</script>
    
    <script>
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault(); // Prevent the default context menu

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
    </script>
</body>
</html>