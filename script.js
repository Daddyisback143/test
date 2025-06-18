// Toggle mobile navigation menu
const navToggler = document.querySelector('.nav-toggler');
const navLinks = document.querySelector('.nav-links');
const nav = document.querySelector('.navbar');

navToggler.addEventListener('click', () => {
    navLinks.classList.toggle('open');
});

// Close mobile menu when a link is clicked
document.querySelectorAll('.nav-links li a').forEach(link => {
    link.addEventListener('click', () => {
        if(navLinks.classList.contains('open')){
            navLinks.classList.remove('open');
        }
    });
});

// Change navbar style on scroll
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});

// --- BOOKING FORM FUNCTIONALITY ---
function updateTravelerFields() {
    const numTravelers = parseInt(document.getElementById('num_travelers').value) || 0;
    const travelerContainer = document.getElementById('traveler-fields');
    
    if (!travelerContainer) return;
    
    travelerContainer.innerHTML = '';
    
    for (let i = 1; i <= numTravelers; i++) {
        const travelerDiv = document.createElement('div');
        travelerDiv.className = 'traveler-field';
        travelerDiv.innerHTML = `
            <h4>Traveler ${i}</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="traveler_name_${i}">Full Name *</label>
                    <input type="text" id="traveler_name_${i}" name="traveler_name_${i}" required>
                </div>
                <div class="form-group">
                    <label for="traveler_age_${i}">Age *</label>
                    <input type="number" id="traveler_age_${i}" name="traveler_age_${i}" min="1" max="120" required>
                </div>
                <div class="form-group">
                    <label for="traveler_gender_${i}">Gender *</label>
                    <select id="traveler_gender_${i}" name="traveler_gender_${i}" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="traveler_mobile_${i}">Mobile Number *</label>
                    <input type="tel" id="traveler_mobile_${i}" name="traveler_mobile_${i}" pattern="[0-9]{10}" required>
                </div>
                <div class="form-group">
                    <label for="traveler_email_${i}">Email *</label>
                    <input type="email" id="traveler_email_${i}" name="traveler_email_${i}" required>
                </div>
            </div>
        `;
        travelerContainer.appendChild(travelerDiv);
    }
}

// Calculate fares based on distance and mode
function calculateFares(source, destination, numTravelers) {
    // Simple fare calculation based on distance
    const distances = {
        'mumbai-delhi': 1400,
        'mumbai-goa': 600,
        'mumbai-bangalore': 1000,
        'delhi-jaipur': 300,
        'delhi-agra': 200,
        'bangalore-chennai': 350,
        'chennai-hyderabad': 600,
        'hyderabad-bangalore': 500
    };
    
    const route = `${source.toLowerCase()}-${destination.toLowerCase()}`;
    const reverseRoute = `${destination.toLowerCase()}-${source.toLowerCase()}`;
    const distance = distances[route] || distances[reverseRoute] || 500; // Default 500km
    
    const baseFares = {
        flight: distance * 2.5, // ₹2.5 per km
        train: distance * 0.8,  // ₹0.8 per km
        bus: distance * 0.4     // ₹0.4 per km
    };
    
    return {
        flight: Math.round(baseFares.flight * numTravelers),
        train: Math.round(baseFares.train * numTravelers),
        bus: Math.round(baseFares.bus * numTravelers)
    };
}

// Show fare options
function showFareOptions(source, destination, numTravelers) {
    // Validate input
    if (!source || !destination || !numTravelers) {
        alert('Please fill in all fields');
        return;
    }
    
    // Check if source and destination are the same
    if (source.toLowerCase() === destination.toLowerCase()) {
        alert('Source and destination cities cannot be the same. Please choose different cities.');
        return;
    }
    
    // Check if date is in the past
    const date = document.getElementById('date').value;
    if (date) {
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to start of day
        
        if (selectedDate < today) {
            alert('Cannot book tickets for past dates. Please select a future date.');
            return;
        }
    }
    
    const fares = calculateFares(source, destination, numTravelers);
    
    document.getElementById('flight-fare').textContent = fares.flight.toLocaleString();
    document.getElementById('train-fare').textContent = fares.train.toLocaleString();
    document.getElementById('bus-fare').textContent = fares.bus.toLocaleString();
    
    document.getElementById('fare-options').style.display = 'block';
    document.getElementById('booking-form').style.display = 'none';
    
    // Store fare data for later use
    window.selectedFares = {
        source: source,
        destination: destination,
        numTravelers: numTravelers,
        fares: fares
    };
}

// Handle fare selection
function selectFare(mode) {
    const fareData = window.selectedFares;
    const fare = fareData.fares[mode];
    const perPerson = Math.round(fare / fareData.numTravelers);
    
    // Store selected fare data
    window.selectedFareData = {
        type: mode,
        source: fareData.source,
        destination: fareData.destination,
        date: document.getElementById('date').value,
        num_travelers: fareData.numTravelers,
        fare: fare,
        per_person: perPerson
    };
    
    // Show traveler form
    document.getElementById('fare-options').style.display = 'none';
    document.getElementById('traveler-form').style.display = 'block';
    
    // Update traveler fields
    updateTravelerFields();
}

