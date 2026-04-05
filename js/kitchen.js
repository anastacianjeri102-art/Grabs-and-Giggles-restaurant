// Kitchen Display JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Store previous order count to detect new orders
    let previousOrderCount = getCurrentOrderCount();
    
    // Auto-refresh page every 10 seconds to check for new orders
    let refreshInterval = setInterval(function() {
        checkForNewOrders();
    }, 10000);
    
    // Function to get current order count from DOM
    function getCurrentOrderCount() {
        const pendingCountSpan = document.querySelector('.pending-count');
        if(pendingCountSpan) {
            return parseInt(pendingCountSpan.textContent) || 0;
        }
        return 0;
    }
    
    // Function to check for new orders
    function checkForNewOrders() {
        const currentCount = getCurrentOrderCount();
        
        if(currentCount > previousOrderCount) {
            // New orders detected!
            const newOrderCount = currentCount - previousOrderCount;
            showNotification(`${newOrderCount} new order(s) received!`, 'new-order');
            playNewOrderSound();
            
            // Highlight new order cards
            highlightNewOrderCards();
        }
        
        previousOrderCount = currentCount;
        
        // Update refresh countdown display
        updateRefreshCountdown();
    }
    
    // Function to show toast notification
    function showNotification(message, type) {
        // Remove existing notifications
        const existingToast = document.querySelector('.toast-notification');
        if(existingToast) existingToast.remove();
        
        // Create new notification
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'new-order' ? 'bell' : 'check-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    // Function to play new order sound
    function playNewOrderSound() {
        // Create a beep using Web Audio API (no external file needed)
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;
            
            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
                audioContext.close();
            }, 300);
        } catch(e) {
            // Fallback: console log
            console.log('🔔 New order received!');
        }
    }
    
    // Function to highlight new order cards
    function highlightNewOrderCards() {
        const kitchenCards = document.querySelectorAll('.kitchen-card');
        
        kitchenCards.forEach(card => {
            card.classList.add('new-order');
            setTimeout(() => {
                card.classList.remove('new-order');
            }, 1000);
        });
    }
    
    // Function to update refresh countdown display
    let seconds = 10;
    const refreshSpan = document.querySelector('.auto-refresh span');
    
    function updateRefreshCountdown() {
        if(refreshSpan) {
            seconds = 10;
            const countdown = setInterval(function() {
                seconds--;
                refreshSpan.innerHTML = `Refreshing in ${seconds}s`;
                if(seconds <= 0) {
                    clearInterval(countdown);
                }
            }, 1000);
        }
    }
    
    // Initial countdown
    updateRefreshCountdown();
    
    // Confirm order completion
    const completeButtons = document.querySelectorAll('.complete-order-btn');
    
    completeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const form = this.closest('form');
            const tableNumber = form.querySelector('input[name="table_number"]').value;
            
            if(!confirm(`Complete all orders for Table ${tableNumber}?`)) {
                e.preventDefault();
            } else {
                showNotification(`Table ${tableNumber} orders completed!`, 'success');
            }
        });
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 3000);
    });
    
    // Update page title with pending count
    const pendingCountSpan = document.querySelector('.pending-count');
    if(pendingCountSpan && parseInt(pendingCountSpan.textContent) > 0) {
        document.title = `(${pendingCountSpan.textContent}) Kitchen Display - Grabs & Giggles`;
    } else {
        document.title = `Kitchen Display - Grabs & Giggles`;
    }
    
    // Add refresh button functionality
    const refreshIcon = document.querySelector('.auto-refresh i');
    if(refreshIcon) {
        refreshIcon.style.cursor = 'pointer';
        refreshIcon.addEventListener('click', function() {
            location.reload();
        });
    }
    
    // Visual feedback for new orders on page load
    if(document.querySelector('.kitchen-card')) {
        setTimeout(() => {
            if(parseInt(pendingCountSpan?.textContent || 0) > 0) {
                showNotification(`${pendingCountSpan.textContent} order(s) waiting in queue`, 'info');
            }
        }, 1000);
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Press 'R' to refresh
        if(e.key === 'r' || e.key === 'R') {
            e.preventDefault();
            location.reload();
        }
    });
});

// Function to manually refresh kitchen display
function refreshKitchenDisplay() {
    location.reload();
}