<?php
$pageTitle = "Digital Library Hub";


$cssFile = "/perpustakaan/assets/css/home.css"; 
include('templates/header.php'); 
?>

<main class="landing-wrapper">

    <section class="hero-dense">
        <div class="hero-text">
            <div class="badge-new">
                <i class="fas fa-rocket"></i> Versi 2.0 Kini Hadir
            </div>
            
            <h1>Akses Literasi <br><span class="gradient-text">Tanpa Batas Ruang.</span></h1>
            <p>Platform perpustakaan digital terintegrasi. Pinjam buku fisik, baca e-book, atau akses jurnal internasional dalam satu akun.</p>
            
            <form action="/perpustakaan/cari/" class="search-bar-modern">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Cari judul, penulis, atau ISBN...">
                <button type="submit">Cari</button>
            </form>

            <div class="hero-stats-row">
                <div class="stat-mini">
                    <strong>12K+</strong> <span><i class="fas fa-book"></i> Buku</span>
                </div>
                <div class="stat-mini">
                    <strong>500+</strong> <span><i class="fas fa-newspaper"></i> Jurnal</span>
                </div>
                <div class="stat-mini">
                    <strong>4.9</strong> <span><i class="fas fa-star text-warning"></i> Rating</span>
                </div>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="floating-card card-1">
                <i class="fas fa-book-reader icon-blue"></i>
                <span>Sedang Dibaca</span>
                <strong>Atomic Habits</strong>
            </div>
            <div class="floating-card card-2">
                <i class="fas fa-check-circle icon-green"></i>
                <span>Pengembalian</span>
                <strong>Tepat Waktu</strong>
            </div>
            <img src="./assets/img/books.png" alt="Library" class="hero-img">
        </div>
    </section>

    <div class="marquee-container">
        <div class="marquee-content">
            <span><i class="fas fa-fire-flame-curved text-orange"></i> BUKU TERPOPULER: 'FILOSOFI TERAS'</span>
            <i class="fas fa-circle dot-separator"></i>
            
            <span><i class="fas fa-certificate text-yellow"></i> KOLEKSI BARU: 'THE PSYCHOLOGY OF MONEY'</span>
            <i class="fas fa-circle dot-separator"></i>
            
            <span><i class="far fa-clock text-blue"></i> JAM BUKA: SENIN-SABTU 08.00 - 16.00</span>
            <i class="fas fa-circle dot-separator"></i>
            
            <span><i class="fas fa-wifi text-green"></i> LAYANAN ONLINE 24 JAM</span>
            <i class="fas fa-circle dot-separator"></i>
        </div>
    </div>

    <section class="bento-section">
        <div class="section-head">
            <h2>Ekosistem Kami</h2>
            <a href="#" class="link-arrow">Lihat Semua Fitur <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="bento-grid">
            <div class="bento-box box-large bg-dark">
                <div class="box-content">
                    <h3>E-Library Access</h3>
                    <p>Baca buku digital langsung dari browser atau aplikasi mobile tanpa perlu datang ke lokasi.</p>
                    <a href="#" class="btn-glass"><i class="fas fa-play"></i> Mulai Baca</a>
                </div>
                <i class="fas fa-tablet-screen-button bg-icon"></i>
            </div>

            <div class="bento-box box-tall bg-light">
                <div class="icon-circle"><i class="fas fa-history"></i></div>
                <h3>Riwayat</h3>
                <p>Lacak semua buku yang pernah Anda pinjam seumur hidup.</p>
                <div class="mini-chart-visual">
                    <span style="height: 40%"></span>
                    <span style="height: 70%"></span>
                    <span style="height: 50%"></span>
                    <span style="height: 90%"></span>
                </div> 
            </div>

            <div class="bento-box box-wide bg-accent">
                <div class="box-row">
                    <div>
                        <h3>Booking Buku</h3>
                        <p>Amankan buku incaran.</p>
                    </div>
                    <i class="far fa-bookmark big-icon"></i>
                </div>
            </div>

            <div class="bento-box box-small bg-white">
                <div class="center-content">
                    <i class="fas fa-wifi text-primary fa-2x mb-2"></i>
                    <h3>WiFi Gratis</h3>
                </div>
            </div>
            
            <div class="bento-box box-small bg-white">
                <div class="center-content">
                    <i class="fas fa-mug-hot text-coffee fa-2x mb-2"></i>
                    <h3>Cafe Area</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-dense">
        <div class="cta-wrapper">
            <div class="cta-text">
                <h2>Belum jadi anggota?</h2>
                <p>Daftar dalam 2 menit, gratis selamanya.</p>
            </div>
            <a href="/perpustakaan/login/" class="btn-black">
                <i class="fas fa-user-plus"></i> Buat Akun
            </a>
        </div>
    </section>

</main>

<?php include('templates/footer.php'); ?>