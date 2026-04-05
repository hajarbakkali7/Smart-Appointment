// Données des services
const servicesData = {
    soins: {
        name: 'Soins & Esthétique',
        services: [
            { id: 1, name: 'Soins capillaires', duration: '45min', price: 35, description: 'Traitement hydratant et revitalisant pour cheveux' },
            { id: 2, name: 'Soins du visage', duration: '1h', price: 50, description: 'Nettoyage, gommage et masque hydratant' },
            { id: 3, name: 'Manucure', duration: '30min', price: 25, description: 'Soin complet des mains et vernis' },
            { id: 4, name: 'Pédicure', duration: '45min', price: 35, description: 'Soin complet des pieds et vernis' }
        ]
    },
    coiffure: {
        name: 'Coiffure',
        services: [
            { id: 5, name: 'Coupe de cheveux', duration: '30min', price: 30, description: 'Coupe personnalisée selon votre style' },
            { id: 6, name: 'Coloration', duration: '1h30', price: 65, description: 'Coloration complète ou mèches' },
            { id: 7, name: 'Coiffage & brushing', duration: '45min', price: 35, description: 'Mise en forme et brushing professionnel' },
            { id: 8, name: 'Permanente', duration: '2h', price: 80, description: 'Permanente ou défrisage' }
        ]
    },
    maquillage: {
        name: 'Maquillage & Beauté',
        services: [
            { id: 9, name: 'Maquillage classique', duration: '30min', price: 30, description: 'Maquillage naturel pour tous les jours' },
            { id: 10, name: 'Maquillage de soirée', duration: '45min', price: 45, description: 'Maquillage sophistiqué pour événements' },
            { id: 11, name: 'Maquillage mariée', duration: '1h30', price: 100, description: 'Maquillage complet avec essai' }
        ]
    }
};

// État de l'application
let currentCategory = null;
let selectedServices = [];
let customerData = {
    name: '',
    email: '',
    phone: '',
    comments: ''
};

// Navigation
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    document.getElementById(pageId).classList.add('active');
}

function goToHome() {
    showPage('homePage');
    currentCategory = null;
    updateCartDisplay();
}

function goBack(page) {
    if (page === 'home') {
        showPage('homePage');
    } else if (page === 'services') {
        showPage('servicesPage');
    } else if (page === 'form') {
        showPage('formPage');
    }
}

// Sélection de catégorie
function selectCategory(categoryId) {
    currentCategory = categoryId;
    const category = servicesData[categoryId];
    
    document.getElementById('categoryTitle').textContent = category.name;
    
    const servicesGrid = document.getElementById('servicesGrid');
    servicesGrid.innerHTML = '';
    
    category.services.forEach(service => {
        const serviceCard = document.createElement('div');
        serviceCard.className = 'service-card';
        serviceCard.id = `service-${service.id}`;
        
        const isSelected = selectedServices.find(s => s.id === service.id);
        if (isSelected) {
            serviceCard.classList.add('selected');
        }
        
        serviceCard.innerHTML = `
            <div class="service-check">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <h3 class="service-name">${service.name}</h3>
            <p class="service-description">${service.description}</p>
            <div class="service-info">
                <div class="service-duration">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    ${service.duration}
                </div>
                <div class="service-price">${service.price}€</div>
            </div>
        `;
        
        serviceCard.addEventListener('click', () => toggleService(service, categoryId));
        servicesGrid.appendChild(serviceCard);
    });
    
    showPage('servicesPage');
    updateSelectedBadge();
}

// Toggle service selection
function toggleService(service, categoryId) {
    const serviceCard = document.getElementById(`service-${service.id}`);
    const existingIndex = selectedServices.findIndex(s => s.id === service.id);
    
    if (existingIndex !== -1) {
        selectedServices.splice(existingIndex, 1);
        serviceCard.classList.remove('selected');
    } else {
        selectedServices.push({ ...service, categoryId });
        serviceCard.classList.add('selected');
    }
    
    updateSelectedBadge();
    updateCartDisplay();
}

// Mise à jour du badge de sélection
function updateSelectedBadge() {
    const selectedBadge = document.getElementById('selectedBadge');
    const selectedCount = document.getElementById('selectedCount');
    const continueButton = document.getElementById('continueButton');
    const continueCount = document.getElementById('continueCount');
    const continuePlural = document.getElementById('continuePlural');
    
    if (selectedServices.length > 0) {
        selectedBadge.style.display = 'flex';
        continueButton.style.display = 'flex';
        selectedCount.textContent = selectedServices.length;
        continueCount.textContent = selectedServices.length;
        continuePlural.textContent = selectedServices.length > 1 ? 's' : '';
    } else {
        selectedBadge.style.display = 'none';
        continueButton.style.display = 'none';
    }
}

// Mise à jour du panier dans le header
function updateCartDisplay() {
    const cartInfo = document.getElementById('cartInfo');
    const cartServicesCount = document.getElementById('cartServicesCount');
    const cartTotalPrice = document.getElementById('cartTotalPrice');
    
    if (selectedServices.length > 0) {
        cartInfo.style.display = 'block';
        cartServicesCount.textContent = `${selectedServices.length} service${selectedServices.length > 1 ? 's' : ''}`;
        cartTotalPrice.textContent = `${getTotalPrice()}€`;
    } else {
        cartInfo.style.display = 'none';
    }
}

