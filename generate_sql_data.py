import random
from datetime import datetime, timedelta

# Parameter
notaris_ids = list(range(19, 10375))  # ID notaris
bulan_range = range(1, 9)  # Bulan Januari–Agustus
akta_per_bulan_min = 3
akta_per_bulan_max = 6
jenis_transaksi_list = ['Pendaftaran', 'Perubahan', 'Pembatalan', 'Penghapusan']

# Nilai penjaminan yang sudah diperbaiki
nilai_penjaminan_list = [
    "s.d. 50 juta",
    "50 juta – 100 juta",
    "100 juta – 200 juta",
    "200 juta – 500 juta",
    "500 juta – 1 Miliar",
    "1 Miliar – 100 Miliar",
    "100 M – 500 Miliar",
    "500 M – 1 Triliun",
    "> 1 Triliun"
]

rows = []
id_laporan = 1

for id_notaris in notaris_ids:
    for bulan in bulan_range:
        tahun = 2025
        jumlah_akta = random.randint(akta_per_bulan_min, akta_per_bulan_max)

        for _ in range(jumlah_akta):
            tanggal_akta = datetime(tahun, bulan, random.randint(1, 28))

            bulan_berikut = bulan + 1
            tahun_deadline = tahun
            if bulan_berikut == 13:
                bulan_berikut = 1
                tahun_deadline += 1
            deadline = datetime(tahun_deadline, bulan_berikut, 15)

            if random.random() < 0.3:
                input_date = deadline + timedelta(days=random.randint(1, 60))
                status_pelanggaran = 1
                keter_pelanggaran = (
                    f"Laporan melebihi batas waktu input. Periode laporan: {tanggal_akta.strftime('%d-%m-%Y')}, "
                    f"Maksimal: {deadline.strftime('%d-%m-%Y')}, "
                    f"Diinput: {input_date.strftime('%d-%m-%Y')}, "
                    f"Terlambat: {(input_date - deadline).days} hari."
                )
            else:
                input_date = deadline - timedelta(days=random.randint(0, 10))
                status_pelanggaran = 0
                keter_pelanggaran = ""

            pemberi = f"Pemberi {id_laporan}".replace("'", "''")
            penerima = f"Penerima {id_laporan}".replace("'", "''")
            keterangan = f"keterangan {id_laporan}".replace("'", "''")
            if keter_pelanggaran == "":
                keter_pelanggaran_sql = "NULL"
            else:
                keter_pelanggaran_sql = "'" + keter_pelanggaran.replace("'", "''") + "'"

            rows.append(
                f"({id_laporan}, {id_notaris}, 'Perjanjian Fidusia', 'fidusia', "
                f"'{random.randint(1, 200)}', '{tanggal_akta.strftime('%Y-%m-%d')}', "
                f"'{pemberi}', '{penerima}', "
                f"'XYZ.{random.randint(10000, 99999)}.AP.{bulan:02d}.{random.randint(1, 99):02d}', "
                f"'{random.choice(nilai_penjaminan_list)}', "
                f"'{random.choice(jenis_transaksi_list)}', 'Terverifikasi', "
                f"'{keterangan}', {status_pelanggaran}, "
                f"{keter_pelanggaran_sql}, "
                f"'{input_date.strftime('%Y-%m-%d %H:%M:%S')}')"
            )
            id_laporan += 1

sql_output = (
    "INSERT INTO laporan_entitas "
    "(id_laporan, id_notaris, judul_akta, tipe, nomor, tanggal, pemberi, penerima, no_sertifikat, nilai_penjaminan, "
    "jenis_transaksi, status, keterangan, status_pelanggaran, keterangan_pelanggaran, created_at) VALUES\n"
    + ",\n".join(rows)
    + ";"
)

with open("laporan_entitas_simulasi.sql", "w", encoding="utf-8") as f:
    f.write(sql_output)

print(len(rows), "data tersimpan di laporan_entitas_simulasi.sql")