// Function to handle booking form submission
function handleBookingSubmit(formData, isPackage = false) {
    // Validate booking data
    const source = formData.get('source');
    const destination = formData.get('destination');
    const date = formData.get('date');
    
    // Check if source and destination are the same
    if (source.toLowerCase() === destination.toLowerCase()) {
        const resultDiv = document.getElementById('booking-result');
        resultDiv.innerHTML = `
            <div class="booking-error">
                ❌ Source and destination cities cannot be the same. Please choose different cities.
            </div>
        `;
        return;
    }
    
    // Check if date is in the past
    const selectedDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time to start of day
    
    if (selectedDate < today) {
        const resultDiv = document.getElementById('booking-result');
        resultDiv.innerHTML = `
            <div class="booking-error">
                ❌ Cannot book tickets for past dates. Please select a future date.
            </div>
        `;
        return;
    }
    
    const resultDiv = document.getElementById('booking-result');
    resultDiv.innerHTML = '<div style="text-align: center; padding: 20px;">Processing booking...</div>';
    
    fetch(isPackage ? 'php/book.php' : 'php/book.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Booking response:', data); // Debug log
        
        if (data.status === 'success') {
            const html = atob(data.html);
            
            let downloadButtons = '';
            if (data.pdf) {
                downloadButtons += `<a href="data:application/pdf;base64,${data.pdf}" download="ticket.pdf" class="btn btn-primary">Download PDF</a>`;
            }
            downloadButtons += `<a href="data:text/html;base64,${data.html}" download="ticket.html" class="btn btn-secondary">Download HTML</a>`;
            
            resultDiv.innerHTML = `
                <div class="booking-success">
                    <h3>✅ ${data.msg}</h3>
                    <div class="ticket-preview">
                        ${html}
                    </div>
                    <div class="download-buttons">
                        ${downloadButtons}
                    </div>
                </div>
            `;
            
            // Reset forms
            document.getElementById('booking-form').style.display = 'block';
            document.getElementById('fare-options').style.display = 'none';
            document.getElementById('traveler-form').style.display = 'none';
            document.getElementById('booking-form').reset();
        } else {
            resultDiv.innerHTML = `
                <div class="booking-error">
                    ❌ ${data.msg}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Booking error:', error); // Debug log
        resultDiv.innerHTML = `
            <div class="booking-error">
                ❌ Booking failed. Please try again. Error: ${error.message}
            </div>
        `;
    });
}

// Function to handle package booking submission
function handlePackageBookingSubmit(formData) {
    const resultDiv = document.getElementById('package-booking-result');
    resultDiv.innerHTML = '<div style="text-align: center; padding: 20px;">Processing package booking...</div>';
    
    fetch('php/book.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Package booking response:', data); // Debug log
        
        if (data.status === 'success') {
            const html = atob(data.html);
            const pdf = atob(data.pdf);
            
            resultDiv.innerHTML = `
                <div class="booking-success">
                    <h3>✅ ${data.msg}</h3>
                    <div class="ticket-preview">
                        ${html}
                    </div>
                    <div class="download-buttons">
                        <a href="data:application/pdf;base64,${data.pdf}" download="package_booking.pdf" class="btn btn-primary">Download PDF</a>
                        <a href="data:text/html;base64,${data.html}" download="package_booking.html" class="btn btn-secondary">Download HTML</a>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="booking-error">
                    ❌ ${data.msg}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Package booking error:', error); // Debug log
        resultDiv.innerHTML = `
            <div class="booking-error">
                ❌ Package booking failed. Please try again. Error: ${error.message}
            </div>
        `;
    });
}

// --- Package Details Modal/Page Logic ---
document.querySelectorAll('.view-package-btn').forEach(btn => {
  btn.onclick = function() {
    const pkg = btn.getAttribute('data-package');
    // For now, open details.php as a placeholder (can be replaced with a new page or modal)
    window.open(`details.php?type=package&name=${encodeURIComponent(pkg)}`, '_blank', 'width=800,height=700');
  };
});

