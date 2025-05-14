<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhaleSpin - Doorprize Slot Machine</title>
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
        
        .slot-machine {
            width: 600px;
            background-color: #C0392B;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .slot-machine:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(90deg, #F1C40F, #d4af37, #F1C40F);
            z-index: 1;
        }
        
        .slot-machine:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(90deg, #F1C40F, #d4af37, #F1C40F);
            z-index: 1;
        }
        
        .slot-machine-top {
            width: 100%;
            background-color: #A93226;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .slot-display {
            display: flex;
            justify-content: space-around;
            background-color: #34495E;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5);
            position: relative;
        }
        
        .slot-display:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background-color: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }
        
        .slot-reel {
            width: 150px;
            height: 150px;
            overflow: hidden;
            background-color: white;
            border-radius: 10px;
            position: relative;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .slot-window {
            height: 150px;
            overflow: hidden;
            position: relative;
        }
        
        .slot-roll {
            position: absolute;
            width: 100%;
            animation-timing-function: cubic-bezier(0.11, 0, 0.5, 0);
            animation-fill-mode: forwards;
        }
        
        .slot-item {
            height: 150px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .lever-area {
            position: absolute;
            top: 50%;
            right: -70px;
            transform: translateY(-50%);
            height: 200px;
            width: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .lever-base {
            width: 40px;
            height: 40px;
            background-color: #7f8c8d;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .lever {
            width: 20px;
            height: 120px;
            background-color: #F1C40F;
            border-radius: 10px 10px 0 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: transform 0.3s;
            transform-origin: center bottom;
        }
        
        .lever:hover {
            transform: rotate(-5deg);
        }
        
        .lever-knob {
            width: 40px;
            height: 40px;
            background-color: #E74C3C;
            border-radius: 50%;
            margin-left: -10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .controls {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }
        
        .btn-spin {
            background-color: #E74C3C;
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 15px 40px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .btn-spin:hover {
            background-color: #C0392B;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .btn-spin:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .btn-spin .fa-play {
            margin-right: 10px;
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
        
        @keyframes bounce {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
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
        
        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: float 5s infinite ease-in-out;
            z-index: -1;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
        
        @keyframes spin {
            0% { transform: translateY(0); }
            100% { transform: translateY(var(--spin-distance)); }
        }
        
        @keyframes spinReverse {
            0% { transform: translateY(0); }
            100% { transform: translateY(calc(-1 * var(--spin-distance))); }
        }
        
        @keyframes wobble {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes leverPull {
            0% { transform: rotate(0); }
            50% { transform: rotate(20deg); }
            100% { transform: rotate(0); }
        }
        
        .wobbling {
            animation: wobble 0.3s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Background elements -->
    <div id="bubbles"></div>
    
    <div class="page-container">
        <header class="text-center mb-8">
            <h1 class="text-5xl font-bold mb-2">WhaleSpin</h1>
            <p class="text-xl opacity-80">Modern Doorprize Slot Machine</p>
            <div class="w-24 h-1 bg-cyan-400 mx-auto mt-4 mb-3 rounded-full"></div>
            <p class="text-sm text-cyan-300 italic">Match the same name across multiple reels to win!</p>
        </header>
        
        <!-- Slot Machine -->
        <div class="slot-machine">
            <div class="slot-machine-top">
                <h2 class="text-2xl font-bold text-white">DOORPRIZE SLOT MACHINE</h2>
            </div>
            
            <div class="slot-display">
                <div class="slot-reel" id="reel1">
                    <div class="slot-window">
                        <div class="slot-roll" id="roll1">
                            <!-- Slots will be filled by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="slot-reel" id="reel2">
                    <div class="slot-window">
                        <div class="slot-roll" id="roll2">
                            <!-- Slots will be filled by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="slot-reel" id="reel3">
                    <div class="slot-window">
                        <div class="slot-roll" id="roll3">
                            <!-- Slots will be filled by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="lever-area">
                    <div class="lever-base"></div>
                    <div class="lever" id="lever"></div>
                    <div class="lever-knob"></div>
                </div>
            </div>
            
            <div class="controls">
                <button class="btn-spin" id="spin-btn">
                    <i class="fas fa-play"></i> SPIN
                </button>
            </div>
        </div>
        
        <!-- Winner Display -->
        <div id="winner-display" class="winner-card">
            <div class="trophy-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h3 class="text-3xl font-medium mb-4">Congratulations!</h3>
            <div class="text-6xl font-bold mb-8" id="winner-name">-</div>
            <div class="text-2xl opacity-80 mb-10" id="winner-info">-</div>
            <div class="text-3xl font-bold text-yellow-300 mb-10">Match Identified!</div>
            <div class="flex justify-center space-x-4 mt-4">
                <button id="play-again" class="bg-white text-cyan-600 hover:bg-gray-100 px-10 py-4 rounded-full font-bold transition-all text-2xl shadow-lg hover:shadow-xl">
                    <i class="fas fa-sync-alt mr-2"></i> Play Again
                </button>
            </div>
        </div>
    </div>
    
    <!-- Confetti Canvas (hidden initially) -->
    <canvas id="confetti-canvas" class="confetti" style="display: none;"></canvas>
    
    <script src="https://cdn.jsdelivr.net/npm/confetti-js@0.0.18/dist/index.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create bubbles for background effect
            const bubblesContainer = document.getElementById('bubbles');
            for (let i = 0; i < 20; i++) {
                const bubble = document.createElement('div');
                bubble.classList.add('bubble');
                const size = Math.random() * 20 + 10;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                bubble.style.left = `${Math.random() * 100}%`;
                bubble.style.top = `${Math.random() * 100}vh`;
                bubble.style.animationDelay = `${Math.random() * 5}s`;
                bubble.style.opacity = Math.random() * 0.5 + 0.1;
                bubblesContainer.appendChild(bubble);
            }
            
            // Load participants from backend
            let participants = [];
            let isSpinning = false;
            const colors = [
                '#FF6B6B', '#FFDC69', '#87DEFF', '#6CCD91', '#FFA06B', 
                '#C592FF', '#FF92E9', '#92EFFF', '#ABFF92', '#FFBF92'
            ];
            
            // DOM elements
            const spinBtn = document.getElementById('spin-btn');
            const lever = document.getElementById('lever');
            const roll1 = document.getElementById('roll1');
            const roll2 = document.getElementById('roll2');
            const roll3 = document.getElementById('roll3');
            const winnerName = document.getElementById('winner-name');
            const winnerInfo = document.getElementById('winner-info');
            const winnerDisplay = document.getElementById('winner-display');
            const playAgainBtn = document.getElementById('play-again');
            const confettiCanvas = document.getElementById('confetti-canvas');
            
            // Load participants
            loadParticipants();
            
            // Event listeners
            spinBtn.addEventListener('click', startSpin);
            lever.addEventListener('click', startSpin);
            playAgainBtn.addEventListener('click', hideWinner);
            
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
                            initializeSlots();
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
                
                initializeSlots();
            }
            
            function initializeSlots() {
                if (participants.length === 0) {
                    // No participants - show message
                    createEmptySlots();
                    return;
                }
                
                // Create the slot items for each reel
                createSlotReel(roll1);
                createSlotReel(roll2);
                createSlotReel(roll3);
            }
            
            function createEmptySlots() {
                // Create empty slots with a message
                const emptyMessage = createSlotItem("No participants available");
                roll1.appendChild(emptyMessage.cloneNode(true));
                roll2.appendChild(emptyMessage.cloneNode(true));
                roll3.appendChild(emptyMessage.cloneNode(true));
                
                // Disable spin button
                spinBtn.disabled = true;
                spinBtn.classList.add('opacity-50');
                lever.style.cursor = 'not-allowed';
            }
            
            function createSlotReel(reelElement) {
                // Clear any existing content
                reelElement.innerHTML = '';
                
                // For each participant, create a slot item with random color
                participants.forEach((participant, index) => {
                    const participantName = typeof participant === 'object' ? participant.name : participant;
                    
                    // Create slot item with participant name
                    const slotItem = createSlotItem(participantName);
                    
                    // Add random background color
                    slotItem.style.backgroundColor = colors[index % colors.length];
                    
                    // Add to the reel
                    reelElement.appendChild(slotItem);
                });
                
                // Clone the first few items to create seamless loop
                const totalItems = reelElement.children.length;
                for(let i = 0; i < 3; i++) {
                    const clone = reelElement.children[i % totalItems].cloneNode(true);
                    reelElement.appendChild(clone);
                }
            }
            
            function createSlotItem(text) {
                const item = document.createElement('div');
                item.className = 'slot-item';
                
                // Create a wrapper for the text
                const textElem = document.createElement('div');
                textElem.style.maxWidth = '140px';
                textElem.style.overflow = 'hidden';
                textElem.style.textOverflow = 'ellipsis';
                textElem.style.whiteSpace = 'nowrap';
                textElem.textContent = text;
                
                item.appendChild(textElem);
                return item;
            }
            
            function startSpin() {
                if (isSpinning || participants.length === 0) return;
                
                isSpinning = true;
                
                // Disable spin button
                spinBtn.disabled = true;
                spinBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SPINNING...';
                
                // Animate lever pull
                lever.style.animation = 'leverPull 0.5s forwards';
                
                // Call API to get a winner
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
                    console.log('API response:', data);
                    
                    if (data.status === 'success') {
                        // Check if we want to use the API winner or pick a random winner
                        // 70% chance to use API winner, 30% chance to override with a random participant
                        const useApiWinner = Math.random() < 0.7;
                        
                        if (useApiWinner) {
                            // Animate slot machine to stop at winner from API
                            animateSlotsToWinner(data.winner, false);
                        } else {
                            // Select a random participant as winner
                            selectRandomWinner();
                        }
                    } else if (participants.length > 0) {
                        // Fallback: use a random participant as winner if API fails
                        selectRandomWinner();
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat memilih pemenang');
                        resetSpinButton();
                    }
                })
                .catch(error => {
                    if (participants.length > 0) {
                        // Fallback: use a random participant as winner if API errors
                        selectRandomWinner();
                    } else {
                        alert(error.message);
                        resetSpinButton();
                    }
                });
            }
            
            function selectRandomWinner() {
                if (participants.length === 0) {
                    alert('No participants available.');
                    resetSpinButton();
                    return;
                }
                
                const randomIndex = Math.floor(Math.random() * participants.length);
                const randomWinner = participants[randomIndex];
                const winner = {
                    id: typeof randomWinner === 'object' ? randomWinner.id : 0,
                    name: typeof randomWinner === 'object' ? randomWinner.name : randomWinner,
                    info: typeof randomWinner === 'object' ? (randomWinner.info || '') : ''
                };
                
                console.log('Using random winner:', winner);
                animateSlotsToWinner(winner, false);
            }
            
            function animateSlotsToWinner(apiWinner, useStuckReel = false) {
                // Validate winner object to prevent blank winners
                if (!apiWinner || !apiWinner.name || apiWinner.name.trim() === '') {
                    console.error('Invalid winner object:', apiWinner);
                    selectRandomWinner();
                    return;
                }
                
                // Find the winner in our participants array
                const winnerIndex = participants.findIndex(p => 
                    (typeof p === 'object' && p.id == apiWinner.id) || p === apiWinner.name
                );
                
                if (winnerIndex === -1) {
                    // Winner not found, use the first participant as fallback
                    if (participants.length > 0) {
                        const fallbackWinner = participants[0];
                        const winner = {
                            id: typeof fallbackWinner === 'object' ? fallbackWinner.id : 0,
                            name: typeof fallbackWinner === 'object' ? fallbackWinner.name : fallbackWinner,
                            info: typeof fallbackWinner === 'object' ? (fallbackWinner.info || '') : ''
                        };
                        animateSlotsToWinner(winner, false);
                    } else {
                        alert('No participants available. Please add participants and try again.');
                        resetSpinButton();
                    }
                    return;
                }
                
                // Calculate final positions for the reels
                const itemHeight = 150; // Height of each slot item
                const totalItems = participants.length;
                
                // Set different spin durations for each reel - make them slower
                const spinDuration1 = Math.floor(Math.random() * 2000) + 6000; // 6-8 seconds
                const spinDuration2 = Math.floor(Math.random() * 2000) + 6000; // 6-8 seconds
                const spinDuration3 = Math.floor(Math.random() * 2000) + 6000; // 6-8 seconds
                
                // Randomize which reel stops last
                const durations = [spinDuration1, spinDuration2, spinDuration3];
                const maxDuration = Math.max(...durations);
                
                // CRITICAL FIX: Completely strip all existing animations and styles
                roll1.style = '';
                roll2.style = '';
                roll3.style = '';
                
                // Force reflow before adding new animations
                void roll1.offsetWidth;
                void roll2.offsetWidth;
                void roll3.offsetWidth;
                
                // Add variation to spin direction - BUT ALL SPIN NORMAL DIRECTION
                const spinDirections = ['spin', 'spin', 'spin'];
                
                // Different timing functions for each reel
                const timingFunctions = [
                    `cubic-bezier(${Math.random()*0.2}, ${0.5+Math.random()*0.3}, ${0.2+Math.random()*0.3}, ${0.8+Math.random()*0.2})`,
                    `cubic-bezier(${Math.random()*0.2}, ${0.5+Math.random()*0.3}, ${0.2+Math.random()*0.3}, ${0.8+Math.random()*0.2})`,
                    `cubic-bezier(${Math.random()*0.2}, ${0.5+Math.random()*0.3}, ${0.2+Math.random()*0.3}, ${0.8+Math.random()*0.2})`
                ];
                
                // MAKE THE WINNER FIXED - Critical fix to ensure the display matches the winner
                let finalWinner = apiWinner;
                
                // Calculate position for reels to show the winner
                // Calculate exact position for each reel independently to avoid errors
                const winnerPosition = -1 * ((winnerIndex) * itemHeight);
                let reelPositions = [winnerPosition, winnerPosition, winnerPosition];
                
                // Set CSS custom properties for animation and start animations
                const reelElements = [roll1, roll2, roll3];
                
                for (let i = 0; i < 3; i++) {
                    reelElements[i].style.setProperty('--spin-distance', `${reelPositions[i]}px`);
                    reelElements[i].style.animation = `${spinDirections[i]} ${durations[i]/1000}s ${timingFunctions[i]}`;
                }
                
                // Variables to track stutter reels and positions
                let stutterReel = Math.floor(Math.random() * 3);
                let secondStutterReel = null;
                let stuckNames = [];
                let useStuckNameAsWinner = false; // Disable stuck name as winner to fix matching issue
                
                // Add some stutter/wobble effect to one random reel
                setTimeout(() => {
                    // Brief pause in the middle of animation
                    reelElements[stutterReel].style.animationPlayState = 'paused';
                    
                    // Record which name is visible during the stutter
                    const currentPosition = getComputedStyle(reelElements[stutterReel]).transform;
                    const translateY = parseMatrix(currentPosition)[5]; // Get translateY value
                    const visibleIndex = Math.round(translateY / itemHeight * -1) % participants.length;
                    if (visibleIndex >= 0 && visibleIndex < participants.length) {
                        const stuckName = typeof participants[visibleIndex] === 'object' ? 
                            participants[visibleIndex].name : participants[visibleIndex];
                        stuckNames.push({
                            reel: stutterReel,
                            name: stuckName,
                            index: visibleIndex
                        });
                    }
                    
                    // Add wobble animation to show it's stuck
                    reelElements[stutterReel].parentNode.classList.add('wobbling');
                    
                    setTimeout(() => {
                        reelElements[stutterReel].style.animationPlayState = 'running';
                        reelElements[stutterReel].parentNode.classList.remove('wobbling');
                    }, 600); // Pause for 600ms
                }, durations[stutterReel] * 0.4);
                
                // Randomly apply another stutter to a different reel
                if (Math.random() > 0.5) {
                    secondStutterReel = (stutterReel + 1 + Math.floor(Math.random() * 2)) % 3;
                    setTimeout(() => {
                        // Brief pause in the middle of animation
                        reelElements[secondStutterReel].style.animationPlayState = 'paused';
                        
                        // Record which name is visible during the stutter
                        const currentPosition = getComputedStyle(reelElements[secondStutterReel]).transform;
                        const translateY = parseMatrix(currentPosition)[5]; // Get translateY value
                        const visibleIndex = Math.round(translateY / itemHeight * -1) % participants.length;
                        if (visibleIndex >= 0 && visibleIndex < participants.length) {
                            const stuckName = typeof participants[visibleIndex] === 'object' ? 
                                participants[visibleIndex].name : participants[visibleIndex];
                            stuckNames.push({
                                reel: secondStutterReel,
                                name: stuckName,
                                index: visibleIndex
                            });
                        }
                        
                        // Add wobble animation
                        reelElements[secondStutterReel].parentNode.classList.add('wobbling');
                        
                        setTimeout(() => {
                            reelElements[secondStutterReel].style.animationPlayState = 'running';
                            reelElements[secondStutterReel].parentNode.classList.remove('wobbling');
                        }, 400); // Shorter pause
                    }, durations[secondStutterReel] * 0.7);
                }
                
                // Apply final positions after animations
                setTimeout(() => {
                    roll1.style.animation = 'none';
                    roll1.style.transform = `translateY(${reelPositions[0]}px)`;
                }, spinDuration1);
                
                setTimeout(() => {
                    roll2.style.animation = 'none';
                    roll2.style.transform = `translateY(${reelPositions[1]}px)`;
                }, spinDuration2);
                
                setTimeout(() => {
                    roll3.style.animation = 'none';
                    roll3.style.transform = `translateY(${reelPositions[2]}px)`;
                }, spinDuration3);
                
                // Add a final check to ensure correct positions after all animations
                setTimeout(() => {
                    // Re-verify final reel positions
                    for (let i = 0; i < 3; i++) {
                        reelElements[i].style.animation = 'none';
                        reelElements[i].style.transform = `translateY(${reelPositions[i]}px)`;
                    }
                    
                    // Wait a bit to let DOM update, then show winner
                    setTimeout(() => {
                        // Use the original winner - we've ensured it matches the reels
                        showWinner(finalWinner);
                        resetSpinButton();
                        removeWinnerFromList(finalWinner.id || finalWinner.name);
                    }, 200);
                }, maxDuration + 500);
                
                // Reset lever animation
                setTimeout(() => {
                    lever.style.animation = 'none';
                    void lever.offsetWidth; // Trigger reflow
                }, 500);
            }
            
            // Helper function to parse matrix transform values
            function parseMatrix(matrixStr) {
                const match = matrixStr.match(/matrix\((.+)\)/);
                if (!match) return [1, 0, 0, 1, 0, 0]; // Default identity matrix
                
                return match[1].split(',').map(Number);
            }
            
            function resetSpinButton() {
                isSpinning = false;
                spinBtn.disabled = false;
                spinBtn.innerHTML = '<i class="fas fa-play"></i> SPIN';
            }
            
            function showWinner(winner) {
                winnerName.textContent = winner.name;
                winnerInfo.textContent = winner.info || '';
                
                winnerDisplay.classList.add('show');
                
                // Show confetti
                showConfetti();
            }
            
            function hideWinner() {
                winnerDisplay.classList.remove('show');
                
                // Hide confetti
                if (window.confetti) {
                    window.confetti.clear();
                    confettiCanvas.style.display = 'none';
                }
            }
            
            function removeWinnerFromList(winnerId) {
                // Remove from array
                const index = participants.findIndex(p => 
                    (typeof p === 'object' && p.id == winnerId) || 
                    (typeof p === 'string' && p === winnerId)
                );
                
                if (index > -1) {
                    participants.splice(index, 1);
                }
                
                // Update the slots
                initializeSlots();
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
        });
    </script>
</body>
</html> 