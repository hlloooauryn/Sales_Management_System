@extends('layouts.public')

@section('title', 'Hubungi Kami — ' . ($siteName ?? 'Bloom Moment'))

@section('content')
<div style="background: linear-gradient(135deg, var(--color-butter-yellow-light, #FFF8E7) 0%, var(--color-sky-blue-light, #E7F3FA) 100%); padding: 54px 0; border-bottom: 1px solid var(--color-border);">
    <div class="pub-container">
        <h1 style="font-size: 36px; margin-bottom: 8px;">Hubungi Florist Kami</h1>
        <p style="color: var(--color-text-muted); font-size: 16px;">
            Punya permintaan custom buket, dekorasi acara, atau pertanyaan tentang stok bunga? Kami siap membantu!
        </p>
    </div>
</div>

<div class="pub-container" style="padding: 60px 24px 90px;">
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 50px;">
        
        <!-- Contact Information -->
        <div>
            <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--color-sage-green-hover, #728266);">Informasi Butik</span>
            <h2 style="font-size: 28px; margin: 8px 0 20px;">Kunjungi Studio Bunga Kami</h2>

            <div style="margin-bottom: 24px;">
                <div style="font-weight: 700; color: var(--color-dark-brown); font-size: 15px; margin-bottom: 4px;">📍 Lokasi Butik:</div>
                <p style="color: var(--color-text-muted); line-height: 1.6;">
                    {{ $siteAddress ?? 'Jl. Cendana No. 18, RT 04 / RW 02, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12160' }}
                </p>
            </div>

            <div style="margin-bottom: 24px;">
                <div style="font-weight: 700; color: var(--color-dark-brown); font-size: 15px; margin-bottom: 4px;">⏰ Jam Buka Operasional:</div>
                <p style="color: var(--color-text-muted); line-height: 1.6;">
                    Senin – Sabtu: 08.00 – 21.00 WIB<br>
                    Minggu & Hari Libur: 09.00 – 19.00 WIB
                </p>
            </div>

            <div style="margin-bottom: 24px;">
                <div style="font-weight: 700; color: var(--color-dark-brown); font-size: 15px; margin-bottom: 4px;">💬 Kontak Langsung:</div>
                <p style="color: var(--color-text-muted); line-height: 1.6;">
                    WhatsApp: <strong>{{ $sitePhone ?? '+62 812-9988-7766' }}</strong><br>
                    Telepon: (021) 720-9876<br>
                    Email: hello@bloommoment.com
                </p>
            </div>

            <div style="margin-top: 30px;">
                <a href="https://wa.me/6281299887766?text=Halo%20Bloom%20Moment,%20saya%20ingin%20berkonsultasi%20mengenai%20pesanan%20bunga." target="_blank" class="btn btn-primary" style="padding: 12px 24px;">
                    📱 Chat WhatsApp Sekarang
                </a>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 22px; margin-bottom: 6px;">Kirimkan Pesan Anda</h3>
            <p style="color: var(--color-text-muted); font-size: 13.5px; margin-bottom: 24px;">
                Isi formulir di bawah ini untuk konsultasi rangkaian kustom atau saran kepada tim florist kami.
            </p>

            <form onsubmit="event.preventDefault(); alert('Terima kasih! Pesan Anda telah kami terima. Florist kami akan segera menghubungi Anda.'); this.reset();">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" class="form-control" placeholder="Nama Anda" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email / Nomor WhatsApp <span class="req">*</span></label>
                    <input type="text" class="form-control" placeholder="0812... atau email@domain.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Keperluan Rangkaian</label>
                    <select class="form-control">
                        <option>Buket Ulang Tahun / Wisuda</option>
                        <option>Bunga Meja & Pemanis Ruangan</option>
                        <option>Bunga Papan / Standing Flowers Duka / Selamat</option>
                        <option>Konsultasi Bunga Pernikahan (Wedding)</option>
                        <option>Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pesan / Catatan Khusus <span class="req">*</span></label>
                    <textarea class="form-control" rows="4" placeholder="Jelaskan kebutuhan, preferensi bunga, atau tanggal acara..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    Kirim Pesan ke Florist
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