// --- Dynamic Package Data and Rendering ---
const domesticPackages = [
  {
    name: "Goa Beach Getaway",
    days: 5,
    nights: 4,
    cities: ["Panaji", "Calangute", "Baga"],
    price_per_person: 18999,
    meals: "Included",
    hotels: ["Taj Vivanta", "Beach Bay Resort"],
    travel_modes: ["Flight", "Cab"],
    image: "https://media.istockphoto.com/id/157579910/photo/the-beach.jpg?s=612x612&w=0&k=20&c=aMk67AmzIVD_S1Nibww8ytUdyub2ck3HNQ3uTvuPWPI=",
    description: "5 days, 4 nights. Explore North & South Goa beaches, nightlife, and spice plantation tour."
  },
  {
    name: "Himachal Adventure Escape",
    days: 7,
    nights: 6,
    cities: ["Shimla", "Manali", "Solang Valley"],
    price_per_person: 21999,
    meals: "Included",
    hotels: ["Snow Valley Resort", "Apple Country Resort"],
    travel_modes: ["Train", "Bus", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTeyWGGWqyUwuDE4tw2HBRyoH7aBldHGk8JQ&s",
    description: "7 days, 6 nights. Enjoy snow activities, scenic valleys, and local sightseeing."
  },
  {
    name: "Kerala Backwaters Retreat",
    days: 6,
    nights: 5,
    cities: ["Kochi", "Munnar", "Alleppey"],
    price_per_person: 20499,
    meals: "Included",
    hotels: ["Spice Jungle Resort", "Houseboat Stay"],
    travel_modes: ["Flight", "Cab", "Boat"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTHrnyvcXzeXzyAeGYn9eWncSPM7G_st8yfRg&s",
    description: "6 days, 5 nights. Tea gardens, wildlife safari, houseboat cruise through backwaters."
  },
  {
    name: "Golden Triangle Tour",
    days: 6,
    nights: 5,
    cities: ["Delhi", "Agra", "Jaipur"],
    price_per_person: 17999,
    meals: "Included",
    hotels: ["Radisson Blu", "Hotel Clarks Shiraz"],
    travel_modes: ["Train", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSn00QIIVRkAOhOBvypNAdU4ggEx930HARjA&s",
    description: "6 days, 5 nights. Visit Taj Mahal, Jaipur forts, and heritage sites."
  },
  {
    name: "Kashmir Paradise Package",
    days: 5,
    nights: 4,
    cities: ["Srinagar", "Gulmarg", "Pahalgam"],
    price_per_person: 22999,
    meals: "Included",
    hotels: ["Heevan Resort", "Khyber Himalayan Resort"],
    travel_modes: ["Flight", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVdZK6HqqNWTBv_-K7dYvOGmVFeGvt2WYfOQ&s",
    description: "5 days, 4 nights. Enjoy snow, gardens, Dal Lake shikara ride and sightseeing."
  },
  {
    name: "Andaman Island Escape",
    days: 6,
    nights: 5,
    cities: ["Port Blair", "Havelock Island", "Neil Island"],
    price_per_person: 25499,
    meals: "Included",
    hotels: ["Coral Reef Resort", "Symphony Palms Beach"],
    travel_modes: ["Flight", "Boat"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQauB4DKO11HXk5wxJbeZ3w3jFh-fdZnZuKew&s",
    description: "6 days, 5 nights. Crystal-clear waters, scuba diving and beach vibes."
  },
  {
    name: "Rajasthan Royal Trail",
    days: 7,
    nights: 6,
    cities: ["Udaipur", "Jodhpur", "Jaisalmer"],
    price_per_person: 20999,
    meals: "Included",
    hotels: ["Taj Lake Palace", "Desert Camp"],
    travel_modes: ["Train", "Cab"],
    image: "https://www.tripplannersindia.com/assets/images/page/6_Nights_7_Days_Royal_Rajasthan_Tour_Package.jpg",
    description: "7 days, 6 nights. Forts, palaces, desert safari, and culture of Rajasthan."
  },
  {
    name: "Meghalaya & Assam Wonders",
    days: 6,
    nights: 5,
    cities: ["Shillong", "Cherrapunji", "Kaziranga"],
    price_per_person: 21999,
    meals: "Included",
    hotels: ["Ri Kynjai Resort", "IORA Retreat"],
    travel_modes: ["Flight", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLEq4-MVGvGWmWOjDLyv9SRhRWB053Sus62w&s",
    description: "6 days, 5 nights. Waterfalls, caves, root bridges and rhino safari."
  },
  {
    name: "Sikkim Scenic Escape",
    days: 5,
    nights: 4,
    cities: ["Gangtok", "Tsomgo Lake", "Nathula Pass"],
    price_per_person: 19999,
    meals: "Included",
    hotels: ["Lemon Tree Hotel", "The Royal Plaza"],
    travel_modes: ["Flight", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMajMFaMMYBcDLdlL2_xoDqn6E4otZp1p8uA&s",
    description: "5 days, 4 nights. Mountain views, monasteries, and Indo-China border."
  },
  {
    name: "Trekking in Ladakh",
    days: 8,
    nights: 7,
    cities: ["Leh", "Nubra Valley", "Pangong Lake"],
    price_per_person: 29999,
    meals: "Included",
    hotels: ["Hotel Ladakh Residency", "Campsite at Pangong"],
    travel_modes: ["Flight", "Cab"],
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQbcQYDs10I2Yahp0YNzuIw8iyolWhpEvrFeQ&s",
    description: "8 days, 7 nights. High-altitude desert adventure, monasteries, and lake stays."
  }
];

function renderPackages(packages, containerId, limit = null) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = '';
  (limit ? packages.slice(0, limit) : packages).forEach(pkg => {
    const card = document.createElement('div');
    card.className = 'package-card';
    card.innerHTML = `
      <img src="${pkg.image || pkg.img}" alt="${pkg.name}">
      <div class="package-info">
        <h3>${pkg.name}</h3>
        <div class="package-meta">
          ${pkg.days || pkg.nights ? `${pkg.days} Days / ${pkg.nights} Nights<br>` : ''}
          Cities: ${(pkg.cities || []).join(', ')}<br>
          Travel: ${(pkg.travel_modes || []).join(', ')}<br>
          Meals: ${pkg.meals || ''}<br>
          Hotels: ${(pkg.hotels || []).join(', ')}
        </div>
        <div class="package-price">&#8377;${(pkg.price_per_person || pkg.price).toLocaleString('en-IN')} per person</div>
        <div class="package-desc">${pkg.description || pkg.desc}</div>
        <button class="btn view-package-btn" data-package="${pkg.name}">View Details</button>
      </div>
    `;
    card.style.cursor = 'pointer';
    card.onclick = function(e) {
      if (e.target.classList.contains('view-package-btn')) return;
      window.open(`details.php?type=package&name=${encodeURIComponent(pkg.name)}`, '_blank', 'width=800,height=600');
    };
    card.querySelector('.view-package-btn').onclick = function(e) {
      e.stopPropagation();
      window.open(`details.php?type=package&name=${encodeURIComponent(pkg.name)}`, '_blank', 'width=800,height=600');
    };
    container.appendChild(card);
  });
}

// --- International Packages: Use the same structure as domestic ---
const internationalPackages = [
  {
    name: "Maldives Explorer",
    days: 4,
    nights: 3,
    cities: ["Malé", "Maafushi", "Banana Reef"],
    price_per_person: 24999,
    meals: "Included",
    hotels: ["Paradise Island Resort", "Maafushi Inn"],
    travel_modes: ["Flight", "Boat"],
    image: "https://th.bing.com/th/id/OIP.7JulycclRJoDpx0FsDdE9wHaEK?w=321&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "4 days, 3 nights. Maafushi, Banana Reef, city tour."
  },
  {
    name: "Thailand Delight",
    days: 5,
    nights: 4,
    cities: ["Bangkok", "Pattaya"],
    price_per_person: 28999,
    meals: "Included",
    hotels: ["The Berkeley Hotel", "Avani Pattaya"],
    travel_modes: ["Flight", "Cab"],
    image: "https://tse1.mm.bing.net/th/id/OIP.XmqaNqgqbbW9yJU2WfjbSgHaEo?r=0&rs=1&pid=ImgDetMain",
    description: "5 days, 4 nights. Coral island tour, shopping, nightlife."
  },
  {
    name: "Dubai Luxury Tour",
    days: 4,
    nights: 3,
    cities: ["Dubai", "Abu Dhabi"],
    price_per_person: 37999,
    meals: "Included",
    hotels: ["Burj Al Arab View Hotel", "City Max"],
    travel_modes: ["Flight", "Cab"],
    image: "https://th.bing.com/th/id/OIP.nugEpuXjt5l18PBRAsHwSgHaEK?w=321&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "4 days, 3 nights. Burj Khalifa, desert safari, city tour."
  },
  {
    name: "Singapore City Fun",
    days: 5,
    nights: 4,
    cities: ["Singapore"],
    price_per_person: 40999,
    meals: "Included",
    hotels: ["Marina Bay Sands", "Hotel Boss"],
    travel_modes: ["Flight", "Cab", "Metro"],
    image: "https://th.bing.com/th/id/OIP.wZHCD6w-OQIJ-qaEj9pghwHaE8?w=274&h=183&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "5 days, 4 nights. Gardens by the Bay, Universal Studios, city tour."
  },
  {
    name: "Bali Romantic Escape",
    days: 6,
    nights: 5,
    cities: ["Ubud", "Seminyak"],
    price_per_person: 35999,
    meals: "Included",
    hotels: ["Komaneka Resort", "W Hotel Seminyak"],
    travel_modes: ["Flight", "Cab"],
    image: "https://th.bing.com/th/id/OIP.e4fy_H5A-PP3qEiB1_DsywHaE8?w=282&h=188&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "6 days, 5 nights. Temples, beaches, rice terraces and spa experiences."
  },
  {
    name: "Paris and Swiss Alps Tour",
    days: 7,
    nights: 6,
    cities: ["Paris", "Lucerne", "Interlaken"],
    price_per_person: 99999,
    meals: "Included",
    hotels: ["Ibis Paris", "Hotel Alpina"],
    travel_modes: ["Flight", "Train"],
    image: "https://th.bing.com/th/id/OIP.ICiz94DwJm5ioU5YbWBvKQHaEK?w=277&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "7 days, 6 nights. Eiffel Tower, Mt. Titlis, Jungfrau and boat rides."
  },
  {
    name: "Japan Explorer",
    days: 8,
    nights: 7,
    cities: ["Tokyo", "Kyoto", "Osaka"],
    price_per_person: 119999,
    meals: "Included",
    hotels: ["Shinjuku Granbell", "Kyoto Tower Hotel"],
    travel_modes: ["Flight", "Bullet Train"],
    image: "https://th.bing.com/th/id/OIP.vWa74pGyfniF_022-LGbFAHaE8?w=228&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "8 days, 7 nights. Temples, Mt. Fuji, cherry blossoms, and bullet train ride."
  },
  {
    name: "Turkey Heritage Tour",
    days: 6,
    nights: 5,
    cities: ["Istanbul", "Cappadocia", "Pamukkale"],
    price_per_person: 74999,
    meals: "Included",
    hotels: ["Sultanahmet Hotel", "Cave Suite Hotel"],
    travel_modes: ["Flight", "Bus"],
    image: "https://th.bing.com/th/id/OIP.pEnCD9C6CbLSrY-A_pv3NwHaE8?w=257&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "6 days, 5 nights. Hot air balloon ride, Blue Mosque, thermal springs."
  },
  {
    name: "New Zealand Adventure",
    days: 9,
    nights: 8,
    cities: ["Auckland", "Queenstown", "Rotorua"],
    price_per_person: 149999,
    meals: "Included",
    hotels: ["Novotel", "SkyCity Hotel"],
    travel_modes: ["Flight", "Cab", "Train"],
    image: "https://th.bing.com/th/id/OIP.cZvZqWgyk5YslMXb9ZMAHQHaE8?w=239&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "9 days, 8 nights. Adventure sports, Maori culture, and natural wonders."
  },
  {
    name: "USA East Coast Highlights",
    days: 10,
    nights: 9,
    cities: ["New York", "Washington D.C.", "Niagara Falls"],
    price_per_person: 159999,
    meals: "Included",
    hotels: ["Marriott Marquis", "Embassy Suites"],
    travel_modes: ["Flight", "Bus"],
    image: "https://th.bing.com/th/id/OIP.a6TlqRxGYLX1BFn9pNVRLAHaEK?w=318&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    description: "10 days, 9 nights. Statue of Liberty, Capitol Hill, Niagara boat cruise."
  }
];

window.addEventListener('DOMContentLoaded', function() {
  // Add event listeners for booking forms
  const numTravelersInput = document.getElementById('num_travelers');
  if (numTravelersInput) {
    numTravelersInput.addEventListener('change', updateTravelerFields);
    // Initialize traveler fields on page load
    updateTravelerFields();
  }
  
  // Step 1: Basic booking form (Get Fares)
  const bookingForm = document.querySelector('.booking-form');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validate source and destination are not the same
      const source = this.querySelector('[name="source"]').value.trim();
      const destination = this.querySelector('[name="destination"]').value.trim();
      const numTravelers = parseInt(this.querySelector('[name="num_travelers"]').value);
      const date = this.querySelector('[name="date"]').value;
      
      if (source.toLowerCase() === destination.toLowerCase()) {
        document.getElementById('booking-result').innerHTML = `
          <div class="booking-error">❌ Source and destination cannot be the same. Please choose different cities.</div>
        `;
        return;
      }
      
      // Validate all required fields
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.style.borderColor = '#dc3545';
        } else {
          field.style.borderColor = '#e0eafc';
        }
      });
      
      if (!isValid) {
        document.getElementById('booking-result').innerHTML = `
          <div class="booking-error">❌ Please fill in all required fields.</div>
        `;
        return;
      }
      
      // Show fare options
      showFareOptions(source, destination, numTravelers);
    });
  }
  
  // Step 2: Fare selection buttons
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('select-fare-btn')) {
      const mode = e.target.getAttribute('data-mode');
      selectFare(mode);
    }
  });
  
  // Step 3: Traveler form submission
  const travelerForm = document.getElementById('traveler-form');
  if (travelerForm) {
    travelerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validate all required fields
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.style.borderColor = '#dc3545';
        } else {
          field.style.borderColor = '#e0eafc';
        }
      });
      
      if (!isValid) {
        document.getElementById('booking-result').innerHTML = `
          <div class="booking-error">❌ Please fill in all required fields.</div>
        `;
        return;
      }
      
      // Create form data with all booking information
      const formData = new FormData();
      const fareData = window.selectedFareData;
      
      // Add fare data
      formData.append('type', fareData.type);
      formData.append('source', fareData.source);
      formData.append('destination', fareData.destination);
      formData.append('date', fareData.date);
      formData.append('num_travelers', fareData.num_travelers);
      formData.append('fare', fareData.fare);
      formData.append('per_person', fareData.per_person);
      
      // Add traveler details
      for (let i = 1; i <= fareData.num_travelers; i++) {
        formData.append(`traveler_name_${i}`, document.getElementById(`traveler_name_${i}`).value);
        formData.append(`traveler_age_${i}`, document.getElementById(`traveler_age_${i}`).value);
        formData.append(`traveler_gender_${i}`, document.getElementById(`traveler_gender_${i}`).value);
        formData.append(`traveler_mobile_${i}`, document.getElementById(`traveler_mobile_${i}`).value);
        formData.append(`traveler_email_${i}`, document.getElementById(`traveler_email_${i}`).value);
      }
      
      handleBookingSubmit(formData, false);
    });
  }
  
  // Package booking form
  const packageBookingForm = document.querySelector('.package-booking-form');
  if (packageBookingForm) {
    packageBookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      handlePackageBookingSubmit(formData);
    });
  }
  
  setTimeout(function() {
    // Domestic section: show only 3 packages, minimal info
    const domContainer = document.getElementById('domestic-packages');
    if (domContainer) {
      domContainer.innerHTML = '';
      domesticPackages.slice(0, 3).forEach(pkg => {
        const card = document.createElement('div');
        card.className = 'package-card';
        card.innerHTML = `
          <img src="${pkg.image || pkg.img}" alt="${pkg.name}">
          <div class="package-info">
            <h3>${pkg.name}</h3>
            <div class="package-desc">${pkg.description || pkg.desc}</div>
            <div class="package-price">&#8377;${(pkg.price_per_person || pkg.price).toLocaleString('en-IN')} per person</div>
            <button class="btn view-package-btn" data-package="${pkg.name}">View Details</button>
          </div>
        `;
        domContainer.appendChild(card);
      });
      document.getElementById('view-all-domestic-btn').onclick = () => window.location.href = 'packages.html#domestic';
    }
    // International section: show only 3 packages, minimal info
    const intlContainer = document.getElementById('international-packages');
    if (intlContainer) {
      intlContainer.innerHTML = '';
      internationalPackages.slice(0, 3).forEach(pkg => {
        const card = document.createElement('div');
        card.className = 'package-card';
        card.innerHTML = `
          <img src="${pkg.image || pkg.img}" alt="${pkg.name}">
          <div class="package-info">
            <h3>${pkg.name}</h3>
            <div class="package-desc">${pkg.description || pkg.desc}</div>
            <div class="package-price">&#8377;${(pkg.price_per_person || pkg.price).toLocaleString('en-IN')} per person</div>
            <button class="btn view-package-btn" data-package="${pkg.name}">View Details</button>
          </div>
        `;
        intlContainer.appendChild(card);
      });
      document.getElementById('view-all-international-btn').onclick = () => window.location.href = 'packages.html#international';
    }
    // Attach view details event for all visible cards
    document.querySelectorAll('.view-package-btn').forEach(btn => {
      btn.onclick = function(e) {
        e.stopPropagation();
        const pkgName = btn.getAttribute('data-package');
        // Open details.php with package name (can be replaced with modal logic)
        window.open(`details.php?type=package&name=${encodeURIComponent(pkgName)}`, '_blank', 'width=800,height=700');
      };
    });
  }, 0);
});

