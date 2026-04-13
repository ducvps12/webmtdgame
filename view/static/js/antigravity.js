class AntigravityParticles {
    constructor() {
        // DISABLED: Particle effects removed for cleaner UI
        this.canvas = document.getElementById('antigravity-particles');
        if (this.canvas) {
            this.canvas.style.display = 'none'; // Hide canvas on all pages
        }
        return;
        
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.colors = ['#4285F4', '#EA4335', '#FBBC05', '#34A853', '#9c27b0', '#00bcd4']; // Google + Extra colors
        
        this.resize();
        window.addEventListener('resize', () => this.resize());
        
        this.initParticles();
        this.animate();
    }

    resize() {
        this.width = window.innerWidth;
        this.height = window.innerHeight;
        this.canvas.width = this.width;
        this.canvas.height = this.height;
        this.centerX = this.width / 2;
        this.centerY = this.height / 2;
    }

    initParticles() {
        // Adjust particle count depending on screen size
        const count = window.innerWidth < 768 ? 100 : 250;
        this.particles = [];
        for (let i = 0; i < count; i++) {
            this.particles.push(this.createParticle());
        }
    }

    createParticle() {
        // Spread particles around the center
        const angle = Math.random() * Math.PI * 2;
        const distance = Math.random() * (Math.max(this.width, this.height) / 1.5);
        
        return {
            x: this.centerX + Math.cos(angle) * distance,
            y: this.centerY + Math.sin(angle) * distance,
            size: Math.random() * 2 + 1, // small sizes
            color: this.colors[Math.floor(Math.random() * this.colors.length)],
            angle: angle,
            speed: Math.random() * 0.5 + 0.1, // very slow drift
            opacity: Math.random() * 0.8 + 0.2 // subtle
        };
    }

    update() {
        for (let i = 0; i < this.particles.length; i++) {
            let p = this.particles[i];
            
            // Move outward
            p.x += Math.cos(p.angle) * p.speed;
            p.y += Math.sin(p.angle) * p.speed;
            
            // Faintly rotate
            p.angle += 0.001; 
            
            // Reset if out of bounds
            if (p.x < 0 || p.x > this.width || p.y < 0 || p.y > this.height) {
                Object.assign(p, {
                    x: this.centerX + (Math.random() - 0.5) * 100, // spawn near center
                    y: this.centerY + (Math.random() - 0.5) * 100,
                    angle: Math.random() * Math.PI * 2
                });
            }
        }
    }

    draw() {
        this.ctx.clearRect(0, 0, this.width, this.height);
        
        for (let p of this.particles) {
            this.ctx.globalAlpha = p.opacity;
            this.ctx.fillStyle = p.color;
            this.ctx.beginPath();
            
            // Draw a tiny pill or circle (dots in Antigravity)
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fill();
        }
        
        this.ctx.globalAlpha = 1; // reset
    }

    animate() {
        this.update();
        this.draw();
        requestAnimationFrame(() => this.animate());
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AntigravityParticles();

    // --- Mobile Sidebar Logic ---
    const nav = document.querySelector('.apple-nav');
    const navRight = nav ? nav.querySelector('.nav-right') : null;
    const navLinks = nav ? nav.querySelector('.nav-links') : null;
    
    if (nav && navRight && navLinks) {
        // 1. Create hamburger toggle button (visible only on mobile via CSS)
        const toggleBtn = document.createElement('div');
        toggleBtn.className = 'mobile-menu-toggle';
        toggleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>';
        navRight.appendChild(toggleBtn);

        // 2. Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'mobile-sidebar-overlay';
        document.body.appendChild(overlay);

        // 3. Create a SEPARATE sidebar element (clone links, don't move them)
        const sidebar = document.createElement('div');
        sidebar.id = 'mobile-sidebar';
        sidebar.className = 'mobile-sidebar';

        // Close button
        const closeBtn = document.createElement('div');
        closeBtn.className = 'sidebar-close-btn';
        closeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
        sidebar.appendChild(closeBtn);

        // Clone each link into the sidebar
        const linksList = document.createElement('ul');
        linksList.className = 'sidebar-links';
        navLinks.querySelectorAll('li').forEach(li => {
            linksList.appendChild(li.cloneNode(true));
        });
        sidebar.appendChild(linksList);

        // Add auth buttons at the bottom of sidebar
        const authBtns = navRight.querySelectorAll('.btn-pill');
        if (authBtns.length > 0) {
            const btnGroup = document.createElement('div');
            btnGroup.className = 'sidebar-auth-buttons';
            authBtns.forEach(btn => {
                btnGroup.appendChild(btn.cloneNode(true));
            });
            sidebar.appendChild(btnGroup);
        }

        document.body.appendChild(sidebar);

        // 4. Toggle functions
        const openSidebar = (e) => {
            e.stopPropagation();
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('sidebar-no-scroll');
        };

        const closeSidebar = () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-no-scroll');
        };

        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Close when clicking any link inside sidebar
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
    }
});
