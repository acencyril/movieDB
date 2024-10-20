import './styles/app.css';
const routes = require('../public/js/fos_js_routes.json');
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

document.addEventListener('DOMContentLoaded', function () {
    const tokenUrl = Routing.generate('api_login_check');
    const genreUrl = Routing.generate('genres');
    const getSoonReleasedMoviesUrl = Routing.generate('get_soon_released_movies');

    generateTokenAndGetGenres();

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

            fetchUpcomingMovies();
            await getGenres();

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

            addGenreClickEvents();

        } catch (error) {
            console.error('Erreur lors de la récupération des genres:', error);
        }
    }

    async function fetchUpcomingMovies() {
        try {
            const response = await fetch(getSoonReleasedMoviesUrl, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Erreur lors de la récupération des films à venir: ${response.status}`);
            }

            const movies = await response.json();
            displayMovies(movies);
            addButtonClickEvents(movies);

        } catch (error) {
            console.error('Erreur lors de la récupération des films à venir:', error);
        }
    }

    function addGenreClickEvents() {
        const genreRadios = document.querySelectorAll('input[name="genre"]');

        genreRadios.forEach(radio => {
            radio.addEventListener('click', () => {
                const genreId = radio.value;
                const getMoviesByGenreUrl = Routing.generate('get_movies_by_genre', { genreId });

                fetch(getMoviesByGenreUrl)
                    .then(response => response.json())
                    .then(movies => {
                        displayMovies(movies);
                        addButtonClickEvents(movies);
                    })
                    .catch(error => console.error('Erreur lors de la récupération des films:', error));
            });
        });
    }
});


function displayMovies(movies) {
    const videoContainer = document.getElementById('video-container');
    const moviesContainer = document.getElementById('moviesContainer');
    moviesContainer.innerHTML = '';
    videoContainer.innerHTML = '';

    const bestMovie = movies[0];

    const bestMovieCard = `
        <div class="best-movie">
            <div class="best-movie-video">
                <img src="${bestMovie.backdropUrl}" alt="${bestMovie.title}" class="best-movie-image">
                <div class="video-overlay">
                    <div id="playButton" class="play-button">▶</div>
                </div>
            </div>
            <iframe id="videoIframe" width="640" height="360" class="best-movie-image"
                    src="${bestMovie.trailerUrl}" 
                    frameborder="0" 
                     allow="accelerometer; autoplay; clipboard-write; 
                     encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen style="display: none;"></iframe>
            <div class="best-movie-info">
                <img src="${bestMovie.imageUrl}" alt="${bestMovie.title}" class="best-movie-thumbnail">
                <div class="best-movie-details">
                    <h2 class="best-movie-title">${bestMovie.title}</h2>
                    <p class="best-movie-subtitle">${bestMovie.trailerName}</p>
                </div>
            </div>
        </div>
    `;

    videoContainer.innerHTML = bestMovieCard;

    movies.slice(1).forEach(movie => {
        const movieCard = `
            <div class="movie-item">
                <img src="${movie.imageUrl}" alt="${movie.title}" class="movie-image">
                <div class="movie-info">
                    <h3>${movie.title} (${new Date(movie.year).getFullYear()})</h3>
                    <p><strong>Note :</strong> ${movie.voteAverage}/10 (${movie.voteCount} votes)</p>
                    <p>${movie.overview}</p>
                    <button class="details-button">Lire le détails</button>
                </div>
            </div>
        `;
        moviesContainer.innerHTML += movieCard;
    });

    const videoIframe = document.getElementById('videoIframe');
    const playButtonElement = document.getElementById('playButton');

    playButtonElement.addEventListener('click', function() {
        const videoCover = document.querySelector('.best-movie-video');
        videoCover.querySelector('img').style.display = 'none';
        playButtonElement.style.display = 'none';
        videoIframe.style.display = 'block';
    });

    addButtonClickEvents(movies);
}

function addButtonClickEvents(movies) {
    const detailsButtons = document.querySelectorAll('.details-button');

    detailsButtons.forEach((button, index) => {
        const movieIndex = index + 1;

        button.addEventListener('click', function (event) {
            event.stopPropagation();
            openModal(movies[movieIndex]);
        });
    });
}

function openModal(movie) {
    const modal = document.getElementById('movieModal');
    const modalMovie = document.getElementById('modalMovie');

    modalMovie.innerHTML = `
        <div class="modal-content">
            <img src="${movie.backdropUrl}" alt="${movie.title}" class="modal-image" style="width: 100%;">
            <div class="video-overlay">
                <div id="modalPlayButton" class="play-button">▶</div>
            </div>
            <iframe id="modalVideoIframe" width="100%" height="360"
             frameborder="0" 
             allow="accelerometer; autoplay; clipboard-write; 
             encrypted-media; gyroscope; picture-in-picture; web-share"
             allowfullscreen style="display: none;"></iframe>
        </div>
    `;

    document.getElementById('modalMovieTitle').textContent = movie.title + ' ' + movie.trailerName;
    document.getElementById('modalMovieOverview').textContent = 'Film : ' + movie.title;
    document.getElementById('modalMovieRating').textContent = movie.voteAverage;
    document.getElementById('modalMovieRatingSuite').textContent = ' pour ' + movie.voteCount + ' utilisateurs';

    modal.style.display = "block";

    document.getElementById('modalPlayButton').addEventListener('click', function() {
        const modalIframe = document.getElementById('modalVideoIframe');
        modalIframe.src = movie.trailerUrl;
        modalIframe.style.display = 'block';
        document.querySelector('.modal-image').style.display = 'none';
        this.style.display = 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('movieModal');
    const closeModalButton = document.getElementsByClassName('close')[0];

    closeModalButton.onclick = function () {
        modal.style.display = "none";
        document.getElementById('modalMovie').innerHTML = '';
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
            document.getElementById('modalMovie').innerHTML = '';
        }
    }
});

const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const searchButton = document.getElementById('searchButton');

searchInput.addEventListener('input', function() {
    const query = searchInput.value.trim();

    if (query.length > 2) {
        searchMovies(query);
    } else {
        searchResults.innerHTML = '';
    }
});

let searchResultsArray = [];

async function searchMovies(query) {
    const searchUrl = Routing.generate('search_movies', { query: query });

    try {
        const response = await fetch(searchUrl, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur lors de la recherche de films: ${response.status}`);
        }

        const results = await response.json();
        searchResultsArray = results;
        displaySearchResults(results);

    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
    }
}

function displaySearchResults(results) {
    searchResults.innerHTML = '';

    results.forEach(movie => {
        const resultItem = document.createElement('div');
        resultItem.classList.add('search-result-item');
        resultItem.textContent = movie.title;

        resultItem.addEventListener('click', () => {
            displayMovieDetails(movie);
            searchResults.style.display = "none";
        });

        searchResults.appendChild(resultItem);
    });

    if (results.length > 0) {
        searchResults.style.display = "block";
    } else {
        searchResults.style.display = "none";
    }
}

function displayMovieDetails(movie) {
    openModal(movie);
}

searchButton.addEventListener('click', function() {
    if (searchResultsArray.length > 0) {
        displayMovies(searchResultsArray);
    } else {
        console.log('Aucun film trouvé dans les résultats de recherche.');
    }
});

searchInput.addEventListener('focus', function() {
    const query = searchInput.value.trim();
    if (query.length > 2) {
        searchMovies(query);
    }
});

document.addEventListener('click', function(event) {
    const isClickInside = searchInput.contains(event.target) || searchResults.contains(event.target);

    if (!isClickInside) {
        searchResults.style.display = "none";
    }
});
