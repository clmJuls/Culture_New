<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div style="display: flex; align-items: center;">
           <img src="assets/logo/logo.png "alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <a href="home.php">Home</a>
            <a href="create-post.php">+ Create</a>
            <a href="explore.php">Explore</a>
            <a href="notification.php">Notification</a>
            <div class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
            <a href="generate_report.php">Generate Report</a>
            <a href="#" onclick="handleLogout()">Log Out</a>
        </div>
    </div>

    <style>
    /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #365486;
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar img {
            height: 50px;
            width: auto;
        }

        .navbar h1 {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .navbar a {
            color: #DCF2F1;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        .navbar a.active {
            background-color: #1e3c72;
            color: #fff;
        }
        
    /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

    /* Toggle class for show/hide */
        .show {
            display: block;
        }

         .main-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .location-container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        /* Title */
.location-container h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #333;
  text-align: center;
}

/* Form Groups */
.form-group {
  margin-bottom: 15px;
}

label {
  font-size: 1rem;
  display: block;
  font-weight: bold;
  color: #555;
  margin-bottom: 5px;
}

.required {
  color: red;
}

/* Dropdown Styling */
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  color: #333;
  background-color: #fff;
}

select:focus {
  outline: none;
  border-color: #007bff;
}

/* Submit Button */
.submit-btn {
  display: block;
  width: 100%;
  padding: 10px;
  background-color: #007bff;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  text-align: center;
  margin-top: 10px;
}

.submit-btn:hover {
  background-color: #0056b3;
}

 /* Sidebar */
 .sidebar {
    position: fixed;
    top: 60px; 
    left: 0;
    width: 240px;  
    height: 100vh;
    background-color: #365486;
    padding-top: 30px;
    z-index: 999; 
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
    flex-grow: 1;
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    border-radius: 0 5px 5px 0;
}

/* Logo Section */
.logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 15px;
  margin-bottom: 15px;
}

.logo-section img {
  max-width: 100px;
  border-radius: 5px;
}

/* Section Menus */
.menu-section {
  margin-bottom: 10px;
}

.menu-section h3 {
  font-size: 15px;
  margin-bottom: 8px;
  color: #DCF2F1;
}

/* Menu Items */
.menu-item {
  display: inline-block;
  align-items: center;
  justify-content: flex-start;
  margin: 3px 0;
  cursor: pointer;
  transition: background 0.2s ease;
  padding: 5px 5px;
  border-radius: 4px;
  color: #ffffff;
}

.menu-item a {
    color: #ffffff;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 30px;
}

.menu-item a:hover {
    background-color: #7FC7D9;
    color: #0F1035;
}

.menu-item a.active {
    background-color: #1e3c72;
    color: #fff;
}

.menu-item ul {
    list-style: none;
    padding: 0;
}
  
.menu-item li {
    margin-bottom: 10px;
    font-size: .8rem;
}
  
input[type="checkbox"] {
    margin-right: 5px;
}

#chosen-location-container {
    margin-top: 20px; 
    display: block;
}

#chosen-location-container label {
    font-size: 12px; 
    color: #ffffff;
}
</style>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            dropdownContent.classList.toggle("show");
        }
        function handleLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'auth/logout.php';
            }
        }
    </script>

    <!-- Main Content -->
    <div class="main-container">
        <div class="location-container">
            <h2>Choose a Location</h2>
            <form id="location-form" action="#" method="post">
                <div class="form-group">
                    <label for="continent">Continent <span class="required">*</span></label>
                    <select id="continent" name="continent" required>
                      <option value="option">- Select a Continent -</option>
                      <option value="africa">AFRICA</option>
                      <option value="asia">ASIA</option>
                      <option value="australia">AUSTRALIA</option>
                      <option value="europe">EUROPE</option>
                      <option value="north-america">NORTH AMERICA</option>
                      <option value="south-america">SOUTH AMERICA</option>
                    </select>
                  </div>
                  
                <div class="form-group">
                    <label for="country">Country <span class="required">*</span></label>
                    <select id="country" name="country" required>
                        <option value="option">- Select a Country -</option>
                    </select>
                </div>  

            <button type="submit" class="submit-btn">Submit</button>
          </form>
        </div>
    </div>

    <script>
