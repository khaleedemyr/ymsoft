@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('css')
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .content-wrapper {
            min-height: calc(100vh - 60px);
            padding: 0 !important;
            margin: 0 !important;
            font-family: 'Inter', sans-serif;
        }
        
        .weather-card {
            height: 100%;
            min-height: calc(100vh - 60px);
            margin: 0;
            border: none;
            border-radius: 0;
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            color: white;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        
        /* Tambahkan efek glassmorphism */
        .weather-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            z-index: 0;
        }
        
        .weather-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            padding: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .weather-card.morning {
            background: linear-gradient(120deg, #ff9a9e 0%, #fad0c4 100%);
        }
        
        .weather-card.afternoon {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
        }
        
        .weather-card.evening {
            background: linear-gradient(120deg, #a18cd1 0%, #fbc2eb 100%);
        }
        
        .weather-card.night {
            background: linear-gradient(120deg, #0c2b4b 0%, #204065 100%);
        }
        
        .time-section {
            font-family: 'Outfit', sans-serif;
            font-size: 6rem;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.1);
            letter-spacing: -2px;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #ffffff, rgba(255,255,255,0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .date-section {
            font-size: 1.5rem;
            font-weight: 500;
            opacity: 0.95;
            letter-spacing: 0.5px;
            text-transform: capitalize;
            margin-bottom: 2rem;
        }
        
        .greeting-section {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            margin: 2rem 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            background: linear-gradient(to right, #ffffff, rgba(255,255,255,0.9));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .weather-icon {
            font-size: 6rem;
            margin: 2rem 0;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.4));
        }
        
        /* Animasi untuk icon */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        
        .floating {
            animation: float 4s ease-in-out infinite;
        }
        
        .location-weather {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 1.2rem;
            margin-top: 2rem;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .location-weather:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .location-weather i {
            font-size: 1.6rem;
            filter: drop-shadow(0 0 4px rgba(255,255,255,0.4));
        }
        
        .weather-temp {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        
        .weather-desc {
            opacity: 0.95;
            font-weight: 500;
        }
        
        .divider {
            width: 2px;
            height: 24px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 2px;
        }

        /* Tambahkan particle effect */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            animation: moveParticle 15s infinite linear;
        }

        @keyframes moveParticle {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            50% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(-1000px) translateX(100px);
                opacity: 0;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .time-section {
                font-size: 4rem;
            }
            .weather-icon {
                font-size: 4rem;
            }
            .greeting-section {
                font-size: 2rem;
            }
            .location-weather {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="card weather-card">
        <!-- Particle effect -->
        <div class="particles" id="particles"></div>
        
        <div class="card-body text-center">
            <!-- Jam -->
            <div class="time-section mb-2" id="current-time">
                00:00:00
            </div>
            
            <!-- Tanggal -->
            <div class="date-section mb-4" id="current-date">
                Loading...
            </div>
            
            <!-- Icon Cuaca/Waktu -->
            <div class="weather-icon floating" id="time-icon">
                <i class="ri-sun-line"></i>
            </div>
            
            <!-- Ucapan -->
            <div class="greeting-section" id="greeting">
                Selamat Datang, {{ Auth::user()->nama_lengkap }}
            </div>

            <!-- Lokasi & Cuaca -->
            <div class="location-weather" id="location-weather">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ri-map-pin-line"></i>
                    <span id="location">Mencari lokasi...</span>
                </div>
                <div class="divider"></div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ri-temp-hot-line"></i>
                    <div id="weather">
                        <span class="weather-temp">--°C</span>
                        <span class="weather-desc">Memuat...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>

<script>
    function updateDateTime() {
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        const dateElement = document.getElementById('current-date');
        const iconElement = document.getElementById('time-icon');
        const greetingElement = document.getElementById('greeting');
        const card = document.querySelector('.weather-card');
        
        // Update jam
        timeElement.textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        
        // Update tanggal
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        dateElement.textContent = now.toLocaleDateString('id-ID', options);
        
        // Update icon dan ucapan berdasarkan waktu
        const hour = now.getHours();
        let greeting = '';
        let icon = '';
        
        if (hour >= 5 && hour < 11) {
            greeting = 'Selamat Pagi';
            icon = '<i class="ri-sun-line"></i>';
            card.className = 'card weather-card morning';
        } else if (hour >= 11 && hour < 15) {
            greeting = 'Selamat Siang';
            icon = '<i class="ri-sun-fill"></i>';
            card.className = 'card weather-card afternoon';
        } else if (hour >= 15 && hour < 18) {
            greeting = 'Selamat Sore';
            icon = '<i class="ri-sun-foggy-fill"></i>';
            card.className = 'card weather-card evening';
        } else {
            greeting = 'Selamat Malam';
            icon = '<i class="ri-moon-fill"></i>';
            card.className = 'card weather-card night';
        }
        
        iconElement.innerHTML = icon;
        greetingElement.textContent = `${greeting}, {{ Auth::user()->nama_lengkap }}`;
    }
    
    // Update setiap detik
    setInterval(updateDateTime, 1000);
    
    // Update pertama kali
    updateDateTime();

    // Fungsi untuk mendapatkan lokasi
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const { latitude, longitude } = position.coords;
                    // Reverse geocoding menggunakan Nominatim OpenStreetMap dengan parameter yang lebih spesifik
                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json&accept-language=id&addressdetails=1&zoom=10`)
                        .then(response => response.json())
                        .then(data => {
                            let location = '';
                            // Coba dapatkan kota/kabupaten dengan lebih presisi
                            if (data.address) {
                                if (data.address.city) {
                                    location = data.address.city;
                                } else if (data.address.municipality) {
                                    location = data.address.municipality;
                                } else if (data.address.town) {
                                    location = data.address.town;
                                } else if (data.address.county) {
                                    location = data.address.county;
                                } else if (data.address.state) {
                                    location = data.address.state;
                                }
                            }
                            document.getElementById('location').textContent = location || 'Lokasi tidak ditemukan';
                            
                            // Setelah dapat lokasi, ambil data cuaca
                            getWeather(latitude, longitude);
                        })
                        .catch(() => {
                            document.getElementById('location').textContent = 'Lokasi tidak tersedia';
                        });
                },
                error => {
                    document.getElementById('location').textContent = 'Lokasi tidak diizinkan';
                }
            );
        } else {
            document.getElementById('location').textContent = 'Geolokasi tidak didukung';
        }
    }

    // Fungsi untuk mendapatkan cuaca
    function getWeather(lat, lon) {
        fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code&timezone=Asia/Jakarta`)
            .then(response => response.json())
            .then(data => {
                const temp = Math.round(data.current.temperature_2m);
                const weatherCode = data.current.weather_code;
                const weatherDesc = getWeatherDescription(weatherCode);
                
                const weatherContainer = document.getElementById('weather');
                weatherContainer.innerHTML = `
                    <span class="weather-temp">${temp}°C</span>
                    <span class="weather-desc">${weatherDesc}</span>
                `;
            })
            .catch(() => {
                document.getElementById('weather').innerHTML = `
                    <span class="weather-temp">--°C</span>
                    <span class="weather-desc">Tidak tersedia</span>
                `;
            });
    }

    // Fungsi untuk mendapatkan deskripsi cuaca
    function getWeatherDescription(code) {
        const weatherCodes = {
            0: 'Cerah',
            1: 'Cerah Berawan',
            2: 'Berawan Sebagian',
            3: 'Berawan',
            45: 'Berkabut',
            48: 'Berkabut Tebal',
            51: 'Gerimis Ringan',
            53: 'Gerimis',
            55: 'Gerimis Lebat',
            61: 'Hujan Ringan',
            63: 'Hujan',
            65: 'Hujan Lebat',
            66: 'Hujan Dingin',
            67: 'Hujan Dingin Lebat',
            71: 'Salju Ringan',
            73: 'Salju',
            75: 'Salju Lebat',
            77: 'Butiran Salju',
            80: 'Hujan Ringan',
            81: 'Hujan',
            82: 'Hujan Lebat',
            85: 'Hujan Salju Ringan',
            86: 'Hujan Salju Lebat',
            95: 'Hujan Petir',
            96: 'Hujan Petir dengan Hujan Ringan',
            99: 'Hujan Petir dengan Hujan Lebat'
        };
        return weatherCodes[code] || 'Tidak Diketahui';
    }

    // Tambahkan fungsi untuk membuat particle effect
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random position
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            
            // Random size
            const size = Math.random() * 3 + 2;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            
            // Random animation duration
            particle.style.animationDuration = (Math.random() * 10 + 5) + 's';
            
            // Random delay
            particle.style.animationDelay = Math.random() * 5 + 's';
            
            particlesContainer.appendChild(particle);
        }
    }

    // Jalankan fungsi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 1000);
        getLocation();
        createParticles();
        
        // Update cuaca setiap 30 menit
        setInterval(() => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    getWeather(pos.coords.latitude, pos.coords.longitude);
                });
            }
        }, 30 * 60 * 1000);
    });
</script>
@endsection 