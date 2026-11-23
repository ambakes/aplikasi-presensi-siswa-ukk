<?php
$judul = 'Beranda';

require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>

<section class="dashboard-hero" aria-labelledby="judul-dashboard">
    <div class="dashboard-intro">
        <h1 id="judul-dashboard">Presensi Siswa</h1>
        <p>SMK TI Airlangga Samarinda</p>
        <p>Aplikasi ini mencatat kehadiran siswa saat kartu ditap di gerbang sekolah.</p>

        <div class="dashboard-actions">
            <a href="kelas.php" class="dashboard-button">
                Lihat Daftar Kelas
            </a>

            <?php if (!isset($_SESSION['id_user'])): ?>
                <a href="login.php" class="dashboard-button dashboard-button-secondary">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>

    <section id="galeri-dashboard" class="dashboard-gallery" aria-label="Galeri SMK TI Airlangga">
        <div class="dashboard-slides" tabindex="0" aria-label="Geser untuk melihat foto berikutnya">
            <figure class="dashboard-slide" id="foto-sekolah-1">
                <img src="assets/images/images%20(12).jpg" width="299" height="168"
                     alt="Guru dan staf berdiri di halaman SMK TI Airlangga.">
                <figcaption>Kegiatan di halaman sekolah</figcaption>
            </figure>
            <figure class="dashboard-slide dashboard-slide-portrait" id="foto-sekolah-2">
                <img src="assets/images/images%20(13).jpg" width="201" height="251"
                     alt="Poster School Fun Walk SMK TI Airlangga.">
                <figcaption>School Fun Walk</figcaption>
            </figure>
            <figure class="dashboard-slide" id="foto-sekolah-3">
                <img src="assets/images/images%20(14).jpg" width="275" height="183"
                     alt="Siswa mengikuti kegiatan bersama guru di dalam ruangan.">
                <figcaption>Kegiatan siswa</figcaption>
            </figure>
        </div>
        <div class="dashboard-arrows" hidden>
            <button type="button" data-step="-1" aria-label="Foto sebelumnya">‹</button>
            <button type="button" data-step="1" aria-label="Foto berikutnya">›</button>
        </div>
        <div class="dashboard-gallery-controls">
            <a href="#foto-sekolah-1" aria-label="Foto 1: Kegiatan di halaman sekolah">1</a>
            <a href="#foto-sekolah-2" aria-label="Foto 2: School Fun Walk">2</a>
            <a href="#foto-sekolah-3" aria-label="Foto 3: Kegiatan siswa">3</a>
        </div>
    </section>
</section>

<script>
    (() => {
        const gallery = document.querySelector('#galeri-dashboard');
        if (!gallery) {
            return;
        }

        const track = gallery.querySelector('.dashboard-slides');
        const links = [...gallery.querySelectorAll('.dashboard-gallery-controls a')];
        const arrows = gallery.querySelector('.dashboard-arrows');
        const total = links.length;

        const currentIndex = () => Math.round(track.scrollLeft / track.clientWidth);

        function showPhoto(index) {
            const next = (index + total) % total;
            track.scrollTo({ left: track.clientWidth * next, behavior: 'smooth' });
        }

        function markCurrentPhoto() {
            const active = currentIndex();
            links.forEach((link, index) => {
                if (index === active) {
                    link.setAttribute('aria-current', 'true');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        }

        links.forEach((link, index) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                showPhoto(index);
            });
        });

        arrows.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', () => {
                showPhoto(currentIndex() + Number(button.dataset.step));
            });
        });

        // Geser galeri dengan mouse / pen. Di layar sentuh, swipe tetap
        // ditangani secara native oleh overflow + scroll snap.
        let dragging = false;
        let startX = 0;
        let startScroll = 0;
        let moved = 0;

        track.addEventListener('pointerdown', (event) => {
            if (event.pointerType === 'touch' || event.button !== 0) {
                return;
            }

            dragging = true;
            moved = 0;
            startX = event.clientX;
            startScroll = track.scrollLeft;

            track.classList.add('is-dragging');
            track.setPointerCapture(event.pointerId);
            event.preventDefault();
        });

        track.addEventListener('pointermove', (event) => {
            if (!dragging) {
                return;
            }

            const delta = event.clientX - startX;
            moved = Math.max(moved, Math.abs(delta));
            track.scrollLeft = startScroll - delta;
        });

        function endDrag(event) {
            if (!dragging) {
                return;
            }

            dragging = false;
            track.classList.remove('is-dragging');

            if (track.hasPointerCapture(event.pointerId)) {
                track.releasePointerCapture(event.pointerId);
            }

            // Setelah digeser, rapikan posisi ke foto terdekat.
            if (moved > 4) {
                showPhoto(currentIndex());
            }
        }

        track.addEventListener('pointerup', endDrag);
        track.addEventListener('pointercancel', endDrag);
        track.addEventListener('lostpointercapture', endDrag);
        track.addEventListener('dragstart', (event) => event.preventDefault());

        // Navigasi keyboard saat galeri sedang difokuskan.
        track.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                showPhoto(currentIndex() - 1);
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                showPhoto(currentIndex() + 1);
            }
        });

        track.addEventListener('scroll', markCurrentPhoto, { passive: true });
        window.addEventListener('resize', markCurrentPhoto);

        arrows.hidden = false;
        markCurrentPhoto();
    })();
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>