// City plans data (removed import to fix syntax error)
// const cityPlans = {};

// Planner section functionality
document.getElementById('planner-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const toCity = this.querySelector('[name="to_city"]').value.toLowerCase();
  const startDate = new Date(this.querySelector('[name="start_date"]').value);
  const endDate = new Date(this.querySelector('[name="end_date"]').value);
  
  // Calculate number of days
  const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
  
  if (days <= 0) {
    alert('End date must be after start date');
    return;
  }

  // Simple city plan data (inline to avoid import issues)
  const cityPlans = {
    mumbai: {
      sights: [{name: 'Gateway of India', cost: 0}, {name: 'Marine Drive', cost: 0}, {name: 'Juhu Beach', cost: 0}],
      hotel: 3000,
      food: 1500,
      transport: 500
    },
    delhi: {
      sights: [{name: 'Red Fort', cost: 500}, {name: 'Qutub Minar', cost: 300}, {name: 'India Gate', cost: 0}],
      hotel: 2500,
      food: 1200,
      transport: 400
    },
    goa: {
      sights: [{name: 'Calangute Beach', cost: 0}, {name: 'Fort Aguada', cost: 200}, {name: 'Basilica of Bom Jesus', cost: 0}],
      hotel: 4000,
      food: 1800,
      transport: 600
    }
  };

  const cityPlan = cityPlans[toCity];
  if (!cityPlan) {
    alert('Sorry, we don\'t have a plan for this city yet. Please try another destination.');
    return;
  }

  // Calculate total costs
  const totalSightsCost = cityPlan.sights.reduce((sum, sight) => sum + sight.cost, 0);
  const totalHotelCost = cityPlan.hotel * days;
  const totalFoodCost = cityPlan.food * days;
  const totalTransportCost = cityPlan.transport * days;
  const totalCost = totalSightsCost + totalHotelCost + totalFoodCost + totalTransportCost;

  // Generate plan HTML
  const suggestionsDiv = document.getElementById('suggestions');
  suggestionsDiv.innerHTML = `
    <div class="plan-summary">
      <h3>Your ${days}-Day Trip to ${toCity.charAt(0).toUpperCase() + toCity.slice(1)}</h3>
      <div class="cost-breakdown">
        <h4>Cost Breakdown:</h4>
        <ul>
          <li>Sightseeing: ₹${totalSightsCost.toLocaleString('en-IN')}</li>
          <li>Hotel (${days} days): ₹${totalHotelCost.toLocaleString('en-IN')}</li>
          <li>Food (${days} days): ₹${totalFoodCost.toLocaleString('en-IN')}</li>
          <li>Local Transport: ₹${totalTransportCost.toLocaleString('en-IN')}</li>
          <li class="total">Total Estimated Cost: ₹${totalCost.toLocaleString('en-IN')}</li>
        </ul>
      </div>
      <div class="sights-list">
        <h4>Recommended Sights:</h4>
        <ul>
          ${cityPlan.sights.map(sight => `
            <li>${sight.name} ${sight.cost > 0 ? `(₹${sight.cost})` : '(Free)'}</li>
          `).join('')}
        </ul>
      </div>
      <div class="daily-budget">
        <h4>Daily Budget:</h4>
        <p>Hotel: ₹${cityPlan.hotel.toLocaleString('en-IN')}</p>
        <p>Food: ₹${cityPlan.food.toLocaleString('en-IN')}</p>
        <p>Transport: ₹${cityPlan.transport.toLocaleString('en-IN')}</p>
      </div>
      <button class="btn" onclick="window.location.href='#booking'">Book Now</button>
    </div>
  `;
});

