const words = [
    'chat', 'chien', 'maison', 'arbre', 'voiture', 'rue', 'pomme', 'fleur', 'bureau', 'porte',
    'fenetre', 'table', 'chaise', 'lampe', 'stylo', 'papier', 'livre', 'téléphone', 'ordinateur', 'clé',
    'montagne', 'rivière', 'plage', 'ville', 'campagne', 'jardin', 'plante', 'poulet', 'boeuf', 'poisson',
    'pain', 'fromage', 'vin', 'café', 'thé', 'bouteille', 'verre', 'assiette', 'fourchette', 'cuillère',
    'lampe', 'rideau', 'canapé', 'lit', 'matelas', 'couverture', 'oreiller', 'tapis', 'carte', 'route',
    'avion', 'train', 'bateau', 'bus', 'métro', 'vélo', 'motocyclette', 'moto', 'nword', 'coeur', 'amour',
    'famille', 'ami', 'collègue', 'professeur', 'élève', 'enfant', 'adulte', 'sport', 'musique', 'film',
    'jeu', 'ordinateur', 'internet', 'site', 'application', 'message', 'réseau', 'ordinateur', 'programme',
    'code', 'clé', 'cadenas', 'journal', 'magazine', 'article', 'roman', 'poème', 'novella', 'scène',
    'théâtre', 'concert', 'exposition', 'musée', 'galerie', 'peinture', 'sculpture', 'photo', 'image',
    'dessin', 'illustration', 'cartoon', 'affiche', 'panneau', 'sign', 'enseign', 'bannière', 'écran'
];

function getRandomCombination() {
    const word1 = words[Math.floor(Math.random() * words.length)];
    const word2 = words[Math.floor(Math.random() * words.length)];
    const word3 = words[Math.floor(Math.random() * words.length)];
    return `${word1}-${word2}-${word3}`.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

function getRandomCodeCombination() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const digits = '0123456789';
    let code = '';

    // Ensure at least one character and one digit
    code += chars.charAt(Math.floor(Math.random() * chars.length));
    code += digits.charAt(Math.floor(Math.random() * digits.length));

    // Fill the rest of the code with random characters and digits
    for (let i = 2; i < 20; i++) {
        if (Math.random() < 0.5) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        } else {
            code += digits.charAt(Math.floor(Math.random() * digits.length));
        }
    }

    // Shuffle the code to ensure randomness
    code = code.split('').sort(() => 0.5 - Math.random()).join('');

    return code;
}

function updateUsername() {
    const usernameField = document.getElementById('register_form_username') || document.getElementById('profile_form_username');
    const usernameMessage = document.getElementById('usernameMessage');

    usernameField.value = getRandomCombination();
    usernameMessage.textContent = ''; // Clear the message when generating a new username
    usernameField.style.borderColor = ''; // Clear the border color
    checkUsername(usernameField.value); // Immediately check the new random username
}

function updateCode() {
    const codeField = document.getElementById('register_form_code') || document.getElementById('profile_form_code');
    const codeMessage = document.getElementById('codeMessage');

    codeField.value = getRandomCodeCombination();
    codeMessage.textContent = ''; // Clear the message when generating a new code
    codeField.style.borderColor = ''; // Clear the border color
    checkCode(codeField.value); // Immediately check the new random code
}

let debounceTimeout;
let originalUsername;
let originalCode;

document.addEventListener('DOMContentLoaded', () => {
    const usernameField = document.getElementById('register_form_username') || document.getElementById('profile_form_username');
    const codeField = document.getElementById('register_form_code') || document.getElementById('profile_form_code');
    const usernameMessage = document.getElementById('usernameMessage');
    const codeMessage = document.getElementById('codeMessage');

    // Store the original username and code if the form is for editing the profile
    if (usernameField && usernameField.id === 'profile_form_username') {
        originalUsername = usernameField.value;
    }
    if (codeField && codeField.id === 'profile_form_code') {
        originalCode = codeField.value;
    }

    function handleInput(event) {
        clearTimeout(debounceTimeout);
        const field = event.target;
        const value = field.value;

        if (field === usernameField) {
            // If the entered username is the same as the original, skip the check
            if (value === originalUsername) {
                usernameMessage.textContent = '';
                usernameField.style.borderColor = '';
                return;
            }

            if (value.length >= 1) {
                debounceTimeout = setTimeout(() => {
                    checkUsername(value);
                }, 500); // Delay to prevent excessive requests
            } else {
                usernameMessage.textContent = 'Le nom d\'utilisateur doit contenir au moins 1 caractère.';
                usernameMessage.style.color = 'red';
            }
        } else if (field === codeField) {
            // If the entered code is the same as the original, skip the check
            if (value === originalCode) {
                codeMessage.textContent = '';
                codeField.style.borderColor = '';
                return;
            }

            if (value.length === 20) {
                if (/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/.test(value)) {
                    debounceTimeout = setTimeout(() => {
                        checkCode(value);
                    }, 500); // Delay to prevent excessive requests
                } else {
                    codeMessage.textContent = 'Le code doit contenir au moins une lettre et un chiffre.';
                    codeMessage.style.color = 'red';
                }
            } else {
                codeMessage.textContent = 'Le code doit contenir 20 caractères.';
                codeMessage.style.color = 'red';
            }
        }
    }

    if (usernameField) {
        usernameField.addEventListener('input', handleInput);
    }
    if (codeField) {
        codeField.addEventListener('input', handleInput);
    }
});

function checkUsername(username) {
    fetch(`/check_username/${encodeURIComponent(username)}`)
        .then(response => response.json())
        .then(data => {
            const usernameField = document.getElementById('register_form_code') || document.getElementById('profile_form_code');
            const usernameMessage = document.getElementById('usernameMessage');

            if (data.available === false) {
                usernameMessage.textContent = 'Ce nom d\'utilisateur est déjà pris.';
                usernameMessage.style.color = 'red';
                usernameField.style.borderColor = 'red';
            } else {
                usernameMessage.textContent = 'Ce nom d\'utilisateur est disponible.';
                usernameMessage.style.color = 'green';
                usernameField.style.borderColor = 'green';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification du nom d\'utilisateur :', error);
        });
}

function checkCode(code) {
    fetch(`/check_code/${encodeURIComponent(code)}`)
        .then(response => response.json())
        .then(data => {
            const codeField = document.getElementById('register_form_code') || document.getElementById('profile_form_code');
            const codeMessage = document.getElementById('codeMessage');

            if (data.available === false) {
                codeMessage.textContent = 'Ce code est déjà pris.';
                codeMessage.style.color = 'red';
                codeField.style.borderColor = 'red';
            } else {
                codeMessage.textContent = 'Ce code est disponible.';
                codeMessage.style.color = 'green';
                codeField.style.borderColor = 'green';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification du code :', error);
        });
}

