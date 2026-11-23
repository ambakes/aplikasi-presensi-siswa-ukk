</main>

<footer class="site-footer">
    &copy; <?= date('Y') ?> SMK TI Airlangga Samarinda - Aplikasi Presensi Siswa
</footer>

<script>
    // Tombol menu untuk tampilan smartphone
    var tombol = document.getElementById('navToggle');
    var menu = document.getElementById('navMenu');
    var labelMenu = tombol ? tombol.querySelector('.nav-toggle-text') : null;

    if (tombol && menu) {
        tombol.addEventListener('click', function () {
            var terbuka = menu.classList.toggle('buka');

            tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
            tombol.setAttribute('aria-label', terbuka ? 'Tutup menu' : 'Buka menu');

            if (labelMenu) {
                labelMenu.textContent = terbuka ? 'Tutup' : 'Menu';
            }
        });
    }
</script>
</body>
</html>