const continentData = {
  africa: [
    "ALGERIA", "ANGOLA", "BENIN", "BOTSWANA", "BURKINA FASO", "BURUNDI", "CABO VERDE", "CAMEROON", "CENTRAL AFRICAN REPUBLIC", 
    "CHAD", "COMOROS", "CONGO", "CONGO (DEMOCRATIC REPUBLIC)", "DJIBOUTI", "EGYPT", "EQUATORIAL GUINEA", "ERITREA", "ESWATINI", 
    "ETHIOPIA", "GABON", "GAMBIA", "GHANA", "GUINEA", "GUINEA-BISSAU", "IVORY COAST", "KENYA", "LESOTHO", "LIBERIA", 
    "LIBYA", "MADAGASCAR", "MALAWI", "MALI", "MAURITANIA", "MAURITIUS", "MOROCCO", "MOZAMBIQUE", "NAMIBIA", "NIGER", "NIGERIA", 
    "RWANDA", "SAO TOME AND PRINCIPE", "SENEGAL", "SEYCHELLES", "SIERRA LEONE", "SOMALIA", "SOUTH AFRICA", "SOUTH SUDAN", 
    "SUDAN", "TANZANIA", "TOGO", "TUNISIA", "UGANDA", "ZAMBIA", "ZIMBABWE"
  ],
  asia: [
    "AFGHANISTAN", "ARMENIA", "AZERBAIJAN", "BAHRAIN", "BANGLADESH", "BHUTAN", "BRUNEI", "BURMA (MYANMAR)", "CAMBODIA", 
    "CHINA", "EAST TIMOR", "GEORGIA", "INDIA", "INDONESIA", "IRAN", "IRAQ", "ISRAEL", "JAPAN", "JORDAN", "KAZAKHSTAN", 
    "KOREA (NORTH)", "KOREA (SOUTH)", "KUWAIT", "KYRGYZSTAN", "LAOS", "LEBANON", "MALAYSIA", "MALDIVES", "MONGOLIA", "NEPAL", 
    "OMAN", "PAKISTAN", "PHILIPPINES", "QATAR", "SAUDI ARABIA", "SINGAPORE", "SRI LANKA", "SYRIA", "TAIWAN", "TAJIKISTAN", 
    "THAILAND", "TURKMENISTAN", "UNITED ARAB EMIRATES", "UZBEKISTAN", "VIETNAM", "YEMEN"
  ],
  australia: [
    "AUSTRALIA", "FIJI", "KIRIBATI", "MARSHALL ISLANDS", "MICRONESIA", "NAURU", "NEW ZEALAND", "PALAU", "PAPUA NEW GUINEA", 
    "SAMOA", "SOLOMON ISLANDS", "TONGA", "TUVALU", "VANUATU"
  ],
  europe: [
    "ALBANIA", "ANDORRA", "ARMENIA", "AUSTRIA", "AZERBAIJAN", "BELARUS", "BELGIUM", "BOSNIA AND HERZEGOVINA", "BULGARIA", "CROATIA", 
    "CYPRUS", "CZECH REPUBLIC", "DENMARK", "ESTONIA", "FINLAND", "FRANCE", "GEORGIA", "GERMANY", "GREECE", "HUNGARY", "ICELAND", 
    "IRELAND", "ITALY", "KAZAKHSTAN", "KOSOVO", "LATVIA", "LIECHTENSTEIN", "LITHUANIA", "LUXEMBOURG", "MALTA", "MOLDOVA", 
    "MONACO", "MONTENEGRO", "NETHERLANDS", "NORTH MACEDONIA", "NORWAY", "POLAND", "PORTUGAL", "ROMANIA", "RUSSIA", "SAN MARINO", 
    "SERBIA", "SLOVAKIA", "SLOVENIA", "SPAIN", "SWEDEN", "SWITZERLAND", "TURKMENISTAN", "UKRAINE", "UNITED KINGDOM"
  ],
  north_america: [
    "UNITED STATES", "CANADA", "MEXICO", "ANTIGUA AND BARBUDA", "BAHAMAS", "BARBADOS", "BELIZE", "COSTA RICA", "CUBA", "DOMINICA", 
    "DOMINICAN REPUBLIC", "GRENADA", "GUATEMALA", "HAITI", "HONDURAS", "JAMAICA", "MARTINIQUE", "MEXICO", "NICARAGUA", 
    "PANAMA", "SAINT KITTS AND NEVIS", "SAINT LUCIA", "SAINT VINCENT AND THE GRENADINES", "TRINIDAD AND TOBAGO", 
    "UNITED STATES VIRGIN ISLANDS", "BERMUDA", "CAYMAN ISLANDS"
  ],
  south_america: [
    "ARGENTINA", "BOLIVIA", "BRAZIL", "CHILE", "COLOMBIA", "ECUADOR", "GUYANA", "PARAGUAY", "PERU", "SURINAME", "URUGUAY", "VENEZUELA"
  ]
};
</script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
    </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                    <li><a href="geography.php">Geography</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="demographics.php">Demographics</a></li>
                <li><a href="culture.php">Culture</a></li>
                </ul>
            </div>

        <div class="menu-section">
            <h3>Learning Styles</h3>
            <div class="menu-item">
                <ul>
                    <li><input type="checkbox">Visual</li>
                    <li><input type="checkbox">Auditory & Oral</li>
                    <li><input type="checkbox">Read & Write</li>
                    <li><input type="checkbox">Kinesthetic</li>
                </ul>
            </div>

        <div class="menu-section">
            <h3>Location</h3>
            <div class="menu-item">
                <a href="choose-loc.php" class="active"><span>+</span> Choose a location</a>
                <div id="chosen-location-container"></div>
            </div>
        </div>
        
    <div class="menu-section">
      <h3>Resources</h3>
      <div class="menu-item">
        <span>🔗</span>
        <a href="#">About Kulturifiko</a>
      </div>
    </div>
  </div>

 <script>
    // Get form elements
const locationForm = document.getElementById("location-form");
const countryDropdown = document.getElementById("country");

// Handle form submission
locationForm.addEventListener("submit", function (event) {
    event.preventDefault(); 

    // Get the selected country value
    const country = countryDropdown.options[countryDropdown.selectedIndex].text;

    // Check if country is selected and update the sidebar
    if (country) {
        const chosenLocation = `
            <label>
                <input type="checkbox" name="selected-location" value="${country}">
                ${country}
            </label>
        `;
        const chosenLocationContainer = document.getElementById("chosen-location-container");
        chosenLocationContainer.innerHTML = chosenLocation;
    }
    
    // Optional: Reset the form after submission
    locationForm.reset();
});

// Populate country options based on selected continent
document.getElementById("continent").addEventListener("change", function () {
    const selectedContinent = this.value;
    countryDropdown.innerHTML = '<option value="">- Select a Country -</option>';
    if (continentData[selectedContinent]) {
        continentData[selectedContinent].forEach((country) => {
            const option = document.createElement("option");
            option.value = country.toLowerCase().replace(/\s+/g, "-");
            option.textContent = country;
            countryDropdown.appendChild(option);
        });
    }
});
 </script> 

</body>
</html>