// --- SESSION MANAGEMENT ---
function checkSessionStatus() {
  fetch('php/session_status.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
      console.log('Session check response:', data);
      if (!data.logged_in) {
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('isAdminUser');
        window.updateAuthUI();
      } else {
        localStorage.setItem('loggedInUser', data.username);
        localStorage.setItem('isAdminUser', data.is_admin == 1 ? '1' : '0');
        window.updateAuthUI();
      }
    })
    .catch((error) => {
      console.error('Session check error:', error);
      // Don't clear localStorage on error, just update UI based on current state
      window.updateAuthUI();
    });
}

// Call on page load
if (window.location.pathname.endsWith('index.html') || window.location.pathname === '/' || window.location.pathname === '/travelplanner/') {
  // Check session status first, then update UI
  checkSessionStatus();
  
  // Pre-fill booking form if coming from destinations or packages
  const selectedDestination = localStorage.getItem('selectedDestination');
  const selectedPackage = localStorage.getItem('selectedPackage');
  
  if (selectedDestination || selectedPackage) {
    // Wait for DOM to be ready
    setTimeout(() => {
      const destinationField = document.getElementById('destination');
      if (destinationField) {
        if (selectedDestination) {
          destinationField.value = selectedDestination;
          localStorage.removeItem('selectedDestination');
        } else if (selectedPackage) {
          destinationField.value = selectedPackage;
          localStorage.removeItem('selectedPackage');
        }
      }
    }, 1000);
  }
} else {
  // For other pages, just update UI based on localStorage
  window.updateAuthUI();
}

