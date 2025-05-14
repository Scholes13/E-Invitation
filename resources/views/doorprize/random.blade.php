<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhaleSpin - Random Doorprize</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
        }
        
        .page-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
            padding: 20px;
            position: relative;
        }
        
        .random-box {
            width: 90%;
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 30px 30px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: 65vh;
            display: flex;
            flex-direction: column;
        }
        
        .names-container {
            flex: 1;
            overflow: hidden;
            position: relative;
            margin: 15px 0;
            perspective: 100px;
        }
        
        .shuffle-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            height: 100%;
            overflow-y: auto;
            padding: 5px;
            mask-image: linear-gradient(to bottom, transparent 0%, black 5%, black 95%, transparent 100%);
        }
        
        .name-card {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 15px;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.15);
            margin: 4px;
            font-weight: 500;
            font-size: 16px;
            min-width: 120px;
            text-align: center;
            transform-style: preserve-3d;
            transition: all 0.5s ease;
            opacity: 0.7;
            transform: scale(0.9);
        }
        
        .controls-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .hotkey-hint {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .status-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 5px;
            height: 50px;
        }
        
        .winner-card {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s ease-out, opacity 0.3s ease-out;
            z-index: 100;
            width: 100%;
            max-width: 90%;
            height: 85%;
            padding: 60px 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            border: 8px solid rgba(255, 204, 0, 0.8);
            background: linear-gradient(135deg, rgba(0, 183, 255, 0.85) 0%, rgba(0, 108, 255, 0.85) 100%);
            backdrop-filter: blur(5px);
        }
        
        .winner-card.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        
        .trophy-icon {
            color: #FFCC00;
            font-size: 48px;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }
        
        .name-card.active {
            background-color: rgba(255, 204, 0, 0.3);
            border-color: #FFCC00;
            transform: scale(1.05);
            opacity: 1;
            z-index: 5;
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.3);
        }
        
        .name-card.winner {
            background-color: rgba(255, 204, 0, 0.6);
            border-color: #FFCC00;
            transform: scale(1.2) translateZ(20px);
            opacity: 1;
            z-index: 10;
            box-shadow: 0 0 30px rgba(255, 204, 0, 0.5);
            position: relative;
            animation: pulse 1.5s infinite;
        }
        
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 90;
            pointer-events: none;
        }
        
        .blinking-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            background-color: white;
            border-radius: 50%;
            margin: 0 6px;
            animation: blink 1.4s infinite;
        }
        
        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes bounce {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }
        
        @keyframes blink {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(255, 204, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0); }
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.5;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: white;
            opacity: 0;
            will-change: transform;
        }
        
        .name-card.removing {
            transform: scale(0);
            opacity: 0;
            transition: all 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header class="text-center mb-4">
            <h1 class="text-5xl font-bold mb-2">WhaleSpin</h1>
            <p class="text-xl opacity-80">Modern Random Doorprize</p>
            <div class="w-24 h-1 bg-cyan-400 mx-auto mt-3 mb-2 rounded-full"></div>
            <p class="text-sm text-cyan-300 italic">Watch as we shuffle and select a random winner!</p>
        </header>
        
        <!-- Random Selection Box -->
        <div class="random-box">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">DOORPRIZE RANDOM DRAW</h2>
                
                <button id="draw-btn" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-all">
                    <i class="fas fa-random mr-2"></i> Draw Winner 
                    <span class="hotkey-hint">Space</span>
                </button>
            </div>
            
            <div class="names-container">
                <div id="shuffle-container" class="shuffle-container">
                    <!-- Names will be added here by JavaScript -->
                </div>
            </div>
            
            <div class="status-area">
                <div class="text-center" id="status-text">Ready to draw a winner</div>
                
                <div class="blinking-dots" id="loading-dots" style="display: none;">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
            
            <div class="text-center text-sm mt-2 text-cyan-200">
                <span id="participant-count">0</span> participants available
            </div>
        </div>
        
        <!-- Particles Container -->
        <div class="particles" id="particles"></div>
        
        <!-- Winner Display -->
        <div id="winner-display" class="winner-card">
            <div class="trophy-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h3 class="text-3xl font-medium mb-4">Congratulations!</h3>
            <div class="text-6xl font-bold mb-8" id="winner-name">-</div>
            <div class="text-2xl opacity-80 mb-10" id="winner-info">-</div>
            <div class="text-3xl font-bold text-yellow-300 mb-10">Winner Selected!</div>
            <div class="flex justify-center space-x-4 mt-4">
                <button id="play-again" class="bg-white text-cyan-600 hover:bg-gray-100 px-10 py-4 rounded-full font-bold transition-all text-2xl shadow-lg hover:shadow-xl">
                    <i class="fas fa-sync-alt mr-2"></i> Draw Again
                </button>
            </div>
        </div>
    </div>
    
    <!-- Confetti Canvas (hidden initially) -->
    <canvas id="confetti-canvas" class="confetti" style="display: none;"></canvas>
    
    <script src="https://cdn.jsdelivr.net/npm/confetti-js@0.0.18/dist/index.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const drawBtn = document.getElementById('draw-btn');
            const shuffleContainer = document.getElementById('shuffle-container');
            const statusText = document.getElementById('status-text');
            const loadingDots = document.getElementById('loading-dots');
            const winnerName = document.getElementById('winner-name');
            const winnerInfo = document.getElementById('winner-info');
            const winnerDisplay = document.getElementById('winner-display');
            const playAgainBtn = document.getElementById('play-again');
            const confettiCanvas = document.getElementById('confetti-canvas');
            const particlesContainer = document.getElementById('particles');
            const participantCount = document.getElementById('participant-count');
            
            // Variables
            let participants = [];
            let isDrawing = false;
            let activeCardIndex = -1;
            let shuffleInterval;
            let particleElements = [];
            let particleAnimationFrame = null;
            
            // Load participants
            loadParticipants();
            
            // Event listeners
            drawBtn.addEventListener('click', startDraw);
            playAgainBtn.addEventListener('click', function() {
                winnerDisplay.classList.remove('show');
                if (window.confetti) {
                    window.confetti.clear();
                    confettiCanvas.style.display = 'none';
                }
            });
            
            // Add keyboard shortcut (Space bar)
            document.addEventListener('keydown', function(event) {
                if (event.code === 'Space' || event.keyCode === 32) {
                    if (!isDrawing && !winnerDisplay.classList.contains('show')) {
                        event.preventDefault();
                        startDraw();
                    } else if (winnerDisplay.classList.contains('show')) {
                        event.preventDefault();
                        winnerDisplay.classList.remove('show');
                        if (window.confetti) {
                            window.confetti.clear();
                            confettiCanvas.style.display = 'none';
                        }
                    }
                }
            });
            
            // Functions
            function loadParticipants() {
                // Make AJAX request to get participants data
                fetch('{{ route("doorprize.winners") }}')
                    .then(response => response.json())
                    .then(data => {
                        // We'll use this route to check if the backend is working
                        // Then proceed to load all eligible participants
                        return fetch('/api/doorprize/participants');
                    })
                    .then(response => {
                        if (!response.ok) {
                            // If the API doesn't exist, use the participants from the page
                            loadParticipantsFromBackend();
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.participants) {
                            participants = data.participants;
                            initializeNameCards();
                        } else {
                            loadParticipantsFromBackend();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading participants:', error);
                        loadParticipantsFromBackend();
                    });
            }
            
            function loadParticipantsFromBackend() {
                // Get participants from the data embedded in the page
                participants = @json($invitations->map(function($invitation) {
                    return [
                        'id' => $invitation->id_invitation,
                        'name' => $invitation->name_guest,
                        'info' => $invitation->information_invitation
                    ];
                }));
                
                initializeNameCards();
            }
            
            function initializeNameCards() {
                shuffleContainer.innerHTML = '';
                
                if (participants.length === 0) {
                    statusText.textContent = 'No participants available';
                    drawBtn.disabled = true;
                    drawBtn.classList.add('opacity-50');
                    participantCount.textContent = '0';
                    return;
                }
                
                // Update the participant count
                participantCount.textContent = participants.length;
                
                // Adjust card size based on number of participants
                let cardSize = 'medium';
                if (participants.length > 30) {
                    cardSize = 'small';
                } else if (participants.length > 15) {
                    cardSize = 'medium';
                } else {
                    cardSize = 'large';
                }
                
                // Create name cards
                participants.forEach((participant, index) => {
                    const name = typeof participant === 'object' ? participant.name : participant;
                    
                    const nameCard = document.createElement('div');
                    nameCard.className = 'name-card';
                    nameCard.textContent = name;
                    nameCard.dataset.index = index;
                    
                    // Apply size adjustment
                    if (cardSize === 'small') {
                        nameCard.style.fontSize = '14px';
                        nameCard.style.padding = '6px 12px';
                        nameCard.style.minWidth = '100px';
                    } else if (cardSize === 'medium') {
                        nameCard.style.fontSize = '16px';
                        nameCard.style.padding = '8px 15px';
                        nameCard.style.minWidth = '120px';
                    } else {
                        nameCard.style.fontSize = '18px';
                        nameCard.style.padding = '10px 20px';
                        nameCard.style.minWidth = '150px';
                    }
                    
                    // Randomize initial position slightly
                    nameCard.style.transform = `scale(0.9) rotate(${Math.random() * 6 - 3}deg)`;
                    
                    shuffleContainer.appendChild(nameCard);
                });
                
                createParticles();
                animateCards();
            }
            
            function animateCards() {
                const cards = document.querySelectorAll('.name-card');
                cards.forEach((card, index) => {
                    // Random animation delays for initial load
                    const delay = Math.random() * 0.5;
                    card.style.transitionDelay = `${delay}s`;
                    
                    setTimeout(() => {
                        card.style.opacity = '0.7';
                        card.style.transform = 'scale(0.95)';
                    }, 100);
                });
            }
            
            function startDraw() {
                if (isDrawing || participants.length === 0) return;
                
                isDrawing = true;
                drawBtn.disabled = true;
                drawBtn.classList.add('opacity-50');
                statusText.textContent = 'Selecting a winner';
                loadingDots.style.display = 'flex';
                
                // Get all cards and reset any previous active/winner states
                const cards = document.querySelectorAll('.name-card');
                cards.forEach(card => {
                    card.classList.remove('active', 'winner');
                });
                
                // Start rapidly highlighting different cards
                let lastIndex = -1;
                shuffleInterval = setInterval(() => {
                    // Remove active class from previous card
                    if (lastIndex >= 0 && lastIndex < cards.length) {
                        cards[lastIndex].classList.remove('active');
                    }
                    
                    // Select a random card to highlight
                    let randomIndex;
                    do {
                        randomIndex = Math.floor(Math.random() * cards.length);
                    } while (randomIndex === lastIndex);
                    
                    cards[randomIndex].classList.add('active');
                    lastIndex = randomIndex;
                    
                    // Add some floating particles
                    createFloatingParticle();
                }, 150);
                
                // Fetch winner from API
                fetchWinner();
            }
            
            function fetchWinner() {
                fetch('{{ route('doorprize.draw') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Semua tamu sudah menjadi pemenang atau belum ada tamu yang check-in.');
                    }
                    return response.json();
                })
                .then(data => {
                    // Ensure we continue the animation for at least 3 seconds for dramatic effect
                    setTimeout(() => {
                        if (data.status === 'success') {
                            showWinner(data.winner);
                        } else {
                            selectRandomWinner();
                        }
                    }, 3000);
                })
                .catch(error => {
                    console.error('API error:', error);
                    // If API fails, continue for animation effect and then select random winner
                    setTimeout(() => {
                        selectRandomWinner();
                    }, 3000);
                });
            }
            
            function selectRandomWinner() {
                if (participants.length === 0) {
                    stopDrawing();
                    statusText.textContent = 'No participants available';
                    return;
                }
                
                const randomIndex = Math.floor(Math.random() * participants.length);
                const randomWinner = participants[randomIndex];
                const winner = {
                    id: typeof randomWinner === 'object' ? randomWinner.id : 0,
                    name: typeof randomWinner === 'object' ? randomWinner.name : randomWinner,
                    info: typeof randomWinner === 'object' ? (randomWinner.info || '') : ''
                };
                
                showWinner(winner);
            }
            
            function showWinner(apiWinner) {
                // Find the winner in our participants array
                const winnerIndex = participants.findIndex(p => 
                    (typeof p === 'object' && p.id == apiWinner.id) || p === apiWinner.name
                );
                
                // Stop the card shuffling
                stopDrawing();
                
                if (winnerIndex === -1) {
                    // Winner not found in our cards, select random
                    selectRandomWinner();
                    return;
                }
                
                // Show the winner card
                const cards = document.querySelectorAll('.name-card');
                cards.forEach(card => {
                    card.classList.remove('active');
                    if (parseInt(card.dataset.index) === winnerIndex) {
                        card.classList.add('winner');
                    }
                });
                
                // Update status
                statusText.textContent = 'Winner selected!';
                
                // Show large winner card with animation
                setTimeout(() => {
                    winnerName.textContent = apiWinner.name;
                    winnerInfo.textContent = apiWinner.info || '';
                    winnerDisplay.classList.add('show');
                    
                    // Show confetti
                    showConfetti();
                    
                    // Re-enable draw button
                    setTimeout(() => {
                        drawBtn.disabled = false;
                        drawBtn.classList.remove('opacity-50');
                    }, 500);
                    
                    // Actually remove the winner from the list and visually update
                    removeWinnerFromList(apiWinner.id || apiWinner.name);
                }, 1500);
            }
            
            function stopDrawing() {
                clearInterval(shuffleInterval);
                isDrawing = false;
                loadingDots.style.display = 'none';
                
                // Stop all animation frames to reduce jitter
                if (particleAnimationFrame) {
                    cancelAnimationFrame(particleAnimationFrame);
                }
            }
            
            function removeWinnerFromList(winnerId) {
                // Find the winner in the array
                const index = participants.findIndex(p => 
                    (typeof p === 'object' && p.id == winnerId) || 
                    (typeof p === 'string' && p === winnerId)
                );
                
                if (index > -1) {
                    // Find the corresponding card and animate its removal
                    const cards = document.querySelectorAll('.name-card');
                    cards.forEach(card => {
                        if (parseInt(card.dataset.index) === index) {
                            card.classList.add('removing');
                        }
                    });
                    
                    // Remove from array
                    const removedParticipant = participants.splice(index, 1)[0];
                    
                    // Update the participant count
                    participantCount.textContent = participants.length;
                    
                    // Delay the UI refresh to allow for animation
                    setTimeout(() => {
                        // Completely refresh all the cards to update indexes
                        initializeNameCards();
                    }, 600);
                    
                    console.log(`Removed winner: ${typeof removedParticipant === 'object' ? removedParticipant.name : removedParticipant}`);
                }
            }
            
            function showConfetti() {
                confettiCanvas.style.display = 'block';
                const confettiSettings = {
                    target: 'confetti-canvas',
                    max: 200,
                    size: 2,
                    animate: true,
                    props: ['circle', 'square', 'triangle', 'line'],
                    colors: [[165,104,246],[230,61,135],[0,199,228],[253,214,126]],
                    clock: 25,
                    rotate: true
                };
                window.confetti = new ConfettiGenerator(confettiSettings);
                window.confetti.render();
            }
            
            function createParticles() {
                // Clear existing particles
                while (particlesContainer.firstChild) {
                    particlesContainer.removeChild(particlesContainer.firstChild);
                }
                particleElements = [];
                
                // Reduced number of particles for better performance
                for (let i = 0; i < 20; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    
                    // Random size between 2-6px
                    const size = Math.random() * 4 + 2;
                    particle.style.width = `${size}px`;
                    particle.style.height = `${size}px`;
                    
                    // Random color with low opacity
                    const colors = ['rgba(255,255,255,0.5)', 'rgba(173,216,230,0.5)', 'rgba(255,223,186,0.5)'];
                    particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    
                    // Set initial position
                    const x = Math.random() * 100;
                    const y = Math.random() * 100;
                    particle.style.transform = `translate(${x}%, ${y}%)`;
                    
                    particlesContainer.appendChild(particle);
                    particleElements.push({
                        element: particle,
                        x: x,
                        y: y,
                        speedX: Math.random() * 0.1 - 0.05, // Reduced speed
                        speedY: Math.random() * 0.1 - 0.05, // Reduced speed
                        opacity: 0
                    });
                }
                
                // Cancel any existing animation frame
                if (particleAnimationFrame) {
                    cancelAnimationFrame(particleAnimationFrame);
                }
                
                animateParticles();
            }
            
            function animateParticles() {
                // Animate each particle
                particleElements.forEach(particle => {
                    // Gradually show the particle
                    if (particle.opacity < 0.5) {
                        particle.opacity += 0.01;
                    }
                    particle.element.style.opacity = particle.opacity;
                    
                    // Move the particle
                    particle.x += particle.speedX;
                    particle.y += particle.speedY;
                    
                    // Check boundaries and bounce/wrap
                    if (particle.x < 0) particle.x = 100;
                    if (particle.x > 100) particle.x = 0;
                    if (particle.y < 0) particle.y = 100;
                    if (particle.y > 100) particle.y = 0;
                    
                    // Update position - use transform instead of left/top for better performance
                    particle.element.style.transform = `translate(${particle.x}%, ${particle.y}%)`;
                });
                
                // Reduced animation frame rate to improve performance
                particleAnimationFrame = requestAnimationFrame(() => {
                    // Only run animation at ~30fps instead of 60fps for better performance
                    setTimeout(animateParticles, 33);
                });
            }
            
            function createFloatingParticle() {
                if (!isDrawing) return;
                
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Size between 4-10px
                const size = Math.random() * 6 + 4;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Bright colors
                const colors = ['rgba(255,215,0,0.7)', 'rgba(100,200,255,0.7)', 'rgba(255,100,100,0.7)'];
                particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                
                // Random position near the center
                const centerX = 50 + (Math.random() * 40 - 20);
                const centerY = 50 + (Math.random() * 40 - 20);
                particle.style.left = `${centerX}%`;
                particle.style.top = `${centerY}%`;
                particle.style.opacity = '0.7';
                
                particlesContainer.appendChild(particle);
                
                // Animate floating up and fading out
                let opacity = 0.7;
                let posY = centerY;
                
                const animateFloat = () => {
                    opacity -= 0.01;
                    posY -= 0.3;
                    
                    if (opacity <= 0) {
                        particlesContainer.removeChild(particle);
                        return;
                    }
                    
                    particle.style.opacity = opacity;
                    particle.style.top = `${posY}%`;
                    
                    requestAnimationFrame(animateFloat);
                };
                
                requestAnimationFrame(animateFloat);
            }
        });
    </script>
</body>
</html> 