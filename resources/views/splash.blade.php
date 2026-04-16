@extends('layouts.app')

@section('title', 'Splash Countdown - MUSARIS System')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 to-pink-600">
    <div class="max-w-md w-full mx-auto p-4">
        @if($success)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-8 8v-2a8 8 0 100-8-8-8H2a8 8 0 100-8 8v2zm-2 12a10 10 0 00-10 10v-2a10 10 0 0010 10v-2z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-lg font-medium">Success!</h3>
                </div>
                <p class="text-center text-green-700">{{ $message }}</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">🎯 Splash Countdown</h1>
                    <p class="text-gray-600">Complete the countdown to continue</p>
                </div>

                @if($error)
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-8 8v-2a8 8 0 100-8-8-8H2a8 8 0 100-8 8v2zm-2 12a10 10 0 00-10 10v-2a10 10 0 0010 10v-2z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-lg font-medium">Error</h3>
                        </div>
                        <p class="text-red-700">{{ $error }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('splash.submit') }}" class="space-y-6">
                    <div>
                        <label for="countdown" class="block text-sm font-medium text-gray-700 mb-2">Countdown Number (0-100)</label>
                        <div class="relative">
                            <input type="number" 
                                   id="countdown" 
                                   name="countdown" 
                                   min="0" 
                                   max="100" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="{{ old('countdown') ?? '' }}"
                                   placeholder="Enter number between 0 and 100">
                            <div id="popupSweeper" class="absolute inset-0 pointer-events-none">
                                <!-- Popup sweeper will appear here -->
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button type="submit" 
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Start Countdown
                        </button>
                        <button type="button" 
                                onclick="generateRandomCountdown()" 
                                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Random
                        </button>
                    </div>
                </form>

                @if($showPopup)
                    <div id="popupModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                            <div class="text-center mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">🎉 Congratulations!</h2>
                                <p class="text-gray-600">You found the lucky number!</p>
                            </div>
                            <div class="text-center space-y-4">
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg">
                                    <h3 class="text-lg font-medium">🎯 Lucky Number: {{ $luckyNumber }}</h3>
                                </div>
                                <div class="space-x-4">
                                    <button onclick="acceptPrize()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
                                        Accept Prize
                                    </button>
                                    <button onclick="closePopup()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">
                                        Try Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Popup Sweeper Game
let popupSweeper;
let currentCountdown = 0;
let gameActive = false;

document.addEventListener('DOMContentLoaded', function() {
    const countdownInput = document.getElementById('countdown');
    const popupSweeperElement = document.getElementById('popupSweeper');
    
    // Initialize popup sweeper
    popupSweeper = new PopupSweeper('popupSweeper');
    
    // Auto-focus on countdown input
    if (countdownInput) {
        countdownInput.focus();
    }
});

class PopupSweeper {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.gridSize = 10;
        this.mines = [];
        this.revealed = [];
        this.gameActive = false;
        this.luckyNumber = null;
    }
    
    start() {
        this.gameActive = true;
        this.generateGrid();
        this.render();
    }
    
    generateGrid() {
        // Clear container
        this.container.innerHTML = '';
        
        // Create grid
        for (let i = 0; i < this.gridSize * this.gridSize; i++) {
            const cell = document.createElement('div');
            cell.className = 'w-8 h-8 border border-gray-300 flex items-center justify-center text-xs font-bold cursor-pointer hover:bg-gray-100';
            cell.dataset.index = i;
            cell.textContent = '?';
            
            cell.addEventListener('click', () => this.revealCell(i));
            this.container.appendChild(cell);
        }
        
        // Place mines randomly
        this.placeMines();
    }
    
    placeMines() {
        const mineCount = Math.floor(this.gridSize * this.gridSize * 0.15); // 15% mines
        this.mines = [];
        
        while (this.mines.length < mineCount) {
            const randomIndex = Math.floor(Math.random() * (this.gridSize * this.gridSize));
            if (!this.mines.includes(randomIndex)) {
                this.mines.push(randomIndex);
            }
        }
    }
    
    revealCell(index) {
        if (!this.gameActive || this.revealed.includes(index)) return;
        
        this.revealed.push(index);
        
        const cell = this.container.children[index];
        
        if (this.mines.includes(index)) {
            // Hit a mine - game over
            cell.className = 'w-8 h-8 border border-red-300 flex items-center justify-center text-xs font-bold bg-red-500 text-white';
            cell.textContent = '💣';
            this.gameOver(false);
        } else {
            // Safe cell
            cell.className = 'w-8 h-8 border border-green-300 flex items-center justify-center text-xs font-bold bg-green-500 text-white';
            cell.textContent = '✓';
            
            // Check for win condition
            this.checkWinCondition();
        }
    }
    
    checkWinCondition() {
        const totalCells = this.gridSize * this.gridSize;
        const safeCells = totalCells - this.mines.length;
        const revealedSafe = this.revealed.filter(index => !this.mines.includes(index)).length;
        
        if (revealedSafe === safeCells && this.revealed.length === totalCells - this.mines.length) {
            this.luckyNumber = Math.floor(Math.random() * 100) + 1;
            this.showWinPopup();
        }
    }
    
    showWinPopup() {
        const form = document.querySelector('form');
        const luckyNumberInput = document.createElement('input');
        luckyNumberInput.type = 'hidden';
        luckyNumberInput.name = 'lucky_number';
        luckyNumberInput.value = this.luckyNumber;
        
        form.appendChild(luckyNumberInput);
        form.submit();
    }
    
    gameOver(won) {
        this.gameActive = false;
        const cells = this.container.children;
        
        for (let i = 0; i < cells.length; i++) {
            const cell = cells[i];
            
            if (this.mines.includes(i)) {
                cell.className = 'w-8 h-8 border border-red-300 flex items-center justify-center text-xs font-bold bg-red-500 text-white';
                cell.textContent = '💣';
            } else {
                cell.className = 'w-8 h-8 border border-green-300 flex items-center justify-center text-xs font-bold bg-green-500 text-white';
                cell.textContent = '✓';
            }
        }
    }
    
    render() {
        // Grid is already rendered in generateGrid()
    }
}

function generateRandomCountdown() {
    const randomNum = Math.floor(Math.random() * 101);
    document.getElementById('countdown').value = randomNum;
}

function acceptPrize() {
    // Close popup and continue
    closePopup();
}

function closePopup() {
    const modal = document.getElementById('popupModal');
    if (modal) {
        modal.remove();
    }
}

// Prevent form submission if game is active
document.querySelector('form').addEventListener('submit', function(e) {
    if (window.popupSweeper && window.popupSweeper.gameActive) {
        e.preventDefault();
        alert('Please finish the game first!');
        return false;
    }
});
</script>
@endsection
