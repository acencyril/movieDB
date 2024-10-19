/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

const routes = require('../public/js/fos_js_routes.json');
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

document.addEventListener('DOMContentLoaded', function () {
    Routing.setRoutingData(routes);
    const tokenUrl = Routing.generate('api_login_check');
    const genreUrl = Routing.generate('genres');
    async function generateTokenAndGetGenres() {
        try {
            const response = await fetch(tokenUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    username: 'mon_user',
                    password: 'mon_password'
                })
            });

            if (!response.ok) {
                throw new Error(`Erreur lors de la génération du token: ${response.status}`);
            }

            getGenres();

        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function getGenres() {
        try {
            const response = await fetch(genreUrl, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP ! statut : ${response.status}`);
            }

            const genresData = await response.json();
            const genreListElement = document.getElementById('genre-list');

            genreListElement.innerHTML = '';

            genresData.forEach(genre => {
                const genreItem = document.createElement('div');

                const radioButton = document.createElement('input');
                radioButton.type = 'radio';
                radioButton.name = 'genre';
                radioButton.id = `genre-${genre.id}`;
                radioButton.value = genre.id;

                const label = document.createElement('label');
                label.htmlFor = `genre-${genre.id}`;
                label.textContent = genre.name;

                genreItem.appendChild(radioButton);
                genreItem.appendChild(label);
                genreListElement.appendChild(genreItem);
            });

        } catch (error) {
            console.error('Erreur lors de la récupération des genres:', error);
        }
    }

    generateTokenAndGetGenres();
});