// Calcul du prix total
function getTotalPrice() {
    return selectedServices.reduce((total, service) => total + service.price, 0);
}

// Calcul de la durée totale
function getTotalDuration() {
    let totalMinutes = 0;
    
    selectedServices.forEach(service => {
        const duration = service.duration;
        if (duration.includes('h')) {
            const parts = duration.split('h');
            totalMinutes += parseInt(parts[0]) * 60;
            if (parts[1]) {
                totalMinutes += parseInt(parts[1]);
            }
        } else {
            totalMinutes += parseInt(duration);
        }
    });
    
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    
    if (hours > 0 && minutes > 0) {
        return `${hours}h${minutes}min`;
    } else if (hours > 0) {
        return `${hours}h`;
    } else {
        return `${minutes}min`;
    }
}

// Aller au formulaire
function goToForm() {
    const selectedServicesList = document.getElementById('selectedServicesList');
    const formTotalPrice = document.getElementById('formTotalPrice');
    
    selectedServicesList.innerHTML = '';
    selectedServices.forEach(service => {
        const serviceItem = document.createElement('div');
        serviceItem.className = 'service-item';
        serviceItem.innerHTML = `
            <span class="service-item-name">${service.name}</span>
            <span class="service-item-price">${service.price}€</span>
        `;
        selectedServicesList.appendChild(serviceItem);
    });
    
    formTotalPrice.textContent = `${getTotalPrice()}€`;
    showPage('formPage');
}

// Validation du formulaire
function validateForm() {
    const name = document.getElementById('customerName').value.trim();
    const email = document.getElementById('customerEmail').value.trim();
    const phone = document.getElementById('customerPhone').value.trim();
    
    if (!name) {
        alert('Veuillez entrer votre nom');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Veuillez entrer un email valide');
        return false;
    }
    
    const phoneRegex = /^[0-9]{10}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        alert('Veuillez entrer un numéro de téléphone valide (10 chiffres)');
        return false;
    }
    
    return true;
}

// Aller au récapitulatif
function goToSummary() {
    if (!validateForm()) return;
    
    // Enregistrer les données du formulaire
    customerData = {
        name: document.getElementById('customerName').value.trim(),
        email: document.getElementById('customerEmail').value.trim(),
        phone: document.getElementById('customerPhone').value.trim(),
        comments: document.getElementById('customerComments').value.trim()
    };
    
    // Afficher les services dans le récapitulatif
    const summaryServicesList = document.getElementById('summaryServicesList');
    summaryServicesList.innerHTML = '';
    
    selectedServices.forEach(service => {
        const categoryName = servicesData[service.categoryId].name;
        const serviceItem = document.createElement('div');
        serviceItem.className = 'summary-service-item';
        serviceItem.innerHTML = `
            <div class="summary-service-info">
                <div class="service-category-badge">${categoryName}</div>
                <h4 class="summary-service-name">${service.name}</h4>
                <p class="summary-service-description">${service.description}</p>
                <p class="summary-service-duration">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    ${service.duration}
                </p>
            </div>
            <div class="summary-service-price">${service.price}€</div>
        `;
        summaryServicesList.appendChild(serviceItem);
    });
    
    // Afficher la durée totale
    document.getElementById('summaryDuration').textContent = `Durée totale estimée : ${getTotalDuration()}`;
    
    // Afficher le prix total
    document.getElementById('summaryTotalPrice').textContent = `${getTotalPrice()}€`;
    
    // Afficher les informations client
    const customerInfoDisplay = document.getElementById('customerInfoDisplay');
    customerInfoDisplay.innerHTML = `
        <div class="customer-info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <span>${customerData.name}</span>
        </div>
        <div class="customer-info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            <span>${customerData.email}</span>
        </div>
        <div class="customer-info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span>${customerData.phone}</span>
        </div>
        ${customerData.comments ? `
        <div class="customer-comments">
            <p class="customer-comments-label">Commentaires :</p>
            <p class="customer-comments-text">${customerData.comments}</p>
        </div>
        ` : ''}
    `;
    
    showPage('summaryPage');
}

// Confirmer la réservation
async function confirmBooking() {
    const bookingData = {
        customer: customerData,
        services: selectedServices,
        totalPrice: getTotalPrice(),
        totalDuration: getTotalDuration()
    };
    
    try {
        const response = await fetch('api/book.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Afficher le modal de succès
            const modal = document.getElementById('successModal');
            modal.classList.add('active');
            
            // Réinitialiser après 3 secondes
            setTimeout(() => {
                modal.classList.remove('active');
                resetBooking();
                goToHome();
            }, 3000);
        } else {
            // Gestion des erreurs avec messages personnalisés
            if (result.duplicate) {
                // Cas de doublon détecté
                alert('⚠️ Réservation déjà existante\n\n' + result.message);
            } else {
                // Autres erreurs
                alert('❌ Erreur\n\n' + result.message);
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('❌ Erreur de connexion\n\nUne erreur est survenue. Veuillez vérifier votre connexion internet et réessayer.');
    }
}

// Réinitialiser la réservation
function resetBooking() {
    selectedServices = [];
    currentCategory = null;
    customerData = {
        name: '',
        email: '',
        phone: '',
        comments: ''
    };
    
    document.getElementById('customerName').value = '';
    document.getElementById('customerEmail').value = '';
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerComments').value = '';
    
    updateCartDisplay();
}