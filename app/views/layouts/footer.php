</main>

<footer class="text-center py-3 mt-4 border-top">
    <small><?= htmlspecialchars($_app['app_name'] ?? 'Preguntero App') ?></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
(function () {
    if (typeof particlesJS === 'undefined') return;
    particlesJS('particles-js', {
        particles: {
            number: { value: 55, density: { enable: true, value_area: 900 } },
            color:  { value: '#00e5ff' },
            shape:  { type: 'circle' },
            opacity: {
                value: 0.22, random: true,
                anim: { enable: true, speed: 0.5, opacity_min: 0.05, sync: false }
            },
            size: { value: 2, random: true },
            line_linked: {
                enable: true, distance: 140,
                color: '#00e5ff', opacity: 0.07, width: 1
            },
            move: {
                enable: true, speed: 0.55, direction: 'none',
                random: true, straight: false, out_mode: 'out', bounce: false
            }
        },
        interactivity: {
            detect_on: 'canvas',
            events: {
                onhover: { enable: true, mode: 'grab' },
                onclick: { enable: false },
                resize:  true
            },
            modes: { grab: { distance: 110, line_linked: { opacity: 0.22 } } }
        },
        retina_detect: true
    });
}());
</script>
</body>
</html>