// Also clear login form when showing the login section
window.addEventListener('hashchange', function() {
  if (window.location.hash === '#auth') {
    clearLoginForm();
  }
});

// Function to show destination details
function showDestinationDetails(city) {
  const cityData = {
    mumbai: {
      name: 'Mumbai',
      description: 'The City of Dreams - India\'s financial capital and entertainment hub',
      image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Gateway of India', 'Marine Drive', 'Juhu Beach', 'Bandra-Worli Sea Link', 'Colaba Causeway'],
      bestTime: 'October to March',
      startingPrice: '₹5,000'
    },
    delhi: {
      name: 'Delhi',
      description: 'Heart of India - A blend of ancient history and modern culture',
      image: 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Red Fort', 'Qutub Minar', 'India Gate', 'Humayun\'s Tomb', 'Chandni Chowk'],
      bestTime: 'October to March',
      startingPrice: '₹4,500'
    },
    goa: {
      name: 'Goa',
      description: 'Pearl of the Orient - Beach paradise with Portuguese heritage',
      image: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Calangute Beach', 'Baga Beach', 'Fort Aguada', 'Basilica of Bom Jesus', 'Dudhsagar Falls'],
      bestTime: 'November to March',
      startingPrice: '₹6,500'
    },
    jaipur: {
      name: 'Jaipur',
      description: 'The Pink City - Royal heritage and magnificent forts',
      image: 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Amber Fort', 'Hawa Mahal', 'City Palace', 'Jantar Mantar', 'Nahargarh Fort'],
      bestTime: 'October to March',
      startingPrice: '₹4,000'
    },
    kerala: {
      name: 'Kerala',
      description: 'God\'s Own Country - Backwaters, tea gardens, and natural beauty',
      image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Alleppey Backwaters', 'Munnar Tea Gardens', 'Fort Kochi', 'Varkala Beach', 'Periyar Wildlife'],
      bestTime: 'September to March',
      startingPrice: '₹5,500'
    },
    udaipur: {
      name: 'Udaipur',
      description: 'City of Lakes - Romantic lakeside palaces and cultural heritage',
      image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      highlights: ['Lake Palace', 'City Palace', 'Pichola Lake', 'Saheliyon Ki Bari', 'Sajjangarh Palace'],
      bestTime: 'September to March',
      startingPrice: '₹4,800'
    }
  };
  
  const data = cityData[city];
  if (!data) return;
  
  const modal = document.createElement('div');
  modal.className = 'destination-modal';
  modal.innerHTML = `
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div class="destination-detail">
        <div class="detail-image" style="background-image: url('${data.image}')"></div>
        <div class="detail-info">
          <h2>${data.name}</h2>
          <p class="location">📍 ${data.name}</p>
          <p class="description">${data.description}</p>
          <div class="features">
            <h4>Highlights:</h4>
            ${data.highlights.map(highlight => `<span class="feature-tag">${highlight}</span>`).join('')}
          </div>
          <div class="price">Starting from ${data.startingPrice}</div>
          <div class="detail-actions">
            <button class="btn btn-book" onclick="goToBooking('${data.name}')">Book This Destination</button>
            <button class="btn-close" onclick="closeDestinationModal()">Close</button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Close modal functionality
  modal.querySelector('.close-modal').onclick = closeDestinationModal;
  modal.onclick = function(e) {
    if (e.target === modal) closeDestinationModal();
  };
}

// Go to booking with destination pre-filled
function goToBooking(destinationName) {
  window.location.href = `#booking`;
  // Store destination for pre-filling
  localStorage.setItem('selectedDestination', destinationName);
  
  // Close modal if open
  closeDestinationModal();
}

