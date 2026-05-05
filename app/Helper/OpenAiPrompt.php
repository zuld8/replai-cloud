<?php

if (! function_exists('prompt_detect_intent')) {
  /**
   * Set the active class to the current opened menu.
   *
   * @param  string|array $route
   * @param  string       $className
   * @return string
   */
  function prompt_detect_intent()
  {
    return "
Coba deteksi intent dengan ketentuan di bawah ini.

Tugas Anda:
1. Baca pesan terbaru pengguna beserta konteks percakapan sebelumnya (jika ada).
2. Tentukan intent utama dari pesan terbaru berdasarkan definisi berikut:
 

- 'media'  
  Pesan berisi atau meminta gambar/media.  
  Masukkan semua URL gambar yang ditemukan ke 'medias' dan pesan singkat ke 'message'.
  Jika tidak ada, balas jika tidak ada gambar yang dapat dikirim.

- 'check_shipping'
  HANYA gunakan jika pesan EKSPLISIT menyebutkan salah satu dari:
  • Ongkir / ongkos kirim / biaya kirim / cek ongkir / hitung ongkir
  • Kurir / ekspedisi (JNE, JNT, SiCepat, Ninja, dll)
  • Pengiriman barang / kirim barang / estimasi sampai / lama pengiriman
  
  PENTING - JANGAN gunakan 'check_shipping' untuk:
  ✗ Pertanyaan alamat toko/kantor/lokasi (gunakan 'question')
  ✗ Pertanyaan alamat tanpa menyebut pengiriman (gunakan 'question')
  ✗ Permintaan informasi lokasi (gunakan 'question')
  ✗ Tanya alamat untuk keperluan lain selain pengiriman (gunakan 'question')
  
  Contoh check_shipping yang BENAR:
  ✓ \"Berapa ongkir ke Jakarta?\"
  ✓ \"Cek ongkos kirim dong\"
  ✓ \"Bisa kirim pakai JNE ga?\"
  ✓ \"Biaya kirim ke Bandung berapa?\"
  
  Contoh yang BUKAN check_shipping:
  ✗ \"Alamat tokonya dimana?\" → gunakan 'question'
  ✗ \"Lokasi kantornya?\" → gunakan 'question'
  ✗ \"Alamat lengkapnya apa?\" → gunakan 'question'
  ✗ \"Dimana alamat cabangnya?\" → gunakan 'question'
  
  Aturan ketat untuk check_shipping:
    1. Jika alamat belum diketahui → 'address' = null dan 'message' = permintaan alamat lengkap.
    2. Jika kode pos belum diketahui → 'pos_code' = null dan 'message' = permintaan kode pos.
    3. Jika alamat & kode pos lengkap → isi 'address' dan 'pos_code'.
    4. Tidak boleh ada teks naratif atau sapaan di 'message', kecuali permintaan data yang dimaksud (address, pos_code).
    5. 'quantity' default 1 jika tidak disebutkan.
    6. Jangan menghitung ongkir.

- 'question'  
  Pertanyaan umum yang tidak masuk ke kategori di atas, termasuk:
  • Pertanyaan tentang alamat/lokasi toko/kantor/cabang
  • Informasi produk/layanan
  • Pertanyaan umum lainnya
  Kamu langsung isi balasannya ke **message**

Aturan tambahan:
- Jangan mengarang data training yang tidak ada.
- Selalu isi 'intent'.
- Jika tidak ada data untuk 'medias', kirimkan array kosong.
- Jawaban hanya dalam format JSON sesuai schema.
- SANGAT PENTING: Kata 'alamat' saja TIDAK otomatis berarti check_shipping!
";
  }

  function question_optimize($query)
  {
    return "Kamu adalah asisten yang membantu memperbaiki dan memperluas query pencarian.

Pertanyaan asli: '{$query}'

Tugas kamu:
1. Perbaiki typo dan kesalahan pengetikan
2. Buat 2 variasi pertanyaan dengan kata-kata berbeda tapi makna sama seperti transfer, bisa juga bermakna pembayaran, atau bisa juga cara bisa juga tutorial

Contoh:
Query: 'ransfer kmna?'
Output:
1. transfer kemana
2. metode pembayaran
3. cara bayar

Query: 'brp hrga laptop?'
Output:
1. berapa harga laptop
2. harga laptop berapa
3. laptop harganya berapa

Sekarang buat untuk query di atas. Berikan HANYA 3 variasi, satu per baris, tanpa numbering.";
  }
}