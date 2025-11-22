<?php
$pageTitle = "Digital Library Hub";
$cssFile = "/perpustakaan/assets/css/home.css"; 
include('templates/header.php'); 
?>

<main class="landing-wrapper">

    <section class="hero-dense">    
        <div class="hero-text">
            <div class="badge-new">
                Versi 2.0 Kini Hadir
            </div>
            
            <h1>Akses Literasi <br><span class="gradient-text">Tanpa Batas Ruang.</span></h1>
            <p>Platform perpustakaan digital terintegrasi. Pinjam buku fisik, baca e-book, atau akses jurnal internasional dalam satu akun.</p>
            

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
                <span>Sedang Dibaca</span>
                <strong>Atomic Habits</strong>
            </div>
            <div class="floating-card card-2">
                <span>Pengembalian</span>
                <strong>Tepat Waktu</strong>
            </div>
            <img src="./assets/img/books.png" alt="Library" class="hero-img">
        </div>
    </section>

    <div class="marquee-container">
        <div class="marquee-content">
            <span> BUKU TERPOPULER: 'FILOSOFI TERAS'</span> - 
            <span> KOLEKSI BARU: 'THE PSYCHOLOGY OF MONEY'</span> - 
            <span> JAM BUKA: SENIN-SABTU 08.00 - 16.00</span> - 
            <span>LAYANAN ONLINE 24 JAM</span> - 
        </div>
    </div>

   
    <section class="cta-dense">
        <div class="cta-wrapper">
            <div class="cta-text">
                <h3>Belum jadi anggota?</h3>
                <p>Daftar dalam 2 menit, gratis selamanya.</p>
            </div>
            <a href="/perpustakaan/register/" class="btn-black">
                Buat Akun
            </a>
        </div>
    </section>

</main>

<?php include('templates/footer.php'); ?>