// Close destination modal
function closeDestinationModal() {
  const modal = document.querySelector('.destination-modal');
  if (modal) {
    modal.remove();
  }
}

// Contact form submission
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('contact-result') || document.createElement('div');
            
            // Show loading message
            resultDiv.innerHTML = '<div style="text-align: center; padding: 20px;">Sending message...</div>';
            if (!document.getElementById('contact-result')) {
                this.appendChild(resultDiv);
            }
            
            console.log('Contact form data:', Object.fromEntries(formData)); // Debug log
            
            fetch('php/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Contact response status:', response.status); // Debug log
                return response.json();
            })
            .then(data => {
                console.log('Contact response data:', data); // Debug log
                
                if (data.status === 'success') {
                    resultDiv.innerHTML = `
                        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-top: 15px;">
                            ✅ ${data.msg}
                        </div>
                    `;
                    this.reset();
                } else {
                    resultDiv.innerHTML = `
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-top: 15px;">
                            ❌ ${data.msg}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Contact form error:', error); // Debug log
                resultDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-top: 15px;">
                        ❌ Failed to send message. Please try again. Error: ${error.message}
                    </div>
                `;
            });
        });
    }
});

// --- LOGIN FORM HANDLER FOR MAIN PAGE ---
function attachLoginHandler() {
  console.log('Attaching login handler...');
  const mainLoginForm = document.getElementById('login-form');
  const loginResult = document.getElementById('login-result');
  console.log('Login form found:', mainLoginForm);
  console.log('Login result found:', loginResult);
  
  if (!mainLoginForm) {
    console.log('No login form found!');
    return;
  }
  if (mainLoginForm._handlerAttached) {
    console.log('Handler already attached');
    return;
  }
  mainLoginForm._handlerAttached = true;
  console.log('Handler attached successfully');
  
  // Direct click handler for login button
  const loginButton = mainLoginForm.querySelector('button[type="submit"]');
  console.log('Login button found:', loginButton);
  if (loginButton) {
    loginButton.onclick = function(e) {
      console.log('Login button clicked!');
      e.preventDefault();
      e.stopPropagation();
      handleLogin();
    };
  }
  
  mainLoginForm.onsubmit = function(e) {
    console.log('Form submit event!');
    e.preventDefault();
    handleLogin();
  };
  
  function handleLogin() {
    console.log('handleLogin called!');
    loginResult.innerHTML = '<div class="loader">Logging in...</div>';
    const formData = new FormData(mainLoginForm);
    console.log('Form data:', Object.fromEntries(formData));
    fetch('php/login.php', {
      method: 'POST',
      body: formData
    })
    .then(res => {
      console.log('Response received:', res);
      return res.json();
    })
    .then(data => {
      console.log('Login response:', data);
      if (data.status === 'success') {
        localStorage.setItem('loggedInUser', data.username);
        localStorage.setItem('isAdminUser', data.is_admin == 1 ? '1' : '0');
        window.updateAuthUI();
        loginResult.innerHTML = `<div class='success'><span class='icon'>✅</span> Login successful, Welcome <b>${data.username}</b>!`;
        
        // Handle redirect
        if (data.redirect) {
          const currentPage = window.location.pathname.split('/').pop() || 'index.html';
          const redirectPage = data.redirect.split('/').pop();
          
          if (currentPage === redirectPage) {
            // User is already on the target page, just update UI
            console.log('User already on target page, updating UI only');
            // Hide login section
            const authSection = document.getElementById('auth');
            if (authSection) authSection.style.display = 'none';
            // Force UI update
            setTimeout(() => {
              window.updateAuthUI();
            }, 100);
          } else {
            // Redirect to different page
            setTimeout(() => {
              window.location = data.redirect;
            }, 900);
          }
        }
      } else {
        loginResult.innerHTML = `<div class='error'><span class='icon'>❌</span> ${data.msg || 'Login failed.'}</div>`;
      }
    })
    .catch((error) => {
      console.error('Login error:', error);
      loginResult.innerHTML = `<div class='error'><span class='icon'>❌</span> Login failed. Please try again.</div>`;
    });
  }
  
  // Enter key submits form
  mainLoginForm.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      console.log('Enter key pressed!');
      e.preventDefault();
      handleLogin();
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('DOMContentLoaded fired');
  attachLoginHandler();
});
attachLoginHandler();

