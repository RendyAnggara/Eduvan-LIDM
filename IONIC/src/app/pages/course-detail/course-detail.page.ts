import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router'; // Import Router buat pindah halaman belajar
import { CourseService } from '../../services/course.service';
import { Browser } from '@capacitor/browser'; // Import Capacitor Browser buat buka invoice Xendit
import { AlertController } from '@ionic/angular'; // 🟢 TAMBAHAN SAKTI: Import Alert buat pop-up bintang rating

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrls: ['./course-detail.page.scss'],
  standalone: false,
})
export class CourseDetailPage implements OnInit {
  course: any = {};
  contents: any[] = []; // Wadah untuk menampung data kurikulum asli dari API

  // Variabel untuk mengontrol UI dinamis di HTML
  paymentStatus: string = 'none'; // nilainya bisa: 'none', 'pending', 'success'
  paymentUrl: string = ''; // Menyimpan link invoice Xendit dari database
  isWishlist: boolean = false; // Status wishlist aktif/tidak
  loadingBeli: boolean = false; // State loading biar tombol ga di-spam pas loading invoice

  constructor(
    private route: ActivatedRoute,
    private router: Router, // Inject Router
    private courseService: CourseService,
    private alertController: AlertController,
    private cdr: ChangeDetectorRef // 
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  // LIFECYCLE IONIC: Memastikan status di-refresh murni setiap kali user bolak-balik page
  ionViewWillEnter() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  getDetail(id: string) {
    // AKURAT: Kunci ID dari parameter URL sejak awal agar terhindar dari race condition 'undefined'
    const targetCourseId = Number(id);

    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.course = res.data;
          console.log('Detail Kursus:', this.course);

          this.cdr.detectChanges();
          // SINKRONISASI MANTEP: Jalankan pengecekan wishlist murni pakai ID URL yang udah pasti valid di sini
          this.cekStatusWishlistUser(targetCourseId);

          // Ambal data kurikulum materi pembelajaran
          this.ambilKontenKurikulum(targetCourseId);

          // Ambil status pendaftaran menggunakan targetCourseId dari URL yang sudah pasti ada
          this.courseService.getMyEnrollments().subscribe(
            (enrollRes: any) => {
              if (enrollRes.success && enrollRes.data) {
                const riwayatBeli = enrollRes.data.find(
                  (item: any) => Number(item.course_id) === targetCourseId,
                );

                if (riwayatBeli) {
                  this.paymentStatus = String(riwayatBeli.status)
                    .trim()
                    .toLowerCase();
                  this.paymentUrl = riwayatBeli.payment_url;
                  console.log(
                    'Status Pembayaran Ditemukan:',
                    this.paymentStatus,
                  );
                } else {
                  this.paymentStatus = 'none';
                }
              }
            },
            (enrollError) => {
              console.log(
                'API Enrollments merespons error. Menerapkan fallback sukses...',
              );
              // BYPASS FALLBACK: Jika Laravel melempar eror status 400 karena relasi sudah ada
              if (enrollError.status === 400) {
                this.paymentStatus = 'success';
              }
            },
          );
        }
      },
      (error) => {
        console.error('Gagal ambil detail:', error);
      },
    );
  }

  // 🟢 TAMBAHAN SAKTI: Fungsi Pop-up Radio Button untuk memilih Bintang 1-5
  async bukaPopUpRating() {
    const alertRating = await this.alertController.create({
      header: 'Beri Rating Kelas',
      subHeader: 'Pilih bintang ulasan',
      buttons: [
        {
          text: 'Batal',
          role: 'cancel',
        },
        {
          text: 'Kirim Bintang',
          handler: (dataBintang) => {
            if (!dataBintang) {
              alert('Berikan Ratting');
              return false;
            }
            // Kirim nilai bintang terpilih ke backend cPanel
            this.kirimRatingKeLaravel(dataBintang);
            return true;
          },
        },
      ],
      inputs: [
        {
          type: 'radio',
          label: '⭐⭐⭐⭐⭐ (5)',
          value: '5',
          checked: true,
        },
        { type: 'radio', label: '⭐⭐⭐⭐ (4)', value: '4' },
        { type: 'radio', label: '⭐⭐⭐ (3)', value: '3' },
        { type: 'radio', label: '⭐⭐ (2)', value: '2' },
        { type: 'radio', label: '⭐ (1)', value: '1' },
      ],
    });

    await alertRating.present();
  }

  kirimRatingKeLaravel(bintang: string) {
    console.log(
      `Mengirim rating bintang ${bintang} untuk course ID: ${this.course.id}`,
    );

    this.courseService
      .kirimRatingCourse(this.course.id, Number(bintang))
      .subscribe(
        (res: any) => {
          alert(res.message || 'Rating berhasil disimpan.');
          this.getDetail(String(this.course.id));
        },
        (error: any) => {
          // 🌟 Di sini ditambahin : any
          console.error('Gagal kirim rating:', error);
          alert(
            error.error?.message || 'Gagal menyimpan ratting',
          );
        },
      );
  }

  // Fungsi untuk mengambil data kurikulum asli lewat API backend
  ambilKontenKurikulum(courseId: number) {
    this.courseService.getCourseContents(courseId).subscribe(
      (res: any) => {
        if (res.success) {
          this.contents = res.data; // Array konten masuk ke variabel
          console.log('Konten Kurikulum Asli:', this.contents);
        }
      },
      (error) => {
        console.log(
          'Materi dikunci karena belum bayar / status pending:',
          error,
        );
      },
    );
  }

  // Fungsi mencocokkan apakah user sudah beli kursus ini atau belum
  cekStatusPembayaranUser() {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;

    const targetCourseId = Number(id);

    this.courseService.getMyEnrollments().subscribe(
      (res: any) => {
        if (res.success && res.data) {
          const riwayatBeli = res.data.find(
            (item: any) => Number(item.course_id) === targetCourseId,
          );

          if (riwayatBeli) {
            this.paymentStatus = String(riwayatBeli.status)
              .trim()
              .toLowerCase();
            this.paymentUrl = riwayatBeli.payment_url;
            console.log(
              'Status Pembayaran Terdeteksi Sah:',
              this.paymentStatus,
            );
          } else {
            this.paymentStatus = 'none';
          }
        }
      },
      (error) => {
        if (error.status === 400) {
          this.paymentStatus = 'success';
        }
      },
    );
  }

  // PERBAIKAN UTAMA: Terima targetCourseId langsung dari pemanggil agar data sinkron peluru
  cekStatusWishlistUser(targetCourseId: number) {
    this.courseService.ambilDaftarWishlist().subscribe(
      (res: any) => {
        if (res.success) {
          const listWishlist = res.data || [];

          // COCOKKAN MENGGUNAKAN ID YANG DI-PASSING SEJAK AWAL SIKLUS DETAIL
          this.isWishlist = listWishlist.some(
            (item: any) => Number(item.course_id) === targetCourseId,
          );

          console.log(
            '📌 Hasil sinkronisasi akhir jantung detail (ID ' +
              targetCourseId +
              '):',
            this.isWishlist,
          );
        }
      },
      (err) => {
        console.error('❌ Gagal sinkronisasi status wishlist di detail:', err);
      },
    );
  }

  // LOGIKA UTAMA TOMBOL DAFTAR (MAKIN KICKASS)
  enroll() {
    console.log('User menekan tombol enroll untuk kursus:', this.course.title);
    this.loadingBeli = true; // Aktifkan efek loading

    this.courseService.buyCourse(this.course.id).subscribe(
      async (res: any) => {
        this.loadingBeli = false; // Matikan loading jika sukses
        if (res.success) {
          if (res.data.payment_url) {
            this.paymentStatus = 'pending';
            this.paymentUrl = res.data.payment_url;

            alert(
              'Invoice Xendit berhasil dibuat, membuka halaman pembayaran...',
            );

            try {
              await Browser.open({
                url: res.data.payment_url,
                windowName: '_blank',
              });
            } catch (browserError) {
              console.log(
                'Capacitor Browser mentok di localhost laptop, mengalihkan ke window.open...',
              );
              window.open(res.data.payment_url, '_blank');
            }

            this.getDetail(String(this.course.id));
          } else {
            this.paymentStatus = 'success';
            alert('Berhasil mendaftar kursus gratis!');
            this.getDetail(String(this.course.id));
            this.masukKelas();
          }
        }
      },
      (error) => {
        this.loadingBeli = false; // Matikan loading jika eror
        console.error('Gagal melakukan pendaftaran:', error);

        if (
          error.status === 400 &&
          (error.error?.message?.includes('sudah') ||
            error.error?.message?.includes('bought') ||
            error.error?.message?.includes('success'))
        ) {
          this.paymentStatus = 'success'; // Paksa UI lokal berubah jadi sukses lunas
          this.ambilKontenKurikulum(this.course.id); // Tarik ulang modul biar gembok kebuka
          alert('Anda sudah terdaftar di kursus ini. Selamat belajar!');
        } else if (
          error.status === 400 ||
          error.error?.message?.includes('pembayaran')
        ) {
          alert(
            error.error?.message ||
              'Silahkan selesaikan pembayaran yang sudah ada.',
          );
        } else {
          alert(error.error?.message || 'Gagal mendaftar kursus.');
        }
      },
    );
  }

  // Fungsi tombol "Bayar Sekarang" jika status transaksi masih pending
  async bukaInvoiceXendit() {
    if (this.paymentUrl) {
      console.log('Membuka ulang link invoice Xendit:', this.paymentUrl);
      try {
        await Browser.open({ url: this.paymentUrl, windowName: '_blank' });
      } catch (e) {
        console.log('Capacitor mentok di laptop, melempar via window.open...');
        window.open(this.paymentUrl, '_blank');
      }
    } else {
      alert('Link pembayaran tidak ditemukan, silahkan hubungi admin.');
    }
  }

  // Fungsi ketika list materi diklik (Validasi gembok)
  klikMateri(contentId: number) {
    if (this.paymentStatus !== 'success') {
      alert(
        'Materi ini masih terkunci! Silakan selesaikan pembayaran terlebih dahulu.',
      );
      if (this.paymentStatus === 'pending') {
        this.bukaInvoiceXendit(); // Jika pending, tawarin buka link Xendit lagi
      }
    } else {
      console.log('Membuka materi ID:', contentId);
      this.router.navigate([`/course/${this.course.id}/watch/${contentId}`]);
    }
  }

  // Fungsi tombol "Mulai Belajar" jika status sudah sukses lunas
  masukKelas() {
    console.log('User masuk ke ruang belajar kursus:', this.course.id);
    this.router.navigate(['/tabs/my-learning']);
  }

  // LOGIKA KLIK TOMBOL JANTUNG (SINKRONISASI INSTAN TANPA DELAY)
  toggleWishlist() {
    console.log(
      '🔥 TOMBOL WISHLIST BERHASIL DI-KLIK! ID COURSE:',
      this.course?.id,
    );

    if (!this.course || !this.course.id) {
      console.error('❌ Error: Data course belum dimuat sepenuhnya oleh API!');
      return;
    }

    // Ubah warna UI lokal secara instan dulu agar user melihat perubahan langsung
    this.isWishlist = !this.isWishlist;
    console.log('Ubah warna jantung lokal menjadi:', this.isWishlist);

    // Baru kirim data ke live server Laravel
    this.courseService.toggleWishlistServer(this.course.id).subscribe(
      (res: any) => {
        console.log('✅ Response sukses dari Laravel:', res);
        this.courseService.wishlistChanged$.next(true);

        if (res.success && res.is_wishlist !== undefined) {
          this.isWishlist = res.is_wishlist;
        }
      },
      (error) => {
        console.error(
          '❌ Gagal kirim ke endpoint Laravel, mengembalikan warna:',
          error,
        );
        this.isWishlist = !this.isWishlist;
        this.courseService.wishlistChanged$.next(true);
      },
    );
  }
}
