<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhaleSpin - Doorprize Wheel System</title>
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
        
        .wheel-container {
            position: relative;
            width: 550px;
            height: 550px;
            margin: 0 auto;
        }
        
        .wheel {
            width: 100%;
            height: 100%;
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 0 0 15px #01579b, 0 0 50px rgba(0, 150, 255, 0.5);
            transition: transform 5s cubic-bezier(0.17, 0.67, 0.12, 0.99);
            transform: rotate(0deg);
            background: #0288d1;
        }
        
        .wheel-item {
            position: absolute;
            width: 50%;
            height: 50%;
            transform-origin: bottom right;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            clip-path: polygon(0 0, 100% 0, 100% 100%);
        }
        
        .wheel-item-text {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
            padding: 5px;
            transform-origin: left center;
            position: relative;
            left: 25px;
            top: -30px;
        }
        
        .wheel-center {
            position: absolute;
            width: 80px;
            height: 80px;
            background: #ffeb3b;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .wheel-center:hover {
            background: #ffd600;
            transform: translate(-50%, -50%) scale(1.1);
        }
        
        .wheel-center::before {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background: #ffc107;
            border-radius: 50%;
        }
        
        .wheel-pointer {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            z-index: 20;
        }
        
        .wheel-pointer::before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 30px solid #ff5722;
            filter: drop-shadow(0 0 5px rgba(255, 87, 34, 0.7));
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
        
        .whale-tail {
            position: absolute;
            width: 100px;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M10,50 Q50,10 90,50 Q50,90 10,50" fill="%230288d1" stroke="%23ffffff" stroke-width="2"/></svg>') no-repeat;
            background-size: contain;
            animation: swim 8s infinite linear;
            opacity: 0.7;
            z-index: -1;
        }
        
        @keyframes swim {
            0% {
                transform: translateX(-150px) rotateY(0deg);
            }
            50% {
                transform: translateX(calc(100vw + 150px)) rotateY(0deg);
            }
            51% {
                transform: translateX(calc(100vw + 150px)) rotateY(180deg);
            }
            100% {
                transform: translateX(-150px) rotateY(180deg);
            }
        }
        
        .winner-card {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s ease-out;
            z-index: 100;
        }
        
        .winner-card.show {
            transform: translate(-50%, -50%) scale(1);
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
        
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- Background elements -->
    <div id="bubbles"></div>
    <div class="whale-tail"></div>
    <div class="whale-tail" style="top: 30%; animation-delay: 2s;"></div>
    <div class="whale-tail" style="top: 70%; animation-delay: 4s;"></div>
    
    <div class="page-container">
        <header class="text-center mb-8">
            <h1 class="text-5xl font-bold mb-2">WhaleSpin</h1>
            <p class="text-xl opacity-80">Modern Doorprize Wheel System</p>
            <div class="w-24 h-1 bg-cyan-400 mx-auto mt-4 rounded-full"></div>
        </header>
        
        <!-- Wheel Section -->
        <div class="relative">
            <div class="wheel-container">
                <div class="wheel-pointer"></div>
                <div class="wheel" id="wheel">
                    <!-- Wheel segments will be added by JavaScript -->
                </div>
                <div class="wheel-center" id="spin-btn">
                    <i class="fas fa-play text-yellow-800 text-xl relative z-10"></i>
                </div>
            </div>
        </div>
        
        <!-- Winner Display -->
        <div id="winner-display" class="winner-card bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl p-8 shadow-2xl">
            <h3 class="text-2xl font-medium mb-2">Congratulations!</h3>
            <div class="text-4xl font-bold mb-4" id="winner-name">-</div>
            <div class="text-lg opacity-80 mb-4" id="winner-info">-</div>
            <div class="flex justify-center space-x-4">
                <button id="play-again" class="bg-white text-cyan-600 hover:bg-gray-100 px-6 py-2 rounded-full font-medium transition-all">
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
                '#EF5350', '#EC407A', '#AB47BC', '#7E57C2', 
                '#5C6BC0', '#42A5F5', '#29B6F6', '#26C6DA', 
                '#26A69A', '#66BB6A', '#9CCC65', '#D4E157', 
                '#FFCA28', '#FFA726', '#FF7043'
            ];
            
            // DOM elements
            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');
            const winnerName = document.getElementById('winner-name');
            const winnerInfo = document.getElementById('winner-info');
            const winnerDisplay = document.getElementById('winner-display');
            const playAgainBtn = document.getElementById('play-again');
            const confettiCanvas = document.getElementById('confetti-canvas');
            
            // Load participants
            loadParticipants();
            
            // Event listeners
            spinBtn.addEventListener('click', spinWheel);
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
                            updateWheel();
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
                
                updateWheel();
            }
            
            function updateWheel() {
                wheel.innerHTML = '';
                
                if (participants.length === 0) {
                    // Empty wheel - not showing any message
                    return;
                }
                
                // Update the global wheel styles
                document.body.style.background = 'linear-gradient(135deg, #1a237e 0%, #0d47a1 100%)';
                wheel.style.boxShadow = 'none';
                wheel.style.background = 'transparent';
                
                // Update center button to "Click to spin"
                const spinBtn = document.getElementById('spin-btn');
                spinBtn.innerHTML = '<div style="text-align:center;font-size:16px;font-weight:bold;color:#000;line-height:1.1;z-index:20;position:relative;"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGRkNDMDAiLz4KPC9zdmc+Cg==" width="40" height="40" style="margin-bottom:5px"><br>Click<br>to<br>spin</div>';
                spinBtn.style.width = '120px';
                spinBtn.style.height = '120px';
                spinBtn.style.background = '#fff';
                spinBtn.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
                
                // Create a new canvas-based wheel
                const canvas = document.createElement('canvas');
                canvas.width = 550;
                canvas.height = 550;
                canvas.style.position = 'absolute';
                canvas.style.top = '0';
                canvas.style.left = '0';
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                wheel.appendChild(canvas);
                
                const ctx = canvas.getContext('2d');
                const centerX = canvas.width / 2;
                const centerY = canvas.height / 2;
                const radius = Math.min(centerX, centerY) - 10;
                const segmentAngle = 2 * Math.PI / participants.length;
                
                // Draw wheel segments
                for (let i = 0; i < participants.length; i++) {
                    const startAngle = i * segmentAngle;
                    const endAngle = startAngle + segmentAngle;
                    
                    // Draw segment
                    ctx.beginPath();
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.closePath();
                    
                    // Fill with color
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.fill();
                    
                    // Add stroke
                    ctx.strokeStyle = '#000';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    
                    // Add name
                    const participant = participants[i];
                    const name = typeof participant === 'object' ? participant.name : participant;
                    
                    // Calculate text position and rotation
                    const textAngle = startAngle + segmentAngle / 2;
                    // Adjust text distance from center based on segment size
                    const textDistanceFromCenter = radius * 0.65;
                    
                    ctx.save();
                    
                    // Position text along the radius - move further out (from 40% to 70% of radius)
                    const textX = centerX + Math.cos(textAngle) * (radius * 0.7);
                    const textY = centerY + Math.sin(textAngle) * (radius * 0.7);
                    ctx.translate(textX, textY);
                    
                    // Rotate text to align with radius (pointing toward center)
                    // Add 90 degrees (PI/2) to make text perpendicular to radius
                    // Add or subtract 180 degrees (PI) depending on position to ensure text isn't upside down
                    let textRotation = textAngle;
                    
                    // Determine if we're in the right half or left half of the wheel
                    // to ensure text is always readable
                    if (textAngle > Math.PI / 2 && textAngle < Math.PI * 3 / 2) {
                        // Text in left half - rotate to point right
                        textRotation += Math.PI;
                    }
                    
                    ctx.rotate(textRotation);
                    
                    // Set text properties
                    ctx.fillStyle = '#000'; // Change to black text for better visibility on colorful backgrounds
                    
                    // Adjust font size based on number of participants and segment size
                    let fontSize = 16;
                    if (participants.length > 15) fontSize = 14;
                    if (participants.length > 25) fontSize = 12;
                    
                    ctx.font = `bold ${fontSize}px Poppins, sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    // Add text shadow for better contrast
                    ctx.shadowColor = 'rgba(255, 255, 255, 0.5)';
                    ctx.shadowBlur = 2;
                    ctx.shadowOffsetX = 0;
                    ctx.shadowOffsetY = 0;
                    
                    // Draw the text, truncate if too long
                    const maxTextWidth = radius * 0.5; // Increase text width limit
                    const truncatedName = truncateText(ctx, name, maxTextWidth);
                    ctx.fillText(truncatedName, 0, 0);
                    
                    ctx.restore();
                }
                
                // Add a circle in the center (behind the spin button)
                ctx.beginPath();
                ctx.arc(centerX, centerY, 60, 0, 2 * Math.PI);
                ctx.fillStyle = '#fff';
                ctx.fill();
                
                // Add the wheel pointer at the top
                const pointer = document.querySelector('.wheel-pointer');
                pointer.style.top = '-30px';
                pointer.innerHTML = '';
                
                const pointerTriangle = document.createElement('div');
                pointerTriangle.style.width = '0';
                pointerTriangle.style.height = '0';
                pointerTriangle.style.borderLeft = '20px solid transparent';
                pointerTriangle.style.borderRight = '20px solid transparent';
                pointerTriangle.style.borderTop = '40px solid #fff';
                pointerTriangle.style.filter = 'drop-shadow(0 0 5px rgba(255, 255, 255, 0.7))';
                pointer.appendChild(pointerTriangle);
            }
            
            function spinWheel() {
                if (isSpinning || participants.length === 0) return;
                
                isSpinning = true;
                const spinBtn = document.getElementById('spin-btn');
                spinBtn.innerHTML = '<div style="text-align:center;font-size:16px;font-weight:bold;color:#000;line-height:1.1;z-index:20;position:relative;"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGRkNDMDAiLz4KPC9zdmc+Cg==" width="40" height="40" style="margin-bottom:5px"><br>Spinning...</div>';
                
                // Call API to get a winner and then animate wheel
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
                        // Animate wheel to stop at winner segment
                        animateWheelToWinner(data.winner);
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat memilih pemenang');
                        resetSpinButton();
                    }
                })
                .catch(error => {
                    alert(error.message);
                    resetSpinButton();
                });
            }
            
            function animateWheelToWinner(apiWinner) {
                // Find the winner in our participants array
                const winnerIndex = participants.findIndex(p => 
                    (typeof p === 'object' && p.id == apiWinner.id) || p === apiWinner.name
                );
                
                if (winnerIndex === -1) {
                    // Winner not found in wheel, reload and try again
                    alert('Winner not found in wheel. Refreshing participants list.');
                    window.location.reload();
                    return;
                }
                
                // Calculate random spin (5-10 full rotations plus winner segment)
                const spinDegrees = 1800 + Math.floor(Math.random() * 1800);
                const segmentAngle = 360 / participants.length;
                const finalAngle = spinDegrees + (segmentAngle * winnerIndex);
                
                wheel.style.transform = `rotate(-${finalAngle}deg)`;
                
                setTimeout(() => {
                    // Show winner
                    showWinner(apiWinner);
                    
                    // Reset spin button
                    resetSpinButton();
                    
                    // Remove winner from participants list and update wheel
                    removeWinnerFromList(apiWinner.id);
                    
                }, 5000);
            }
            
            function resetSpinButton() {
                isSpinning = false;
                const spinBtn = document.getElementById('spin-btn');
                spinBtn.innerHTML = '<div style="text-align:center;font-size:16px;font-weight:bold;color:#000;line-height:1.1;z-index:20;position:relative;"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGRkNDMDAiLz4KPC9zdmc+Cg==" width="40" height="40" style="margin-bottom:5px"><br>Click<br>to<br>spin</div>';
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
                
                // Reset wheel position
                wheel.style.transition = 'none';
                wheel.style.transform = 'rotate(0deg)';
                setTimeout(() => {
                    wheel.style.transition = 'transform 5s cubic-bezier(0.17, 0.67, 0.12, 0.99)';
                }, 10);
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
                
                // Update wheel
                updateWheel();
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
            
            // Helper function to truncate text if too long
            function truncateText(ctx, text, maxWidth) {
                if (ctx.measureText(text).width <= maxWidth) {
                    return text;
                }
                
                let truncated = text;
                while (ctx.measureText(truncated + '...').width > maxWidth && truncated.length > 0) {
                    truncated = truncated.slice(0, -1);
                }
                
                return truncated + '...';
            }
            
            // Add keyboard shortcut (ctrl+enter)
            document.addEventListener('keydown', function(event) {
                if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                    spinWheel();
                }
            });
        });
    </script>
</body>
</html> 