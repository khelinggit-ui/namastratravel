<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\DestinationGallery;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\TourGallery;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset untuk idempotensi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TourGallery::truncate();
        DestinationGallery::truncate();
        DB::table('destination_tour')->truncate();
        Tour::truncate();
        Destination::truncate();
        Post::truncate();
        Testimonial::truncate();
        Booking::truncate();
        Contact::truncate();
        Setting::truncate();
        AboutContent::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $img = fn ($id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=1200&q=70";

        // ---- Admin user ----
        User::updateOrCreate(['email' => 'admin@namastratravel.com'], [
            'name' => 'Admin Namastra',
            'password' => Hash::make('namastra2026'),
            'is_admin' => true,
        ]);

        // ---- Settings ----
        Setting::setMany([
            'name' => 'Namastra Travel',
            'tagline' => 'Jelajah Dunia Bersama Kami',
            'address' => 'Jl. Nusantara No. 45, Jakarta Selatan, Indonesia',
            'phone' => '+62 812 3456 7890',
            'whatsapp' => '6281234567890',
            'email' => 'info@namastratravel.com',
            'hours' => 'Senin – Sabtu, 09.00 – 18.00 WIB',
            'instagram' => 'https://instagram.com/namastratravel',
            'facebook' => 'https://facebook.com/namastratravel',
            'tiktok' => 'https://tiktok.com/@namastratravel',
            'maps_embed_url' => 'https://www.openstreetmap.org/export/embed.html?bbox=106.7500%2C-6.3000%2C106.9000%2C-6.1500&layer=mapnik',
        ]);

        // ---- Tours ----
        $tourData = [
            // ============ DOMESTIK ============
            [
                'slug' => 'bali-ultimate-honeymoon', 'title' => 'Bali Ultimate Honeymoon', 'category' => 'domestik',
                'duration' => '5 Hari 4 Malam', 'price_start' => 4500000, 'location' => 'Bali, Indonesia',
                'tagline' => 'Romantis dan tenang di Pulau Dewata',
                'description' => 'Paket honeymoon paling diminati di Bali. Nikmati penginapan resort tepi pantai di Seminyak, spa berdua, sunset dinner romantis, serta tur ke Ubud dan Pura Tanah Lot. Semua transportasi privat dengan driver guide berpengalaman, Anda cukup bersantai menikmati momen.',
                'image' => $img('1518548419971-58eeca3156c6f'), 'sort' => 1, 'tag' => 'Terpopuler',
                'highlights' => ['Welcome drink & fruit basket', 'Spa couple 1× di resort', 'Sunset dinner di tepi pantai', 'Tur Ubud & Tanah Lot', 'Transport privat + driver guide', 'Asuransi perjalanan'],
                'gallery' => [$img('1518548419971-58eeca3156c6f'), $img('1520250497591-112f2f40a3f4'), $img('1537996194471-e657df975ab4')],
            ],
            [
                'slug' => 'nusa-penida-getaway', 'title' => 'Nusa Penida & Ubud Getaway', 'category' => 'domestik',
                'duration' => '3 Hari 2 Malam', 'price_start' => 2950000, 'location' => 'Bali & Nusa Penida, Indonesia',
                'tagline' => 'Tebing dramatis, laut biru, dan sawah hijau Ubud',
                'description' => 'Perpaduan petualangan pulau dan ketenangan pedesaan Bali. Kunjungi Kelingking Beach, Angel’s Billabong, dan Broken Beach di Nusa Penida, lalu lanjut ke Ubud untuk wisata sawah, monkey forest, dan kelas memasak Bali.',
                'image' => $img('1552733407-5d5c46c3bb3b'), 'sort' => 2, 'tag' => 'Highlight',
                'highlights' => ['Kelingking Beach & Broken Beach', 'Snorkeling Crystal Bay', 'Monkey Forest Ubud', 'Kelas memasak masakan Bali', 'Penginapan resort Nusa Penida', 'Transportasi speedboat AC'],
                'gallery' => [$img('1552733407-5d5c46c3bb3b'), $img('1537996194471-e657df975ab4'), $img('1520250497591-112f2f40a3f4')],
            ],
            [
                'slug' => 'yogyakarta-cultural-tour', 'title' => 'Yogyakarta Cultural Tour', 'category' => 'domestik',
                'duration' => '3 Hari 2 Malam', 'price_start' => 1650000, 'location' => 'Yogyakarta, Indonesia',
                'tagline' => 'Budaya Jawa dalam satu perjalanan',
                'description' => 'Eksplorasi budaya dan sejarah Jogja: sunrise Candi Borobudur, Keraton Yogyakarta, Taman Sari, Malioboro, dan kelas batik. Cocok untuk keluarga, pelajar, dan kelompok kantor. Harga paling ramah untuk pengalaman budaya terlengkap.',
                'image' => $img('1581141437454-c4b8c70dbad1'), 'sort' => 3, 'tag' => 'Value for Money',
                'highlights' => ['Sunrise Candi Borobudur', 'Keraton & Taman Sari', 'Belanja di Malioboro', 'Kelas membatik', 'Makan Gudeg & kuliner khas', 'Bus wisata ber-AC'],
                'gallery' => [$img('1581141437454-c4b8c70dbad1'), $img('1518548419971-58eeca3156c6f'), $img('1552733407-5d5c46c3bb3b')],
            ],
            [
                'slug' => 'bromo-ijen-sunrise', 'title' => 'Bromo & Ijen Sunrise Adventure', 'category' => 'domestik',
                'duration' => '3 Hari 2 Malam', 'price_start' => 3750000, 'location' => 'Bromo & Banyuwangi, Indonesia',
                'tagline' => 'Dua sunrise paling ikonik Indonesia',
                'description' => 'Perjalanan epik menuju Kawah Bromo yang dramatis lalu lanjut ke Kawah Ijen untuk melihat Blue Fire dan danau asam terbesar di dunia. Jalur favorit fotografer dan pecinta alam, dengan jeep 4WD dan porter lokal yang berpengalaman.',
                'image' => $img('1533139502658-65d1d3aa3d5e'), 'sort' => 4, 'tag' => 'Petualangan',
                'highlights' => ['Sunrise dari Penanjakan Bromo', 'Kawah Ijen Blue Fire', 'Jeep 4WD di lautan pasir', 'Air terjun Tumpak Sewu (opsional)', 'Porter & senter kepala', 'Penginapan homestay hangat'],
                'gallery' => [$img('1533139502658-65d1d3aa3d5e'), $img('1552733407-5d5c46c3bb3b'), $img('1493976040379-85c8e12e0c0e')],
            ],
            [
                'slug' => 'raja-ampat-exotic', 'title' => 'Raja Ampat Exotic Diving', 'category' => 'domestik',
                'duration' => '6 Hari 5 Malam', 'price_start' => 12500000, 'location' => 'Papua Barat, Indonesia',
                'tagline' => 'Surga bawah laut terbaik di dunia',
                'description' => 'Diving dan snorkeling di salah satu pusat keanekaragaman hayati laut terbaik dunia. Termasuk paket liveaboard, perlengkapan diving lengkap, dive master profesional, serta menjelajahi laguna Wayag dan pulau-pulau karang.',
                'image' => $img('1573793365208-92d0d73819bb'), 'sort' => 5, 'tag' => 'Petualangan',
                'highlights' => ['10 spot diving pilihan', 'Laguna Wayag ikonik', 'Liveaboard & makan lengkap', 'Snorkeling bersama manta', 'Dive master bersertifikat', 'Alat diving sewa gratis'],
                'gallery' => [$img('1573793365208-92d0d73819bb'), $img('1518548419971-58eeca3156c6f'), $img('1537996194471-e657df975ab4')],
            ],
            [
                'slug' => 'labuan-bajo-komodo', 'title' => 'Labuan Bajo & Komodo Adventure', 'category' => 'domestik',
                'duration' => '4 Hari 3 Malam', 'price_start' => 5800000, 'location' => 'Labuan Bajo, NTT, Indonesia',
                'tagline' => 'Bertemu komodo & menikmati sunset di Pulau Padar',
                'description' => 'Jelajahi Taman Nasional Komodo: trekking Pulau Rinca bertemu komodo, mendaki Pulau Padar untuk panorama teluk terbaik dunia, snorkeling di Pink Beach, dan pulau-pulau eksotis sekitarnya. Paket lengkap dengan pemandu nasional berpengalaman.',
                'image' => $img('1520250497591-112f2f40a3f4'), 'sort' => 6, 'tag' => 'Terpopuler',
                'highlights' => ['Trekking Pulau Rinca & komodo', 'Sunset Pulau Padar', 'Snorkeling Pink Beach', 'Makanan laut segar', 'Liveaboard semalam', 'Pemandu taman nasional'],
                'gallery' => [$img('1520250497591-112f2f40a3f4'), $img('1518548419971-58eeca3156c6f'), $img('1573793365208-92d0d73819bb')],
            ],
            [
                'slug' => 'lombok-gili-discovery', 'title' => 'Lombok & Gili Island Discovery', 'category' => 'domestik',
                'duration' => '4 Hari 3 Malam', 'price_start' => 3600000, 'location' => 'Lombok & Gili Trawangan, Indonesia',
                'tagline' => 'Pantai murni, Gunung Rinjani, dan tiga gili eksotis',
                'description' => 'Kombinasi sempurna Lombok dan Gili Trawangan: pantai Kuta Lombok yang masih alami, desa Sasak, air terjun, lalu bersantai di Gili dengan bersepeda dan snorkeling di air yang jernih. Ideal untuk liburan santai dan keluarga.',
                'image' => $img('1518548419971-58eeca3156c6f'), 'sort' => 7,
                'highlights' => ['Pantai Kuta Lombok', 'Desa adat Sasak', 'Air terjun Sendang Gile', 'Snorkeling Gili Trawangan', 'Sewa sepeda di Gili', 'Homestay & resort tepi pantai'],
                'gallery' => [$img('1518548419971-58eeca3156c6f'), $img('1552733407-5d5c46c3bb3b'), $img('1520250497591-112f2f40a3f4')],
            ],
            [
                'slug' => 'danau-toba-horizon', 'title' => 'Danau Toba & Parapat Horizon', 'category' => 'domestik',
                'duration' => '4 Hari 3 Malam', 'price_start' => 3200000, 'location' => 'Sumatera Utara, Indonesia',
                'tagline' => 'Kaldera vulkanik terbesar di dunia',
                'description' => 'Berlibur tenang di Danau Toba dengan pemandangan Samosir, budaya Batak yang khas, air terjun Sipiso-piso, dan kuliner lezat di Parapat. Paket sangat cocok untuk keluarga besar dan reuni dengan harga terjangkau.',
                'image' => $img('1552733407-5d5c46c3bb3b'), 'sort' => 8, 'tag' => 'Promo',
                'highlights' => ['Kapal feri ke Pulau Samosir', 'Desa budaya Batak', 'Air terjun Sipiso-piso', 'Kuliner khas Danau Toba', 'Hotel tepi danau', 'Mini zoo & Pemandian Air Panas'],
                'gallery' => [$img('1552733407-5d5c46c3bb3b'), $img('1518548419971-58eeca3156c6f'), $img('1537996194471-e657df975ab4')],
            ],

            // ============ MANCANEGARA ============
            [
                'slug' => 'singapura-malaysia-tour', 'title' => 'Singapore & Malaysia City Tour', 'category' => 'mancanegara',
                'duration' => '5 Hari 4 Malam', 'price_start' => 8900000, 'location' => 'Singapura & Kuala Lumpur',
                'tagline' => 'Dua negara, satu perjalanan seru',
                'description' => 'Jelajahi kemegahan Gardens by the Bay dan Marina Bay Sands di Singapura, lalu lanjut ke Kuala Lumpur untuk melihat Petronas Twin Tower, Batu Caves, dan pasar malam Jalan Alor. Hotel strategis dekat pusat perbelanjaan.',
                'image' => $img('1533139502658-65d1d3aa3d5e'), 'sort' => 9,
                'highlights' => ['Gardens by the Bay', 'Merlion Park', 'Petronas Twin Tower', 'Batu Caves', 'Shopping di Orchard & Bukit Bintang', 'Tour guide bahasa Indonesia'],
                'gallery' => [$img('1533139502658-65d1d3aa3d5e'), $img('1493976040379-85c8e12e0c0e'), $img('1552733407-5d5c46c3bb3b')],
            ],
            [
                'slug' => 'thailand-bangkok-phuket', 'title' => 'Thailand Visit: Bangkok & Phuket', 'category' => 'mancanegara',
                'duration' => '5 Hari 4 Malam', 'price_start' => 7800000, 'location' => 'Thailand',
                'tagline' => 'Kota & pantai tropis dalam satu paket',
                'description' => 'Kombinasi dua sisi Thailand: kuil emas dan street food Bangkok, lalu pantai putih Phuket dengan aktivitas air dan sunset. Termasuk penerbangan domestik Bangkok–Phuket agar perjalanan Anda efisien dan nyaman.',
                'image' => $img('1508009603885-50cf7c579365'), 'sort' => 10,
                'highlights' => ['Grand Palace & Wat Phra Kaew', 'Floating Market', 'Pantai Patong Phuket', 'Snorkeling Pulau Phi Phi', 'Street food khas Thailand', 'Penerbangan domestik Bangkok–Phuket'],
                'gallery' => [$img('1508009603885-50cf7c579365'), $img('1518548419971-58eeca3156c6f'), $img('1520250497591-112f2f40a3f4')],
            ],
            [
                'slug' => 'korea-seoul-busan', 'title' => 'South Korea Seoul & Busan Highlights', 'category' => 'mancanegara',
                'duration' => '6 Hari 5 Malam', 'price_start' => 15900000, 'location' => 'Seoul & Busan, Korea Selatan',
                'tagline' => 'K-pop, istana, dan pantai dalam satu paket',
                'description' => 'Rasakan pesona Korea: istana Gyeongbokgung, desa tradisional Bukchon, dan kawasan K-pop di Hongdae. Lalu naik KTX ke Busan untuk menikmati pantai Haeundae, Gamcheon Culture Village, dan kuliner street food yang legendaris.',
                'image' => $img('1528360983277-13d401cdc186'), 'sort' => 11, 'tag' => 'Trending',
                'highlights' => ['Istana Gyeongbokgung', 'Desa Bukchon Hanok', 'Kawasan K-pop Hongdae', 'Pantai Haeundae Busan', 'Gamcheon Culture Village', 'Tiket KTX Seoul–Busan'],
                'gallery' => [$img('1528360983277-13d401cdc186'), $img('1493976040379-85c8e12e0c0e'), $img('1533139502658-65d1d3aa3d5e')],
            ],
            [
                'slug' => 'turki-istanbul-cappadocia', 'title' => 'Turki Istanbul & Cappadocia Balloon', 'category' => 'mancanegara',
                'duration' => '8 Hari 6 Malam', 'price_start' => 22500000, 'location' => 'Istanbul & Cappadocia, Turki',
                'tagline' => 'Eropa bertemu Asia, naik balon udara di Cappadocia',
                'description' => 'Perjalanan magis melintasi dua benua: Hagia Sophia, Grand Bazaar, dan selat Bosphorus di Istanbul, lalu balon udara pagi hari di Cappadocia dengan formasi bebatuan unik. Paket pemandangan dan budaya terlengkap di Turki.',
                'image' => $img('1493976040379-85c8e12e0c0e'), 'sort' => 12, 'tag' => 'Highlight',
                'highlights' => ['Balon udara Cappadocia', 'Hagia Sophia & Blue Mosque', 'Grand Bazaar Istanbul', 'Kapal selat Bosphorus', 'Kota bawah tanah Derinkuyu', 'Hotel gua Cappadocia'],
                'gallery' => [$img('1493976040379-85c8e12e0c0e'), $img('1533139502658-65d1d3aa3d5e'), $img('1528360983277-13d401cdc186')],
            ],
            [
                'slug' => 'japan-autumn-tour', 'title' => 'Japan Autumn Wonder', 'category' => 'mancanegara',
                'duration' => '7 Hari 6 Malam', 'price_start' => 18500000, 'location' => 'Tokyo, Kyoto, Osaka',
                'tagline' => 'Musim gugur terindah di negeri sakura',
                'description' => 'Nikmati puncak musim gugur Jepang — Tokyo yang modern, Kyoto bertabur maple merah di Kuil Kiyomizu dan Arashiyama, serta Osaka dengan kuliner dan Universal Studios. Termasuk tiket Shinkansen dan hotel bintang 4 di lokasi strategis.',
                'image' => $img('1493976040379-85c8e12e0c0e'), 'sort' => 13, 'tag' => 'Terpopuler',
                'highlights' => ['Shinkansen Tokyo–Kyoto–Osaka', 'Kuil Kiyomizu maple merah', 'Hutan bambu Arashiyama', 'Universal Studios Osaka', 'Wisata kuliner Dotonbori', 'Hotel bintang 4'],
                'gallery' => [$img('1493976040379-85c8e12e0c0e'), $img('1528360983277-13d401cdc186'), $img('1533139502658-65d1d3aa3d5e')],
            ],
            [
                'slug' => 'europe-classic-rome-paris', 'title' => 'Europe Classic: Rome & Paris', 'category' => 'mancanegara',
                'duration' => '9 Hari 7 Malam', 'price_start' => 28500000, 'location' => 'Italia & Prancis',
                'tagline' => 'Romantis & klasik dua kota ikonik',
                'description' => 'Satu paket memadukan pesona Roma dan Paris. Jelajahi Colosseum, Trevi Fountain, dan Vatikan di Roma, lalu Eiffel Tower, Louvre, dan Seine River Cruise di Paris. Termasuk penerbangan antarkota dan hotel bintang 4 pusat kota.',
                'image' => $img('1499856871958-5b9627545d1a'), 'sort' => 14, 'tag' => 'Terpopuler',
                'highlights' => ['Colosseum & Forum Romawi', 'Air Mancur Trevi', 'Kota Vatikan', 'Menara Eiffel & Louvre', 'Seine River Cruise malam', 'Penerbangan antarkota'],
                'gallery' => [$img('1499856871958-5b9627545d1a'), $img('1533139502658-65d1d3aa3d5e'), $img('1493976040379-85c8e12e0c0e')],
            ],
        ];

        foreach ($tourData as $data) {
            $gallery = $data['gallery'] ?? [];
            unset($data['gallery']);
            $tour = Tour::create($data);
            foreach (array_values($gallery) as $k => $g) {
                $tour->gallery()->create(['image' => $g, 'sort' => $k]);
            }
        }

        // ---- Destinations ----
        $destData = [
            [
                'slug' => 'bali', 'name' => 'Bali', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Pulau Dewata dengan pantai pasir putih, sawah terasering, dan budaya Hindu yang kental. Destinasi paling favorit untuk honeymoon, wisata keluarga, dan retreat yoga.',
                'image' => $img('1518548419971-58eeca3156c6f'), 'sort' => 1,
                'gallery' => [$img('1518548419971-58eeca3156c6f'), $img('1552733407-5d5c46c3bb3b'), $img('1537996194471-e657df975ab4')],
                'tourSlugs' => ['bali-ultimate-honeymoon', 'nusa-penida-getaway'],
            ],
            [
                'slug' => 'nusa-penida', 'name' => 'Nusa Penida', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Pulau eksotis di selatan Bali dengan tebing dramatis, laguna biru, dan spot snorkeling kelas dunia seperti Manta Point.',
                'image' => $img('1552733407-5d5c46c3bb3b'), 'sort' => 2,
                'gallery' => [$img('1552733407-5d5c46c3bb3b'), $img('1537996194471-e657df975ab4')],
                'tourSlugs' => ['nusa-penida-getaway'],
            ],
            [
                'slug' => 'yogyakarta', 'name' => 'Yogyakarta', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Pusat budaya Jawa dengan Candi Borobudur, Keraton, seni batik, dan kuliner gudeg yang melegenda. Wajib dikunjungi untuk wisata sejarah dan edukasi.',
                'image' => $img('1581141437454-c4b8c70dbad1'), 'sort' => 3,
                'gallery' => [$img('1581141437454-c4b8c70dbad1'), $img('1552733407-5d5c46c3bb3b')],
                'tourSlugs' => ['yogyakarta-cultural-tour'],
            ],
            [
                'slug' => 'bromo-ijen', 'name' => 'Bromo & Ijen', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Gunung Bromo yang megah di lautan pasir dan Kawah Ijen dengan fenomena Blue Fire — surga fotografi dan petualangan pendakian ringan.',
                'image' => $img('1533139502658-65d1d3aa3d5e'), 'sort' => 4,
                'gallery' => [$img('1533139502658-65d1d3aa3d5e'), $img('1552733407-5d5c46c3bb3b')],
                'tourSlugs' => ['bromo-ijen-sunrise'],
            ],
            [
                'slug' => 'raja-ampat', 'name' => 'Raja Ampat', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Kekayaan laut terbaik dunia dengan karang yang memukau, cocok untuk diving, snorkeling, dan wisata bahari yang tak terlupakan.',
                'image' => $img('1573793365208-92d0d73819bb'), 'sort' => 5,
                'gallery' => [$img('1573793365208-92d0d73819bb'), $img('1537996194471-e657df975ab4')],
                'tourSlugs' => ['raja-ampat-exotic'],
            ],
            [
                'slug' => 'labuan-bajo', 'name' => 'Labuan Bajo', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Gerbang menuju Taman Nasional Komodo — komodo purba, Pulau Padar yang ikonik, dan Pink Beach yang memesona.',
                'image' => $img('1520250497591-112f2f40a3f4'), 'sort' => 6,
                'gallery' => [$img('1520250497591-112f2f40a3f4'), $img('1518548419971-58eeca3156c6f')],
                'tourSlugs' => ['labuan-bajo-komodo'],
            ],
            [
                'slug' => 'lombok', 'name' => 'Lombok', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Pantai yang masih murni, Gunung Rinjani yang megah, serta tiga gili (Trawangan, Meno, Air) dengan air laut sebening kristal.',
                'image' => $img('1518548419971-58eeca3156c6f'), 'sort' => 7,
                'gallery' => [$img('1518548419971-58eeca3156c6f'), $img('1552733407-5d5c46c3bb3b')],
                'tourSlugs' => ['lombok-gili-discovery'],
            ],
            [
                'slug' => 'danau-toba', 'name' => 'Danau Toba', 'country' => 'Indonesia', 'type' => 'Domestik',
                'description' => 'Kaldera vulkanik terbesar di dunia dengan Pulau Samosir, budaya Batak yang kaya, dan udara pegunungan yang sejuk.',
                'image' => $img('1552733407-5d5c46c3bb3b'), 'sort' => 8,
                'gallery' => [$img('1552733407-5d5c46c3bb3b'), $img('1537996194471-e657df975ab4')],
                'tourSlugs' => ['danau-toba-horizon'],
            ],
            [
                'slug' => 'singapura', 'name' => 'Singapura', 'country' => 'Singapura', 'type' => 'Mancanegara',
                'description' => 'Kota-negara super modern dengan taman futuristik, kuliner street food kelas dunia, dan pusat belanja yang tidak pernah tidur.',
                'image' => $img('1533139502658-65d1d3aa3d5e'), 'sort' => 9,
                'gallery' => [$img('1533139502658-65d1d3aa3d5e'), $img('1493976040379-85c8e12e0c0e')],
                'tourSlugs' => ['singapura-malaysia-tour'],
            ],
            [
                'slug' => 'thailand', 'name' => 'Thailand', 'country' => 'Thailand', 'type' => 'Mancanegara',
                'description' => 'Street food legendaris, kuil emas yang memukau, dan pantai tropis — kombinasi wisata budaya dan santai terbaik di Asia Tenggara.',
                'image' => $img('1508009603885-50cf7c579365'), 'sort' => 10,
                'gallery' => [$img('1508009603885-50cf7c579365'), $img('1518548419971-58eeca3156c6f')],
                'tourSlugs' => ['thailand-bangkok-phuket'],
            ],
            [
                'slug' => 'korea', 'name' => 'Korea Selatan', 'country' => 'Korea Selatan', 'type' => 'Mancanegara',
                'description' => 'Istana bersejarah, kawasan K-pop, street food, dan pantai cantik — perpaduan modern dan tradisi yang wajib dijelajahi.',
                'image' => $img('1528360983277-13d401cdc186'), 'sort' => 11,
                'gallery' => [$img('1528360983277-13d401cdc186'), $img('1493976040379-85c8e12e0c0e')],
                'tourSlugs' => ['korea-seoul-busan'],
            ],
            [
                'slug' => 'turki', 'name' => 'Turki', 'country' => 'Turki', 'type' => 'Mancanegara',
                'description' => 'Dua benua dalam satu negara — sejarah Ottoman di Istanbul, balon udara Cappadocia, dan kuliner yang menggugah selera.',
                'image' => $img('1493976040379-85c8e12e0c0e'), 'sort' => 12,
                'gallery' => [$img('1493976040379-85c8e12e0c0e'), $img('1533139502658-65d1d3aa3d5e')],
                'tourSlugs' => ['turki-istanbul-cappadocia'],
            ],
            [
                'slug' => 'japan', 'name' => 'Jepang', 'country' => 'Jepang', 'type' => 'Mancanegara',
                'description' => 'Negara modern dengan tradisi kental — kuil bersejarah, musim gugur yang indah, kereta cepat, dan kuliner kelas dunia.',
                'image' => $img('1528360983277-13d401cdc186'), 'sort' => 13,
                'gallery' => [$img('1528360983277-13d401cdc186'), $img('1493976040379-85c8e12e0c0e')],
                'tourSlugs' => ['japan-autumn-tour'],
            ],
            [
                'slug' => 'eropa', 'name' => 'Eropa', 'country' => 'Italia & Prancis', 'type' => 'Mancanegara',
                'description' => 'Romantis dan klasik — Colosseum Roma, Menara Eiffel Paris, seni, sejarah, dan arsitektur yang abadi.',
                'image' => $img('1499856871958-5b9627545d1a'), 'sort' => 14,
                'gallery' => [$img('1499856871958-5b9627545d1a'), $img('1533139502658-65d1d3aa3d5e')],
                'tourSlugs' => ['europe-classic-rome-paris'],
            ],
        ];

        foreach ($destData as $data) {
            $tourSlugs = $data['tourSlugs'] ?? [];
            $gallery = $data['gallery'] ?? [];
            unset($data['tourSlugs'], $data['gallery']);
            $destination = Destination::create($data);
            foreach (array_values($gallery) as $k => $g) {
                $destination->gallery()->create(['image' => $g, 'sort' => $k]);
            }
            $destination->tours()->sync(Tour::whereIn('slug', $tourSlugs)->pluck('id'));
        }

        // ---- Posts ----
        $postData = [
            [
                'slug' => 'tips-packing-honeymoon', 'title' => '8 Tips Packing untuk Honeymoon yang Anti Ribet',
                'category' => 'Tips Travel', 'author' => 'Tim Namastra', 'published_at' => '2026-07-20',
                'cover' => $img('1452421822248-d4c2b47f0c58'),
                'excerpt' => 'Agar honeymoon Anda mulus tanpa drama bagasi, simak daftar packing pintar berikut ini.',
                'body' => [
                    'Honeymoon adalah momen yang ingin Anda nikmati tanpa gangguan. Salah satu kunci agar perjalanan mulus adalah packing yang rapi. Mulailah dengan membawa outfit netral yang mudah dipadukan, lalu tambahkan satu aksen warna untuk foto-foto aesthetic.',
                    'Jangan lupa salinan softcopy dokumen penting, charger international adapter untuk perjalanan ke luar negeri, dan kotak P3K kecil. Berikan ruang ekstra di koper untuk oleh-oleh.',
                    'Terakhir, yang paling penting: jangan memaksakan jadwal terlalu padat. Honeymoon seharusnya santai, cukup satu tempat ikonik per hari. Berdua menikmati momen jauh lebih berharga daripada mengejar banyak spot foto.',
                ],
            ],
            [
                'slug' => 'itinerary-3hari-jogja', 'title' => 'Itinerary 3 Hari 2 Malam di Yogyakarta',
                'category' => 'Itinerary', 'author' => 'Tim Namastra', 'published_at' => '2026-07-05',
                'cover' => $img('1581141437454-c4b8c70dbad1'),
                'excerpt' => 'Dari Borobudur hingga Malioboro — rencana jalan-jalan Jogja yang padat namun tetap santai.',
                'body' => [
                    'Hari pertama kami sarankan berkeliling area Malioboro dan Keraton Yogyakarta. Mulailah dengan sarapan gudeg, lalu eksplor Keraton, Taman Sari, dan di sore hari lanjut ke Alun-Alun Kidul sebelum menikmati night market Malioboro.',
                    'Hari kedua: sunrise Candi Borobudur. Bangun pagi buta, nikmati matahari terbit dengan siluet candi di latar belakang, lalu lanjut ke Desa Wisata dan mencoba kelas membatik di Kampung Batik Giriloyo.',
                    'Hari ketiga: kunjungi Candi Prambanan dan HeHa Sky View, lalu berangkat pulang membawa batik dan bakpia sebagai oleh-oleh khas. Itinerary ini pas untuk pemula yang ingin merasakan esensi Jogja dalam tiga hari.',
                ],
            ],
            [
                'slug' => 'keuntungan-tour-grup', 'title' => 'Kenapa Booking Tour Grup Bisa Menghemat Biaya?',
                'category' => 'Tips Travel', 'author' => 'Namastra Travel', 'published_at' => '2026-06-18',
                'cover' => $img('1533139502658-65d1d3aa3d5e'),
                'excerpt' => 'Tour grup ternyata bisa jauh lebih hemat dibanding pergi sendirian. Simak alasannya demi budget traveling Anda.',
                'body' => [
                    'Harga hotel, transportasi, dan tiket objek wisata sering kali jauh lebih murah jika dibeli secara borongan dalam jumlah besar. Inilah keuntungan utama booking dalam bentuk grup — biaya per orang bisa turun hingga 30 persen.',
                    'Anda juga mendapat jasa tour guide dan itinerary yang sudah diatur secara efisien, sehingga waktu liburan tidak terbuang untuk riset dan negosiasi sendiri. Semua logistik sudah diurus tim operation.',
                    'Terlebih untuk keluarga atau rekan kantor, tour grup menciptakan momen kebersamaan yang lebih berkesan. Namun pastikan memilih agen terpercaya yang transparan soal biaya dan legalitas seperti Namastra Travel.',
                ],
            ],
            [
                'slug' => 'panduan-snorkeling-raja-ampat', 'title' => 'Panduan Snorkeling di Raja Ampat untuk Pemula',
                'category' => 'Tips Travel', 'author' => 'Tim Namastra', 'published_at' => '2026-07-28',
                'cover' => $img('1573793365208-92d0d73819bb'),
                'excerpt' => 'Surga bawah laut Raja Ampat bisa dinikmati oleh siapa saja. Ini panduan lengkap untuk pemula.',
                'body' => [
                    'Raja Ampat dikenal memiliki keanekaragaman hayati laut tertinggi di dunia, dengan lebih dari 75 persen spesies karang dunia. Kabar baiknya, Anda tidak perlu menjadi diver handal untuk menikmatinya — snorkeling saja sudah luar biasa.',
                    'Gunakan perlengkapan yang pas: masker yang tidak bocor, snorkel dengan katup, dan fin bersirip penuh agar hemat tenaga. Mintalah bantuan guide untuk menyesuaikan ukuran sebelum turun ke air.',
                    'Pilih waktu yang tepat, umumnya pagi hari ketika arus tenang dan jarak pandang jernih. Selalu gunakan sunblock ramah terumbu, jangan menyentuh karang, dan patuhi instruksi guide demi keselamatan dan kelestarian alam.',
                ],
            ],
            [
                'slug' => 'itinerary-labuan-bajo-4hari', 'title' => 'Itinerary 4 Hari di Labuan Bajo: Komodo & Padar',
                'category' => 'Itinerary', 'author' => 'Tim Namastra', 'published_at' => '2026-08-01',
                'cover' => $img('1520250497591-112f2f40a3f4'),
                'excerpt' => 'Jelajahi Taman Nasional Komodo secara maksimal dengan rencana perjalanan empat hari ini.',
                'body' => [
                    'Hari pertama: tiba di Labuan Bajo, check-in hotel, dan nikmati sunset di Bukit Cinta. Malamnya cicipi seafood segar di pusat kota sambil beristirahat menyiapkan tenaga.',
                    'Hari kedua: full day boat tour ke Pulau Padar, snorkeling di Pink Beach, lalu trekking singkat di Pulau Rinca untuk melihat komodo di habitat aslinya.',
                    'Hari ketiga: lanjut menjelajah pulau-pulau kecil, spot snorkeling Manta Point, dan Kalong Island untuk menyaksikan ribuan kelelawar terbang saat senja. Hari keempat: belanja oleh-oleh sebelum kembali ke kota asal.',
                ],
            ],
            [
                'slug' => 'checklist-dokumen-luar-negeri', 'title' => 'Checklist Dokumen Bepergian ke Luar Negeri',
                'category' => 'Tips Travel', 'author' => 'Tim Namastra', 'published_at' => '2026-05-10',
                'cover' => $img('1436491865332-7a61a109cc05'),
                'excerpt' => 'Jangan sampai keimigrasian menghambat liburan impian Anda. Siapkan dokumen-dokumen berikut.',
                'body' => [
                    'Pastikan paspor masih berlaku minimal 6 bulan sejak tanggal kepulangan, dan siapkan salinan halaman paspor dalam bentuk softcopy maupun cetak sebagai cadangan.',
                    'Periksa visa yang dibutuhkan. Beberapa negara memberikan bebas visa untuk WNI, sementara lainnya memerlukan e-visa. Cek syarat terbaru jauh-jauh hari agar tidak kaget di hari keberangkatan.',
                    'Siapkan tiket pulang-pergi, bukti reservasi hotel, dan asuransi perjalanan. Beberapa otoritas imigrasi juga meminta bukti keuangan. Simpan semua dokumen dalam satu folder digital yang mudah diakses.',
                ],
            ],
            [
                'slug' => 'kuliner-wajib-coba-turki', 'title' => '7 Kuliner Turki yang Wajib Dicoba Saat Berkunjung',
                'category' => 'Kuliner', 'author' => 'Namastra Travel', 'published_at' => '2026-08-05',
                'cover' => $img('1515003197210-e0cd71810b5f'),
                'excerpt' => 'Dari kebab hingga baklava — perjalanan kuliner ke Turki yang tak boleh Anda lewatkan.',
                'body' => [
                    'Turki adalah surga kuliner yang memadukan cita rasa Timur Tengah dan Mediterania. Pertama, jangan lewatkan Döner Kebab asli yang dipanggang perlahan dan disajikan dengan roti hangat.',
                    'Cicipi Meze — kumpulan hidangan pembuka seperti hummus, patlıcan, dan sosis sujuk. Lalu lanjutkan dengan Pide, pizza ala Turki dengan isian keju dan daging yang meleleh.',
                    'Untuk penutup, Baklava dengan lapisan filo renyah dan sirup manis adalah wajib, ditemani Turkish Tea di kedai lokal. Jangan lupa Turkish Delight sebagai oleh-oleh khas dari Grand Bazaar.',
                ],
            ],
            [
                'slug' => 'keindahan-sakura-versus-daun-merah', 'title' => 'Musim Sakura vs Daun Merah: Kapan Waktu Terbaik ke Jepang?',
                'category' => 'Tips Travel', 'author' => 'Tim Namastra', 'published_at' => '2026-07-15',
                'cover' => $img('1493976040379-85c8e12e0c0e'),
                'excerpt' => 'Dua musim paling populer di Jepang. Mana yang cocok untuk Anda?',
                'body' => [
                    'Musim sakura berlangsung sekitar akhir Maret hingga awal April, ketika pohon sakura mekar bersamaan di seluruh Jepang. Pemandangan bunga merah muda di bawah sinar matahari adalah momen yang banyak dinanti.',
                    'Musim daun merah (momiji) terjadi akhir Oktober hingga awal Desember, dengan dedaunan maple yang berubah menjadi merah dan emas. Suhu lebih sejuk dan keramaian biasanya sedikit berkurang dibanding sakura.',
                    'Jika ingin cuaca sejuk dan suasana tenang, pilih musim gugur. Namun jika ingin merasakan perayaan hanami bersama warga lokal, sakura adalah pilihan paling ikonik. Keduanya menawarkan pengalaman foto yang luar biasa.',
                ],
            ],
            [
                'slug' => 'wisata-bahari-nusantara', 'title' => '5 Destinasi Wisata Bahari Terbaik di Nusantara',
                'category' => 'Destinasi', 'author' => 'Namastra Travel', 'published_at' => '2026-06-30',
                'cover' => $img('1518548419971-58eeca3156c6f'),
                'excerpt' => 'Indonesia adalah surga bahari dunia. Inilah lima destinasi laut yang wajib masuk daftar Anda.',
                'body' => [
                    'Pertama, Raja Ampat di Papua Barat dengan karang dan ikan paling beragam di dunia. Kedua, Kepulauan Derawan di Kalimantan Timur yang menjadi rumah bagi penyu hijau.',
                    'Ketiga, Wakatobi di Sulawesi Tenggara dengan taman laut seluas wilayah daratan Pulau Jawa. Keempat, Pulau Weh di Aceh yang menawarkan diving menakjubkan dengan topografi unik.',
                    'Kelima, Labuan Bajo di NTT yang memadukan keindahan bawah laut dan ikon komodo. Semua destinasi ini bisa Anda jelajahi dengan paket tur Namastra yang sudah mencakup transportasi, penginapan, dan pemandu lokal.',
                ],
            ],
        ];

        foreach ($postData as $data) {
            $data['body'] = json_encode($data['body'], JSON_UNESCAPED_UNICODE);
            Post::create($data);
        }

        // ---- Testimonials ----
        $testimonials = [
            ['name' => 'Dinda Aurelia', 'location' => 'Jakarta', 'text' => 'Pengalaman honeymoon ke Bali luar biasa! Tim Namastra mengatur semuanya sampai detail, mulai dari hotel sampai sunset dinner. Kami tinggal menikmati momen.', 'rating' => 5, 'sort' => 1],
            ['name' => 'Budi Santoso', 'location' => 'Surabaya', 'text' => 'Tour Jepang musim gugur paling seru tahun ini! Itinerary rapi, guide ramah, dan semua spot foto bisa keburu semua. Pasti balik lagi pakai Namastra.', 'rating' => 5, 'sort' => 2],
            ['name' => 'Rina Maharani', 'location' => 'Bandung', 'text' => 'Komunikasi via WhatsApp sangat responsif, harga sesuai budget dan transparan tanpa biaya tersembunyi. Sangat recommended untuk first-timer ke luar negeri.', 'rating' => 5, 'sort' => 3],
            ['name' => 'Dewi Purnama', 'location' => 'Medan', 'text' => 'Group keluarga kami berjumlah 12 orang dan Namastra mengurus semuanya dari transportasi sampai akomodasi. Liburan jadi mulus dan berkesan.', 'rating' => 5, 'sort' => 4],
            ['name' => 'Andi Firmansyah', 'location' => 'Makassar', 'text' => 'Paket Komodo & Padar sangat worth it. Guide lokalnya paham banget soal komodo, sunset di Padar bikin merinding. Terima kasih Namastra!', 'rating' => 5, 'sort' => 5],
            ['name' => 'Salsabila Putri', 'location' => 'Jakarta', 'text' => 'Awalnya ragu tour grup, ternyata jauh lebih hemat dan menyenangkan. Banyak teman baru dan semua logistik sudah dibereskan. Gak pusing sama sekali.', 'rating' => 5, 'sort' => 6],
            ['name' => 'Hendra Wijaya', 'location' => 'Surabaya', 'text' => 'Naik balon udara di Cappadocia itu bucket list banget dan Namastra mewujudkannya. Semua lancar, hotel bagus, dan dokumentasi lengkap.', 'rating' => 5, 'sort' => 7],
            ['name' => 'Maya Kusuma', 'location' => 'Semarang', 'text' => 'Perjalanan ke Korea Seoul–Busan terorganisir rapi. Hotel dekat stasiun, street food-nya nendang, dan foto di Gamcheon jadi favorit sepanjang masa.', 'rating' => 5, 'sort' => 8],
        ];
        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }

        // ---- About ----
        AboutContent::updateOrCreate(['id' => 1], [
            'story' => [
                'Namastra Travel berdiri dengan satu misi sederhana: membuat perjalanan menjadi mudah, menyenangkan, dan terpercaya bagi setiap pelanggan. Nama kami bermakna "pemandu" yang mewakili semangat kami untuk menuntun Anda menuju pengalaman terbaik — dari domestik hingga mancanegara.',
                'Sejak berdiri, kami telah membantu ribuan keluarga, pasangan, dan kelompok untuk menjelajah berbagai destinasi. Dengan tim berpengalaman dan jaringan mitra terpercaya di dalam serta luar negeri, kami merancang perjalanan yang aman, transparan, dan penuh nilai.',
            ],
            'vision' => 'Menjadi travel partner terpercaya yang menghadirkan pengalaman wisata berkesan dan aman bagi seluruh masyarakat Indonesia, baik domestik maupun mancanegara.',
            'missions' => [
                'Menyediakan paket tour domestik & mancanegara dengan harga transparan',
                'Memberikan pelayanan personal dan responsif melalui tim berpengalaman',
                'Menjaga standar keamanan dan legalitas setiap perjalanan',
                'Terus menyeleksi destinasi yang bernilai serta ramah bagi pelanggan',
            ],
            'stats' => [
                ['value' => '200+', 'label' => 'Destinasi Dilayani'],
                ['value' => '10', 'label' => 'Tahun Pengalaman'],
                ['value' => '7.500+', 'label' => 'Pelanggan Puas'],
                ['value' => '100%', 'label' => 'Legal & Terpercaya'],
            ],
            'values' => [
                ['title' => 'Di Bantu Tim Berpengalaman', 'desc' => 'Tour operation dan pemandu dengan pengalaman bertahun-tahun di industri perjalanan.'],
                ['title' => 'Harga Transparan', 'desc' => 'Tidak ada biaya tersembunyi, semua jelas sejak awal proses booking.'],
                ['title' => 'Domestik & Mancanegara', 'desc' => 'Layanan lengkap mulai destinasi lokal hingga berbagai negara di seluruh dunia.'],
                ['title' => 'Ramah & Responsif', 'desc' => 'Setiap pertanyaan dijawab cepat melalui WhatsApp dan email.'],
            ],
        ]);

        // ---- Contoh booking & contact ----
        Booking::create([
            'tour_name' => 'Bali Ultimate Honeymoon',
            'whatsapp' => '+62 812 3456 7890',
            'email' => 'pelanggan@example.com',
            'destination' => 'Bali 5 hari 4 malam, berangkat 12 Desember, 2 peserta',
            'pax' => '2 orang',
            'planned_date' => '12 Des 2026',
            'status' => 'baru',
        ]);

        Contact::create([
            'name' => 'Contoh Pesan',
            'email' => 'pesan@example.com',
            'message' => 'Halo, apakah ada paket tour ke Jepang untuk akhir tahun? Terima kasih.',
            'status' => 'baru',
        ]);
    }
}
