<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penerimaan Mahasiswa Baru | Universitas Harapan Bangsa</title>

    <meta name="description" content="Informasi Penerimaan Mahasiswa Baru Universitas Harapan Bangsa. Bergabunglah dan wujudkan masa depan yang lebih cerah.">

    <link rel="stylesheet" href="css/stayle.css">
</head>

<body>

    <div class="home-container">

        <!-- HEADER -->
        <header class="header">
            <a href="index.php" class="logo-area" aria-label="Beranda PMB Universitas Harapan Bangsa">
                <img
                    src="assets/logoo.jpg"
                    alt="Logo Universitas Airlangga"
                    class="univ-logo"
                >

                <div class="univ-name">
                    <strong>UNIVERSITAS</strong>
                    <span>UNAIR</span>
                </div>

                <span class="header-line"></span>

                <div class="logo-text">
                    <span class="pmb">PMB</span>
                    <span class="garis"></span>
                    <div class="logo-subtitle">
                        Penerimaan<br>
                        Mahasiswa Baru
                    </div>
                </div>
            </a>

            <nav class="navigation" aria-label="Navigasi utama">
                <a href="#beranda" class="nav-link active">BERANDA</a>
                <a href="#tentang" class="nav-link">TENTANG</a>
                <a href="#alur" class="nav-link">ALUR PENDAFTARAN</a>

                <a href="login.php" class="nav-login">
                    <span aria-hidden="true">↗</span>
                    LOGIN
                </a>
            </nav>
        </header>

        <!-- HERO -->
        <main>

           <section class="hero" id="beranda">

    <div class="hero-overlay"></div>

    <div class="hero-photo">
        <img
            src="assets/gedung-kampus.jpg"
            alt="Gedung kampus Universitas Airlangga"
        >
    </div>
                <div class="hero-content">

                    <div class="hero-text">

                        <div class="hero-badge">
                            <span class="badge-dot"></span>
                            PORTAL PENERIMAAN MAHASISWA BARU
                        </div>

                        <p class="welcome">
                            Selamat Datang di
                        </p>

                        <h1>
                            Wujudkan<br>
                            <span>Masa Depanmu</span><br>
                            Bersama Kami.
                        </h1>

                        <p class="hero-univ">
                            Universitas Airlangga
                        </p>

                        <p class="description">
                            Awali perjalanan menuju masa depan yang lebih cerah.
                            Kembangkan potensi, raih prestasi, dan jadilah bagian
                            dari generasi yang membawa perubahan.
                        </p>

                        <div class="hero-actions">

                            <a href="login.php" class="button button-primary">
                                <span>Daftar / Login Calon Mahasiswa</span>
                                <span class="button-arrow" aria-hidden="true">→</span>
                            </a>

                            <a href="admin/login.php" class="button button-secondary">
                                <span class="button-icon" aria-hidden="true">♙</span>
                                <span>Login Admin</span>
                            </a>

                        </div>

                        <div class="hero-note">
                            <span class="note-icon" aria-hidden="true">✓</span>
                            <span>Mulai langkahmu menuju masa depan yang lebih baik.</span>
                        </div>

                    </div>

                </div>

                <a href="#tentang" class="scroll-indicator" aria-label="Gulir ke bagian tentang">
                    <span class="scroll-line"></span>
                    <span>JELAJAHI</span>
                    <span aria-hidden="true">↓</span>
                </a>

            </section>

            <!-- INFORMASI SINGKAT -->
            <section class="intro-section" id="tentang">

                <div class="intro-heading">
                    <span class="section-label">SELAMAT DATANG, CALON MAHASISWA</span>

                    <h2>
                        Satu Langkah Hari Ini,<br>
                        <span>Masa Depan Gemilang Esok Hari.</span>
                    </h2>

                    <p>
                        Universitas Unair menjadi tempat untuk belajar,
                        berkembang, dan mempersiapkan diri menghadapi tantangan
                        masa depan. Mulailah perjalanan akademikmu bersama kami.
                    </p>
                </div>

                <div class="intro-card">
                    <div class="intro-card-icon" aria-hidden="true">01</div>
                    <div>
                        <h3>Buat Akun</h3>
                        <p>Masuk ke portal calon mahasiswa untuk memulai proses pendaftaran.</p>
                    </div>
                </div>

                <div class="intro-card">
                    <div class="intro-card-icon" aria-hidden="true">02</div>
                    <div>
                        <h3>Lengkapi Data</h3>
                        <p>Isi informasi pendaftaran sesuai petunjuk yang tersedia di portal.</p>
                    </div>
                </div>

                <div class="intro-card">
                    <div class="intro-card-icon" aria-hidden="true">03</div>
                    <div>
                        <h3>Mulai Perjalananmu</h3>
                        <p>Pantau informasi pendaftaran dan tahapan berikutnya melalui akunmu.</p>
                    </div>
                </div>

            </section>

            <!-- CTA -->
            <section class="cta-section" id="alur">
                <div class="cta-content">
                    <span class="section-label">MASA DEPANMU DIMULAI DI SINI</span>

                    <h2>Siap Memulai Perjalananmu?</h2>

                    <p>
                        Masuk ke portal PMB Universitas Airlangga dan
                        persiapkan langkah pertamamu.
                    </p>

                    <a href="login.php" class="button button-cta">
                        Masuk Portal PMB <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>

        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="footer-brand">
                <img src="assets/logoo.jpg" alt="" class="footer-logo">

                <div>
                    <strong>UNIVERSITAS UNAIR</strong>
                    <span>Portal Penerimaan Mahasiswa Baru</span>
                </div>
            </div>

            <p>
                &copy; <span id="current-year">2026</span>
                Universitas Airlangga. Seluruh hak cipta dilindungi.
            </p>
        </footer>

    </div>

    <script>
        document.getElementById("current-year").textContent =
            new Date().getFullYear();
    </script>

</body>
</html>