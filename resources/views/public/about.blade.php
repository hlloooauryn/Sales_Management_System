@extends('layouts.public')

@section('title', 'Tentang Kami — ' . ($siteName ?? 'Bloom Moment'))

@section('content')
<div style="background: linear-gradient(135deg, var(--color-butter-yellow-light, #FFF8E7) 0%, var(--color-sky-blue-light, #E7F3FA) 100%); padding: 54px 0; border-bottom: 1px solid var(--color-border);">
    <div class="pub-container">
        <h1 style="font-size: 36px; margin-bottom: 8px;">Tentang {{ $siteName ?? 'Bloom Moment' }}</h1>
        <p style="color: var(--color-text-muted); font-size: 16px;">
            Seni Merangkai Kisah dan Kasih Lewat Kelopak Bunga Alami Penuh Makna
        </p>
    </div>
</div>

<div class="pub-container" style="padding: 60px 24px 90px;">
    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 50px; align-items: center; margin-bottom: 60px;">
        <div>
            <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: var(--color-sage-green-hover, #728266);">Filosofi Kami</span>
            <h2 style="font-size: 32px; margin: 10px 0 18px;">Bunga Bukan Sekadar Hiasan, Tetapi Ungkapan Jiwa</h2>
            <p style="color: var(--color-text); line-height: 1.8; margin-bottom: 16px;">
                Didirikan dengan kecintaan mendalam terhadap botani dan keindahan alami, <strong>{{ $siteName ?? 'Bloom Moment' }}</strong> berawal dari sebuah studio florist berdedikasi tinggi yang menghadirkan bunga segar berkualitas prima bagi masyarakat urban.
            </p>
            <p style="color: var(--color-text); line-height: 1.8;">
                Kami percaya setiap bunga memiliki bahasa uniknya sendiri. Melalui paduan warna pastel hangat, aroma alami yang menenangkan, serta sentuhan tangan terampil florist, kami membantu Anda merayakan setiap momen berharga bersama orang-orang terkasih.
            </p>
        </div>

        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); border: 4px solid #FFFFFF;">
            <img src="https://images.unsplash.com/photo-1525310072745-f49212b5ac6d?auto=format&fit=crop&w=800&q=80" alt="Florist Bloom Moment" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>

    <!-- Visi Misi -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 60px;">
        <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 32px; margin-bottom: 12px;">🌟</div>
            <h3 style="font-size: 20px; margin-bottom: 10px;">Visi Kami</h3>
            <p style="color: var(--color-text-muted); line-height: 1.7;">
                Menjadi butik bunga terpercaya yang mengedepankan kualitas bunga segar alami berkelanjutan, estetika desain modern, dan kepuasan pelanggan di setiap rangkaian bunga yang kami ciptakan.
            </p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 32px; margin-bottom: 12px;">🎯</div>
            <h3 style="font-size: 20px; margin-bottom: 10px;">Misi Kami</h3>
            <p style="color: var(--color-text-muted); line-height: 1.7;">
                Bermitra erat dengan petani lokal nusantara, menjaga konsistensi dan kesegaran stok secara profesional melalui sistem inventaris terintegrasi, dan memberikan layanan konsultasi bunga personal yang hangat.
            </p>
        </div>
    </div>
</div>
@endsection
