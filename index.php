<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecasting || App</title>
    <link rel="shortcut icon" href="icon.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
        <?php
        $apiKey = '3dec1de0c97532a73434b0d37116e9b0';
        $cityName = 'Abuja';
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$cityName}&units=metric&appid={$apiKey}";

        $response = file_get_contents($apiUrl);
        if ($response === FALSE) {
            die('Error occurred while fetching weather data.');
        }

        $weatherData = json_decode($response, true);
        if ($weatherData['cod'] !== 200) {
            die('Error: ' . $weatherData['message']);
        }

        // Extract necessary data
        $temperature = round($weatherData['main']['temp']);
        $condition = ucfirst($weatherData['weather'][0]['description']);
        $humidity = $weatherData['main']['humidity'];
        $windSpeed = $weatherData['wind']['speed'];
        $pressure = $weatherData['main']['pressure'];
        ?>
    <img class="background-image" src="weather.jpg" alt="Weather Background">
    
    <div class="container">
        <div class="search-container">
            <input style="background-color: #fff; color: #000;" type="text" placeholder="Search for a city..." id="cityInput">
            <button onclick="searchWeather()">Search</button>
        </div>

        <div class="weather-card">
            <div class="current-weather">
                <i class="wi wi-day-sunny weather-icon"></i>
                <div>
                    <h2 id="cityName" class="city-name">Abuja</h2>
                    <div id="temperature" class="temperature"><?php echo $temperature; ?>°C</div>
                    <div id="condition" class="condition"><?php echo $condition; ?></div>
                </div>
            </div>

            <div class="weather-details" style="display:flex; ">
                <div class="detail-card" style="padding: 50px;">
                    <div id="" class="label">Humidity</div>
                    <div id="humidity" class="value"><?php echo $humidity; ?>%</div>
                </div>
                <div class="detail-card" style="padding: 50px;">
                    <div class="label">Wind Speed</div>
                    <div id="windSpeed" class="value"><?php echo $windSpeed; ?> m/s</div>
                </div>
                <div class="detail-card" style="padding: 50px;">
                    <div class="label">Pressure</div>
                    <div id="pressure" class="value"><?php echo $pressure; ?> hPa</div>
                </div>
            </div>

            <div class="forecast">
                <div class="forecast-card">
                <div class="day"><?php echo date('D'); ?></div>
                       <i class="wi wi-day-cloudy"></i>
                    <div id="temperature" class="temperature"><?php echo $temperature; ?>°C</div>
                </div>
            </div>
        </div>
    </div>

    <div class="loading">Loading...</div>

    <script>
        const iconMap = {
            'clear': 'wi-day-sunny',
            'clouds': 'wi-cloudy',
            'rain': 'wi-rain',
            'thunderstorm': 'wi-thunderstorm',
            'snow': 'wi-snow',
            'mist': 'wi-fog'
        };

        async function searchWeather() {
            let city = document.getElementById("cityInput").value;

            if (city === "") {
                alert("Please enter a city name!");
                return;
            }

            const response = await fetch(`weather.php?city=${city}`);
            const data = await response.json();

            if (data.error) {
                alert("City not found!");
                return;
            }

            const temperatureElements = document.querySelectorAll('.temperature');
            document.getElementById("cityName").innerText = data.city;
            temperatureElements.forEach(element => {
                    element.innerText = `${data.temperature}°C`;
                });
            document.getElementById("humidity").innerText = `Humidity: ${data.humidity}%`;
            document.getElementById("condition").innerText = `Condition: ${data.condition}`;
            document.getElementById("windSpeed").innerText = `Wind Speed: ${data.wind_speed} m/s`;
            document.getElementById("pressure").innerText = `Pressure: ${data.pressure} hPa`;
        }

    </script>
</body>
</html>
