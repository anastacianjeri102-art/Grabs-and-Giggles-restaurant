// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-refresh data every 30 seconds (optional)
    // setInterval(function() {
    //     location.reload();
    // }, 30000);
    
    // Add hover animation to stat cards
    const statCards = document.querySelectorAll('.stat-card, .stats-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s';
        });
    });
});