<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAJ Notary Hub - Notaris & PPAT Lilis Aenun Jariah S.H., M.Kn.</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: url('{{ asset("images/foto kantor.jpg") }}');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header x-data="{ mobileMenuOpen: false }" class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-3">
                <img src="{{ asset('images/Logo notaris bu lilis.png') }}" alt="Logo LAJ" class="h-10 w-10 object-contain">
                <span class="font-bold text-lg text-red-900">LAJ Notary Hub</span>
            </a>

            <nav class="hidden md:flex items-center space-x-8">
                <a href="#layanan" class="text-gray-600 hover:text-red-800 font-medium transition">Layanan</a>
                <a href="#tentang" class="text-gray-600 hover:text-red-800 font-medium transition">Tentang Kami</a>
                <a href="#kontak" class="text-gray-600 hover:text-red-800 font-medium transition">Kontak</a>
            </nav>

            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-red-800">Login</a>
                <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-red-800 px-5 py-2.5 rounded-lg hover:bg-red-700 transition shadow-sm hover:shadow-md">Register</a>
            </div>

            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m4 6H4"></path></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden bg-white border-t" x-cloak x-transition>
            <a href="#layanan" class="block px-6 py-3 text-sm text-gray-700 hover:bg-gray-100">Layanan</a>
            <a href="#tentang" class="block px-6 py-3 text-sm text-gray-700 hover:bg-gray-100">Tentang Kami</a>
            <a href="#kontak" class="block px-6 py-3 text-sm text-gray-700 hover:bg-gray-100">Kontak</a>
            <div class="px-6 py-4 border-t flex items-center space-x-4">
                <a href="{{ route('login') }}" class="flex-1 text-center text-sm font-medium text-gray-700 bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200">Login</a>
                <a href="{{ route('register') }}" class="flex-1 text-center text-sm font-medium text-white bg-red-800 px-4 py-2 rounded-md hover:bg-red-700">Register</a>
            </div>
        </div>
    </header>

    <main>
        <section class="relative hero-bg text-white py-24 md:py-40">
            <div class="absolute inset-0 bg-red-900 opacity-80"></div>
            <div class="container mx-auto px-6 text-center relative z-10">
                <h1 data-aos="fade-down" class="text-4xl md:text-6xl font-extrabold leading-tight">Layanan Notaris & PPAT Profesional</h1>
                <p data-aos="fade-up" data-aos-delay="200" class="mt-4 text-lg md:text-xl max-w-3xl mx-auto text-red-100">Platform digital resmi dari Kantor Notaris & PPAT Lilis Aenun Jariah S.H., M.Kn. yang dirancang untuk mempermudah proses pengajuan, verifikasi, dan manajemen dokumen hukum Anda secara cepat, aman, dan efisien.</p>
                <a href="{{ route('register') }}" data-aos="zoom-in" data-aos-delay="400" class="mt-8 inline-block bg-white text-red-800 font-bold px-10 py-4 rounded-lg hover:bg-gray-200 transition text-lg shadow-lg hover:shadow-xl">
                    Mulai Sekarang
                </a>
            </div>
        </section>

        <section id="layanan" class="py-20 md:py-28 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-red-900">Layanan Kami</h2>
                    <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Kami menyediakan berbagai layanan legal untuk kebutuhan perorangan maupun perusahaan.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300" data-aos="fade-right">
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-100 text-red-800 p-3 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                            <h3 class="text-2xl font-bold text-gray-800">Layanan Notaris</h3>
                        </div>
                        <p class="mt-4 text-gray-600">Pembuatan berbagai macam akta otentik sesuai dengan peraturan yang berlaku.</p>
                        <ul class="mt-5 space-y-3 text-gray-700">
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Akta Pendirian PT, CV, dan Yayasan</li>
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Akta Perjanjian (Sewa, Kerjasama, dll.)</li>
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Surat Keterangan Waris</li>
                        </ul>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300" data-aos="fade-left" data-aos-delay="100">
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-100 text-red-800 p-3 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                            <h3 class="text-2xl font-bold text-gray-800">Layanan PPAT</h3>
                        </div>
                        <p class="mt-4 text-gray-600">Pembuatan akta otentik terkait pertanahan di wilayah kerja yang ditentukan.</p>
                        <ul class="mt-5 space-y-3 text-gray-700">
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Akta Jual Beli (AJB)</li>
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Akta Hibah & Pembagian Hak Bersama</li>
                            <li class="flex items-start"><span class="text-red-800 mr-2">&#10003;</span> Pemasangan Hak Tanggungan (APHT)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="tentang" class="py-20 md:py-28 bg-white">
            <div class="container mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold text-red-900">Tentang Notaris & PPAT Lilis Aenun Jariah S.H., M.Kn.</h2>
                    <p class="mt-6 text-gray-600 leading-relaxed">Kantor kami berdedikasi untuk memberikan pelayanan hukum yang prima dengan menjunjung tinggi integritas, profesionalisme, dan ketepatan waktu. Dipimpin oleh Lilis Aenun Jariah S.H., M.Kn., seorang pejabat umum yang berpengalaman dan terpercaya, kami siap membantu mengamankan segala transaksi dan perbuatan hukum Anda dalam bentuk akta otentik.</p>
                </div>
                <div class="flex justify-center" data-aos="fade-left" data-aos-delay="100">
                    <img src="{{ asset('images/Logo notaris bu lilis.png') }}" alt="Foto Notaris" class="rounded-lg shadow-2xl w-full max-w-md object-cover">
                </div>
            </div>
        </section>

        <section id="kontak" class="py-20 md:py-28 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-red-900">Hubungi Kami</h2>
                <p class="mt-3 text-lg text-gray-600">Kami siap melayani Anda.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="mx-auto bg-red-800 text-white w-16 h-16 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-xl mt-4">Alamat Kantor</h4>
                    <a href="https://maps.app.goo.gl/6mVAvKCGaRpvKV8S8" target="_blank" rel="noopener noreferrer" class="block mt-1 text-gray-600 hover:text-red-800 hover:underline transition">
                        Jl. Raya Bandung No.11, Bojong, Kec. Karangtengah, Kabupaten Cianjur, Jawa Barat 43281
                    </a>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="mx-auto bg-red-800 text-white w-16 h-16 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <h4 class="font-bold text-xl mt-4">Telepon</h4>
                    <a href="https://wa.me/6281290753695" target="_blank" rel="noopener noreferrer" class="block mt-1 text-gray-600 hover:text-red-800 hover:underline transition">
                        +62-812-9075-3695
                    </a>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="mx-auto bg-red-800 text-white w-16 h-16 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-xl mt-4">Jam Operasional</h4>
                    <p class="mt-1 text-gray-600">Senin - Jumat: 08:00 - 17:00</p>
                </div>

            </div>
        </div>
</section>
    </main>

    <footer class="bg-red-900 text-red-200 py-12">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} LAJ Notary Hub. All Rights Reserved.</p>
        </div>
    </footer>

<div x-data="{ ivaOpen: false }" class="fixed bottom-6 right-6 z-50">
        <button @click="ivaOpen = !ivaOpen" 
                class="fixed bottom-6 right-6 flex items-center gap-2 bg-red-800 text-white px-5 py-3 rounded-full shadow-2xl hover:bg-red-900 hover:scale-105 transition-all duration-300 z-50">
            
            <span class="font-semibold text-sm">Konsultasi</span>
            
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </button>

        <div x-show="ivaOpen" 
            x-cloak 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute bottom-20 right-0 w-80 md:w-96 h-[500px] bg-white border-2 border-red-800 shadow-2xl rounded-2xl flex flex-col overflow-hidden">
            
            <iframe src="{{ route('iva.index') }}" class="w-full h-full border-none"></iframe>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800,
        once: true,
      });
    </script>

</body>
</html>