// Auto-hide notification after seconds
const notification = document.getElementById('notification');
if (notification) {
    setTimeout(() => {
        notification.style.display = 'none';
    }, 5000);
}

// Auto-hide flash messages
const flashMessages = document.querySelectorAll('.flash-message');
flashMessages.forEach(msg => {
    setTimeout(() => {
        msg.style.display = 'none';
    }, 3000);
});

// Recharge button functionality
document.querySelectorAll('.recharge-btn').forEach(button => {
    button.addEventListener('click', function() {
        const planName = this.closest('.plan-card').querySelector('.plan-card-header').textContent;
        alert(`Redirecting to recharge page for: ${planName}`);
        // In production: window.location.href = 'recharge-page.php?plan=' + encodeURIComponent(planName);
    });
});

// Tab switching functionality
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab');
        
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        // Show active tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`${tab}-tab`).classList.add('active');
        
        // Update URL without reloading page
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    });
});

// Preserve tab state on page load
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab) {
        const tabButton = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
        const tabContent = document.getElementById(`${tab}-tab`);
        
        if (tabButton && tabContent) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            tabButton.classList.add('active');
            tabContent.classList.add('active');
        }
    }
});

// Handle back/forward navigation
window.addEventListener('popstate', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'home';
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === tab) {
            btn.classList.add('active');
        }
    });
    
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        if (content.id === `${tab}-tab`) {
            content.classList.add('active');
        }
    });
});

// Add recharge-btn class to all primary buttons
document.querySelectorAll('.btn-primary').forEach(button => {
    button.classList.add('recharge-btn');
});