// Add spinner keyframes if not present
if (!document.getElementById('spinner-style')) {
  const style = document.createElement('style');
  style.id = 'spinner-style';
  style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } } @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: none; } }`;
  document.head.appendChild(style);
}

function clearLoginForm() {
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    loginForm.reset();
    // Also clear any result messages
    const loginResult = document.getElementById('login-result');
    if (loginResult) loginResult.innerHTML = '';
  }
}

// Profile dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
  const profileLink = document.getElementById('profile-link');
  const profileDropdown = document.querySelector('.profile-dropdown');
  
  if (profileLink && profileDropdown) {
    profileLink.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const isVisible = profileDropdown.style.display === 'block';
      profileDropdown.style.display = isVisible ? 'none' : 'block';
      console.log('Profile dropdown toggled:', !isVisible);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!profileDropdown.contains(e.target) && e.target !== profileLink) {
        profileDropdown.style.display = 'none';
      }
    });
  }
  
  // Edit Profile functionality
  const editProfileLink = document.getElementById('edit-profile-link');
  if (editProfileLink) {
    editProfileLink.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Edit Profile functionality coming soon!');
      // TODO: Implement edit profile page/modal
    });
  }
  
  // My Bookings functionality
  const myBookingsLink = document.querySelector('a[href="php/mybookings.php"]');
  if (myBookingsLink) {
    myBookingsLink.addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = 'php/mybookings.php';
    });
  }
  
  // Logout functionality
  const logoutLink = document.getElementById('logout-link');
  if (logoutLink) {
    logoutLink.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Logout clicked');
      
      // Clear any existing login messages first
      const loginResult = document.getElementById('login-result');
      if (loginResult) loginResult.innerHTML = '';
      
      fetch('php/logout.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
          console.log('Logout successful:', data);
          localStorage.removeItem('loggedInUser');
          localStorage.removeItem('isAdminUser');
          window.updateAuthUI();
          // Clear any success messages again
          if (loginResult) loginResult.innerHTML = '';
          window.location = 'index.html#auth';
        })
        .catch(error => {
          console.error('Logout error:', error);
          // Force logout even if server fails
          localStorage.removeItem('loggedInUser');
          localStorage.removeItem('isAdminUser');
          window.updateAuthUI();
          // Clear any success messages
          if (loginResult) loginResult.innerHTML = '';
          window.location = 'index.html#auth';
        });
    });
  }
});

// Update auth UI on page load or after login/logout
window.updateAuthUI = function() {
  const username = localStorage.getItem('loggedInUser');
  const isAdmin = localStorage.getItem('isAdminUser') === '1';
  let welcomeDiv = document.getElementById('welcome-user');
  const loginLink = document.getElementById('login-link');
  const logoutLink = document.getElementById('logout-link');
  const profileMenu = document.querySelector('.profile-menu');
  const profileDropdown = document.querySelector('.profile-dropdown');
  const authSection = document.getElementById('auth');
  
  console.log('updateAuthUI called, username:', username);
  
  if (username && !isAdmin) {
    // Regular user is logged in
    if (!welcomeDiv) {
      welcomeDiv = document.createElement('div');
      welcomeDiv.id = 'welcome-user';
      document.querySelector('.navbar').appendChild(welcomeDiv);
    }
    welcomeDiv.innerHTML = `👋 Welcome, <b>${username}</b>!`;
    welcomeDiv.style.color = '#0077cc';
    welcomeDiv.style.fontWeight = '600';
    welcomeDiv.style.marginLeft = '1em';
    if (loginLink) loginLink.style.display = 'none';
    if (logoutLink) logoutLink.style.display = '';
    if (profileMenu) profileMenu.style.display = '';
    if (profileDropdown) profileDropdown.style.display = 'none';
    if (authSection) {
      authSection.style.display = 'none';
      console.log('Login section hidden');
    }
  } else {
    // Admin or logged out: hide all user UI
    if (welcomeDiv) welcomeDiv.innerHTML = '';
    if (loginLink) loginLink.style.display = '';
    if (logoutLink) logoutLink.style.display = 'none';
    if (profileMenu) profileMenu.style.display = 'none';
    if (profileDropdown) profileDropdown.style.display = 'none';
    if (authSection) {
      authSection.style.display = '';
      console.log('Login section shown');
    }
    // Clear any login result messages when logged out
    const loginResult = document.getElementById('login-result');
    if (loginResult) {
      loginResult.innerHTML = '';
      console.log('Login result cleared');
    }
  }
};

// --- AUTH (LOGIN/REGISTER) FORM HANDLING ---
document.addEventListener('DOMContentLoaded', function() {
  // Show register form
  const showRegister = document.getElementById('show-register');
  const showLogin = document.getElementById('show-login');
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  if (showRegister && registerForm && loginForm) {
    showRegister.addEventListener('click', function(e) {
      e.preventDefault();
      loginForm.style.display = 'none';
      registerForm.style.display = 'block';
    });
  }
  if (showLogin && registerForm && loginForm) {
    showLogin.addEventListener('click', function(e) {
      e.preventDefault();
      registerForm.style.display = 'none';
      loginForm.style.display = 'block';
    });
  }
  // Register form submit
  if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(registerForm);
      fetch('php/register.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        const resultDiv = document.getElementById('register-result');
        if (data.status === 'success') {
          resultDiv.innerHTML = `<div class='success'>${data.msg || 'Registration successful. Please login.'}</div>`;
          setTimeout(() => {
            registerForm.style.display = 'none';
            loginForm.style.display = 'block';
          }, 1500);
        } else {
          resultDiv.innerHTML = `<div class='error'>${data.msg || 'Registration failed.'}</div>`;
        }
      })
      .catch(() => {
        document.getElementById('register-result').innerHTML = `<div class='error'>Registration failed. Please try again.</div>`;
      });
    });
  }
});

// --- FORGOT PASSWORD FORM HANDLING ---
document.addEventListener('DOMContentLoaded', function() {
  const forgotForm = document.getElementById('forgot-form');
  const loginForm = document.getElementById('login-form');
  if (forgotForm) {
    forgotForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(forgotForm);
      fetch('php/forgot_password.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        const resultDiv = document.getElementById('forgot-result');
        if (data.status === 'success') {
          resultDiv.innerHTML = `<div class='success'>${data.msg}</div>`;
          setTimeout(() => {
            forgotForm.style.display = 'none';
            if (loginForm) loginForm.style.display = 'block';
          }, 2000);
        } else {
          resultDiv.innerHTML = `<div class='error'>${data.msg}</div>`;
        }
      })
      .catch(() => {
        document.getElementById('forgot-result').innerHTML = `<div class='error'>Request failed. Try again.</div>`;
      });
    });
  }
});
