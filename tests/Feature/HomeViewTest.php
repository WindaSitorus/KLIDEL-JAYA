<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeViewTest extends TestCase
{
    /**
     * Test that the home page renders successfully with proper data.
     */
    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertViewHasAll(['user', 'nextAppointment', 'categories', 'doctors']);
        $response->assertSee('Klinik Maristella');
        $response->assertSee('Bagaimana kondisi kesehatan Anda hari ini?');
        $response->assertSee('Dr. Saran Mitchell');
        $response->assertSee('Dr. Anaya Kapoor');
        $response->assertSee('Buat Janji Temu');
        $response->assertSee('Pendaftaran Sesi Konsultasi');
        $response->assertSee('Gejala / Keluhan Utama');
        $response->assertSee('Sudah Berapa Lama Dirasakan?');
        $response->assertSee('Konfirmasi Pemesanan');
        $response->assertSee('Sebelum Periksa');
        $response->assertSee('Sesudah Periksa');
        $response->assertSee('Tagihan Pemeriksaan');
        $response->assertSee('#BILL00124');
        $response->assertSee('Rp 435.000');
        $response->assertSee('Bayar Sekarang');
        $response->assertSee('Konsultasi + Resep Obat');
        $response->assertSee('Rp 180.000');
        $response->assertSee('Pembayaran Berhasil');
        $response->assertSee('Rincian Pembayaran');
        $response->assertSee('Cetak Kuitansi');
        $response->assertSee('Kembali ke Kalender Jadwal');
        $response->assertSee('Jasa Konsultasi');
        $response->assertSee('Obat');
        $response->assertSee('Rincian Transaksi');
    }
}
