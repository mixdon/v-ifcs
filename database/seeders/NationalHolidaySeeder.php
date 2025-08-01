<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NationalHoliday;
use Carbon\Carbon;

class NationalHolidaySeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        NationalHoliday::truncate(); // Hapus data lama

        $holidays = [
            // --- Tahun 2020 ---
            ['date' => '2020-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2020-01-25', 'name' => 'Tahun Baru Imlek 2571 Kongzili', 'is_collective_leave' => false],
            ['date' => '2020-03-02', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2020-03-25', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1942', 'is_collective_leave' => false],
            ['date' => '2020-04-10', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2020-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2020-05-07', 'name' => 'Hari Raya Waisak 2564', 'is_collective_leave' => false],
            ['date' => '2020-05-21', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2020-05-24', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2020-05-25', 'name' => 'Hari Raya Idul Fitri 1441 H', 'is_collective_leave' => false],
            ['date' => '2020-05-26', 'name' => 'Hari Raya Idul Fitri 1441 H', 'is_collective_leave' => false],
            ['date' => '2020-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2020-07-31', 'name' => 'Hari Raya Idul Adha 1441 H', 'is_collective_leave' => false],
            ['date' => '2020-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2020-08-20', 'name' => 'Tahun Baru Islam 1442 H', 'is_collective_leave' => false],
            ['date' => '2020-08-21', 'name' => 'Cuti Bersama Tahun Baru Islam', 'is_collective_leave' => true],
            ['date' => '2020-10-28', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2020-10-29', 'name' => 'Cuti Bersama Maulid Nabi Muhammad SAW', 'is_collective_leave' => true],
            ['date' => '2020-10-30', 'name' => 'Cuti Bersama Maulid Nabi Muhammad SAW', 'is_collective_leave' => true],
            ['date' => '2020-12-24', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],
            ['date' => '2020-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2020-12-28', 'name' => 'Cuti Bersama Idul Fitri Pengganti', 'is_collective_leave' => true],
            ['date' => '2020-12-29', 'name' => 'Cuti Bersama Idul Fitri Pengganti', 'is_collective_leave' => true],
            ['date' => '2020-12-30', 'name' => 'Cuti Bersama Idul Fitri Pengganti', 'is_collective_leave' => true],
            ['date' => '2020-12-31', 'name' => 'Cuti Bersama Idul Fitri Pengganti', 'is_collective_leave' => true],

            // --- Tahun 2021 ---
            ['date' => '2021-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2021-02-12', 'name' => 'Tahun Baru Imlek 2572 Kongzili', 'is_collective_leave' => false],
            ['date' => '2021-03-11', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2021-03-14', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1943', 'is_collective_leave' => false],
            ['date' => '2021-04-02', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2021-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2021-05-12', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2021-05-13', 'name' => 'Hari Raya Idul Fitri 1442 H', 'is_collective_leave' => false],
            ['date' => '2021-05-14', 'name' => 'Hari Raya Idul Fitri 1442 H', 'is_collective_leave' => false],
            ['date' => '2021-05-26', 'name' => 'Hari Raya Waisak 2565', 'is_collective_leave' => false],
            ['date' => '2021-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2021-07-20', 'name' => 'Hari Raya Idul Adha 1442 H', 'is_collective_leave' => false],
            ['date' => '2021-08-10', 'name' => 'Tahun Baru Islam 1443 H', 'is_collective_leave' => false],
            ['date' => '2021-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2021-10-19', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2021-12-24', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],
            ['date' => '2021-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],

            // --- Tahun 2022 ---
            ['date' => '2022-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2022-02-01', 'name' => 'Tahun Baru Imlek 2573 Kongzili', 'is_collective_leave' => false],
            ['date' => '2022-02-28', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2022-03-03', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1944', 'is_collective_leave' => false],
            ['date' => '2022-04-15', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2022-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2022-05-02', 'name' => 'Hari Raya Idul Fitri 1443 H', 'is_collective_leave' => false],
            ['date' => '2022-05-03', 'name' => 'Hari Raya Idul Fitri 1443 H', 'is_collective_leave' => false],
            ['date' => '2022-05-04', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2022-05-05', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2022-05-16', 'name' => 'Hari Raya Waisak 2566', 'is_collective_leave' => false],
            ['date' => '2022-05-26', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2022-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2022-07-09', 'name' => 'Hari Raya Idul Adha 1443 H', 'is_collective_leave' => false],
            ['date' => '2022-07-30', 'name' => 'Tahun Baru Islam 1444 H', 'is_collective_leave' => false],
            ['date' => '2022-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2022-10-08', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2022-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2022-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2023 ---
            ['date' => '2023-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2023-01-22', 'name' => 'Tahun Baru Imlek 2574 Kongzili', 'is_collective_leave' => false],
            ['date' => '2023-01-23', 'name' => 'Cuti Bersama Imlek', 'is_collective_leave' => true],
            ['date' => '2023-02-18', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2023-03-22', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1945', 'is_collective_leave' => false],
            ['date' => '2023-03-23', 'name' => 'Cuti Bersama Nyepi', 'is_collective_leave' => true],
            ['date' => '2023-04-07', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2023-04-19', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2023-04-20', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2023-04-21', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2023-04-22', 'name' => 'Hari Raya Idul Fitri 1444 H', 'is_collective_leave' => false],
            ['date' => '2023-04-23', 'name' => 'Hari Raya Idul Fitri 1444 H', 'is_collective_leave' => false],
            ['date' => '2023-04-24', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2023-04-25', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2023-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2023-05-18', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2023-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2023-06-02', 'name' => 'Cuti Bersama Waisak', 'is_collective_leave' => true],
            ['date' => '2023-06-04', 'name' => 'Hari Raya Waisak 2567', 'is_collective_leave' => false],
            ['date' => '2023-06-28', 'name' => 'Cuti Bersama Idul Adha', 'is_collective_leave' => true],
            ['date' => '2023-06-29', 'name' => 'Hari Raya Idul Adha 1444 H', 'is_collective_leave' => false],
            ['date' => '2023-07-19', 'name' => 'Tahun Baru Islam 1445 H', 'is_collective_leave' => false],
            ['date' => '2023-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2023-09-28', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2023-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2023-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2024 ---
            ['date' => '2024-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2024-02-08', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2024-02-09', 'name' => 'Cuti Bersama Imlek', 'is_collective_leave' => true],
            ['date' => '2024-02-10', 'name' => 'Tahun Baru Imlek 2575 Kongzili', 'is_collective_leave' => false],
            ['date' => '2024-03-11', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1946', 'is_collective_leave' => false],
            ['date' => '2024-03-12', 'name' => 'Cuti Bersama Nyepi', 'is_collective_leave' => true],
            ['date' => '2024-03-29', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2024-03-31', 'name' => 'Hari Paskah', 'is_collective_leave' => false],
            ['date' => '2024-04-08', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2024-04-09', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2024-04-10', 'name' => 'Hari Raya Idul Fitri 1445 H', 'is_collective_leave' => false],
            ['date' => '2024-04-11', 'name' => 'Hari Raya Idul Fitri 1445 H', 'is_collective_leave' => false],
            ['date' => '2024-04-12', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2024-04-15', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2024-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2024-05-09', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2024-05-10', 'name' => 'Cuti Bersama Kenaikan Isa Al Masih', 'is_collective_leave' => true],
            ['date' => '2024-05-23', 'name' => 'Hari Raya Waisak 2568', 'is_collective_leave' => false],
            ['date' => '2024-05-24', 'name' => 'Cuti Bersama Waisak', 'is_collective_leave' => true],
            ['date' => '2024-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2024-06-17', 'name' => 'Hari Raya Idul Adha 1445 H', 'is_collective_leave' => false],
            ['date' => '2024-06-18', 'name' => 'Cuti Bersama Idul Adha', 'is_collective_leave' => true],
            ['date' => '2024-07-07', 'name' => 'Tahun Baru Islam 1446 H', 'is_collective_leave' => false],
            ['date' => '2024-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2024-09-16', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2024-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2024-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2025 --- (Ini adalah data yang sudah Anda miliki, saya tambahkan lagi untuk kelengkapan)
            ['date' => '2025-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2025-01-29', 'name' => 'Tahun Baru Imlek', 'is_collective_leave' => false],
            ['date' => '2025-03-29', 'name' => 'Hari Raya Nyepi', 'is_collective_leave' => false],
            ['date' => '2025-04-18', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2025-04-29', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2025-04-30', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2025-05-01', 'name' => 'Idul Fitri 1446 H', 'is_collective_leave' => false],
            ['date' => '2025-05-02', 'name' => 'Idul Fitri 1446 H', 'is_collective_leave' => false],
            ['date' => '2025-05-03', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2025-05-15', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2025-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2025-06-06', 'name' => 'Hari Raya Waisak', 'is_collective_leave' => false],
            ['date' => '2025-06-16', 'name' => 'Idul Adha 1446 H', 'is_collective_leave' => false],
            ['date' => '2025-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2025-09-06', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2025-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2025-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2026 ---
            ['date' => '2026-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2026-02-17', 'name' => 'Tahun Baru Imlek 2577 Kongzili', 'is_collective_leave' => false],
            ['date' => '2026-03-17', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1948', 'is_collective_leave' => false],
            ['date' => '2026-03-18', 'name' => 'Cuti Bersama Nyepi', 'is_collective_leave' => true],
            ['date' => '2026-03-27', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2026-04-18', 'name' => 'Hari Raya Idul Fitri 1447 H', 'is_collective_leave' => false],
            ['date' => '2026-04-19', 'name' => 'Hari Raya Idul Fitri 1447 H', 'is_collective_leave' => false],
            ['date' => '2026-04-20', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2026-04-21', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2026-05-14', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2026-05-22', 'name' => 'Hari Raya Waisak 2570', 'is_collective_leave' => false],
            ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2026-06-07', 'name' => 'Hari Raya Idul Adha 1447 H', 'is_collective_leave' => false],
            ['date' => '2026-06-08', 'name' => 'Cuti Bersama Idul Adha', 'is_collective_leave' => true],
            ['date' => '2026-06-27', 'name' => 'Tahun Baru Islam 1448 H', 'is_collective_leave' => false],
            ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2026-09-05', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2026-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2026-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2027 ---
            ['date' => '2027-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2027-02-06', 'name' => 'Tahun Baru Imlek 2578 Kongzili', 'is_collective_leave' => false],
            ['date' => '2027-03-07', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1949', 'is_collective_leave' => false],
            ['date' => '2027-03-08', 'name' => 'Cuti Bersama Nyepi', 'is_collective_leave' => true],
            ['date' => '2027-04-07', 'name' => 'Hari Raya Idul Fitri 1448 H', 'is_collective_leave' => false],
            ['date' => '2027-04-08', 'name' => 'Hari Raya Idul Fitri 1448 H', 'is_collective_leave' => false],
            ['date' => '2027-04-09', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2027-04-10', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2027-04-23', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2027-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2027-05-21', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2027-05-22', 'name' => 'Hari Raya Waisak 2571', 'is_collective_leave' => false],
            ['date' => '2027-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2027-06-27', 'name' => 'Hari Raya Idul Adha 1448 H', 'is_collective_leave' => false],
            ['date' => '2027-07-17', 'name' => 'Tahun Baru Islam 1449 H', 'is_collective_leave' => false],
            ['date' => '2027-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2027-09-04', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2027-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2027-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2028 ---
            ['date' => '2028-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2028-01-26', 'name' => 'Tahun Baru Imlek 2579 Kongzili', 'is_collective_leave' => false],
            ['date' => '2028-02-25', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2028-03-25', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1950', 'is_collective_leave' => false],
            ['date' => '2028-04-21', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2028-04-24', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2028-04-25', 'name' => 'Hari Raya Idul Fitri 1449 H', 'is_collective_leave' => false],
            ['date' => '2028-04-26', 'name' => 'Hari Raya Idul Fitri 1449 H', 'is_collective_leave' => false],
            ['date' => '2028-04-27', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2028-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2028-05-11', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2028-05-18', 'name' => 'Hari Raya Waisak 2572', 'is_collective_leave' => false],
            ['date' => '2028-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2028-06-15', 'name' => 'Hari Raya Idul Adha 1449 H', 'is_collective_leave' => false],
            ['date' => '2028-07-05', 'name' => 'Tahun Baru Islam 1450 H', 'is_collective_leave' => false],
            ['date' => '2028-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2028-09-02', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2028-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2028-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2029 ---
            ['date' => '2029-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2029-02-13', 'name' => 'Tahun Baru Imlek 2580 Kongzili', 'is_collective_leave' => false],
            ['date' => '2029-02-14', 'name' => 'Cuti Bersama Imlek', 'is_collective_leave' => true],
            ['date' => '2029-03-14', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1951', 'is_collective_leave' => false],
            ['date' => '2029-03-15', 'name' => 'Cuti Bersama Nyepi', 'is_collective_leave' => true],
            ['date' => '2029-04-10', 'name' => 'Hari Raya Idul Fitri 1450 H', 'is_collective_leave' => false],
            ['date' => '2029-04-11', 'name' => 'Hari Raya Idul Fitri 1450 H', 'is_collective_leave' => false],
            ['date' => '2029-04-12', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2029-04-13', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2029-04-20', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2029-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2029-05-24', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2029-05-25', 'name' => 'Hari Raya Waisak 2573', 'is_collective_leave' => false],
            ['date' => '2029-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2029-06-04', 'name' => 'Hari Raya Idul Adha 1450 H', 'is_collective_leave' => false],
            ['date' => '2029-06-25', 'name' => 'Tahun Baru Islam 1451 H', 'is_collective_leave' => false],
            ['date' => '2029-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2029-09-01', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2029-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2029-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],

            // --- Tahun 2030 ---
            ['date' => '2030-01-01', 'name' => 'Tahun Baru Masehi', 'is_collective_leave' => false],
            ['date' => '2030-02-02', 'name' => 'Tahun Baru Imlek 2581 Kongzili', 'is_collective_leave' => false],
            ['date' => '2030-03-03', 'name' => 'Hari Raya Nyepi Tahun Baru Saka 1952', 'is_collective_leave' => false],
            ['date' => '2030-04-01', 'name' => 'Hari Raya Idul Fitri 1451 H', 'is_collective_leave' => false],
            ['date' => '2030-04-02', 'name' => 'Hari Raya Idul Fitri 1451 H', 'is_collective_leave' => false],
            ['date' => '2030-04-03', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2030-04-04', 'name' => 'Cuti Bersama Idul Fitri', 'is_collective_leave' => true],
            ['date' => '2030-04-18', 'name' => 'Wafat Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2030-05-01', 'name' => 'Hari Buruh Internasional', 'is_collective_leave' => false],
            ['date' => '2030-05-15', 'name' => 'Kenaikan Isa Al Masih', 'is_collective_leave' => false],
            ['date' => '2030-05-22', 'name' => 'Hari Raya Waisak 2574', 'is_collective_leave' => false],
            ['date' => '2030-06-01', 'name' => 'Hari Lahir Pancasila', 'is_collective_leave' => false],
            ['date' => '2030-06-23', 'name' => 'Hari Raya Idul Adha 1451 H', 'is_collective_leave' => false],
            ['date' => '2030-07-13', 'name' => 'Tahun Baru Islam 1452 H', 'is_collective_leave' => false],
            ['date' => '2030-08-17', 'name' => 'Hari Kemerdekaan RI', 'is_collective_leave' => false],
            ['date' => '2030-08-31', 'name' => 'Maulid Nabi Muhammad SAW', 'is_collective_leave' => false],
            ['date' => '2030-12-25', 'name' => 'Hari Raya Natal', 'is_collective_leave' => false],
            ['date' => '2030-12-26', 'name' => 'Cuti Bersama Natal', 'is_collective_leave' => true],
        ];

        foreach ($holidays as $holiday) {
            NationalHoliday::create($holiday);
        }
    }
}