import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrls: ['./course-detail.page.scss'],
  standalone: false,
})
export class CourseDetailPage implements OnInit {
  course: any = {};
  contents: any[] = [];
  paymentStatus: string = 'none';
  paymentUrl: string = '';
  isWishlist: boolean = false;
  loadingBeli: boolean = false;

  isModalRatingOpen: boolean = false;
  ratingInput: number = 5;
  isModalTransferOpen: boolean = false;
  fileGambarBukti: File | null = null;
  namaFileTerpilih: string = '';
  loadingUpload: boolean = false;
  imagePreviewUrl: string | undefined = undefined;

  isSuccessAlertOpen: boolean = false;
  isErrorAlertOpen: boolean = false;
  alertMessageCustom: string = '';

  alertSuccessButtons = [
    {
      text: 'Selesai',
      role: 'confirm',
      handler: () => {
        this.tutupAlertKustom();
      },
    },
  ];

  alertErrorButtons = [
    {
      text: 'Coba Lagi',
      role: 'cancel',
      handler: () => {
        this.tutupAlertKustom();
      },
    },
  ];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  ionViewWillEnter() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  // 1. Logika Pemisah Murni Wajib (Gratis) vs Pilihan (Berbayar)
  isFreeCourse(): boolean {
    if (!this.course || !this.course.category) {
      // Jika data course belum ter-load dari API, kembalikan false dulu agar tidak flicker/salah mode
      return false;
    }

    // Mengubah category ke huruf kecil agar pencarian kata kunci akurat
    const category = String(this.course.category).toLowerCase().trim();
    const price = Number(this.course.price || 0);

    // A. MATA PELAJARAN WAJIB (GRATIS)
    // Cek apakah kategorinya mengandung kata 'school', 'wajib', atau 'free'
    if (
      category.includes('school') ||
      category.includes('wajib') ||
      category.includes('free')
    ) {
      return true;
    }

    // B. MATA PELAJARAN PILIHAN (BERBAYAR)
    // Cek apakah kategorinya 'premium', 'pilihan', atau memiliki harga > 0
    if (
      category.includes('premium') ||
      category.includes('pilihan') ||
      price > 0
    ) {
      return false; // Berbayar!
    }

    // C. FALLBACK
    // Jika harga 0 gratis, jika harga > 0 berbayar
    return price === 0;
  }

  // 2. Perbaiki getDetail agar status pembayaran tidak saling menimpa
  getDetail(id: string) {
    const targetCourseId = Number(id);

    // Reset status payment awal ke 'none'
    this.paymentStatus = 'none';

    this.courseService.getCourseById(id).subscribe({
      next: (res: any) => {
        // Ambil data dari response API
        const data = res.data || res;
        if (data) {
          this.course = data;

          // Pastikan harga tipe number
          if (this.course.price !== undefined && this.course.price !== null) {
            this.course.price = Number(this.course.price);
          }

          console.log(
            'Detail Loaded -> Category:',
            this.course.category,
            '| Price:',
            this.course.price
          );

          this.cdr.detectChanges();
          this.cekStatusWishlistUser(targetCourseId);
          this.ambilKontenKurikulum(targetCourseId);
        }
      },
      error: (error) => {
        console.error('Gagal ambil detail:', error);
      },
    });

    // Cek pendaftaran transaksi user
    this.courseService.getMyEnrollments().subscribe({
      next: (enrollRes: any) => {
        if (enrollRes.success && enrollRes.data) {
          const riwayatBeli = enrollRes.data.find(
            (item: any) => Number(item.course_id) === targetCourseId
          );

          if (riwayatBeli) {
            this.paymentStatus = String(riwayatBeli.status)
              .trim()
              .toLowerCase();
          } else {
            this.paymentStatus = 'none';
          }
          this.cdr.detectChanges();
        }
      },
      error: () => {
        this.paymentStatus = 'none';
        this.cdr.detectChanges();
      },
    });
  }

  bukaModalUploadTransfer() {
    this.isModalTransferOpen = true;
    this.fileGambarBukti = null;
    this.namaFileTerpilih = '';
    this.imagePreviewUrl = undefined;
    this.cdr.detectChanges();
  }
  handleRefresh(event: CustomEvent) {
    console.log('User melakukan refresh halaman...');

    this.ngOnInit();

    setTimeout(() => {
      if (event && event.target) {
        (event.target as any).complete();
      }
    }, 800);
  }

  async pilihFileBuktiTransfer() {
    try {
      const image = await Camera.getPhoto({
        quality: 50,
        allowEditing: false,
        source: CameraSource.Prompt,
        resultType: CameraResultType.Uri,
        promptLabelHeader: 'Pilih Bukti Pembayaran',
        promptLabelPhoto: 'Ambil dari Galeri',
        promptLabelPicture: 'Gunakan Kamera',
      });
      this.imagePreviewUrl = image.webPath;
      this.namaFileTerpilih = `bukti_transfer_${Date.now()}.jpg`;
      const response = await fetch(image.webPath!);
      const blob = await response.blob();
      this.fileGambarBukti = new File([blob], this.namaFileTerpilih, {
        type: 'image/jpeg',
      });
      this.cdr.detectChanges();
    } catch (error) {
      console.log('User membatalkan pemilihan media.', error);
    }
  }

  kirimBuktiTransferKeServer() {
    if (!this.fileGambarBukti) {
      this.alertMessageCustom =
        'Harap pilih file gambar bukti transfer terlebih dahulu!';
      this.isErrorAlertOpen = true;
      this.cdr.detectChanges();
      return;
    }
    this.loadingUpload = true;
    this.cdr.detectChanges();

    const formData = new FormData();
    formData.append('course_id', String(this.course.id));
    formData.append('proof_of_payment', this.fileGambarBukti);
    this.courseService.buyCourseManual(formData).subscribe({
      next: (res: any) => {
        this.loadingUpload = false;
        this.isModalTransferOpen = false;

        this.alertMessageCustom =
          res.message ||
          'Bukti transfer sukses dikirim! Mohon tunggu konfirmasi Admin.';
        this.isSuccessAlertOpen = true;

        this.paymentStatus = 'pending';
        this.getDetail(String(this.course.id));
        this.cdr.detectChanges();
      },

      error: (err) => {
        this.loadingUpload = false;
        console.error('Gagal upload bukti:', err);
        this.alertMessageCustom =
          err.error?.message ||
          'Gagal mengirim bukti pembayaran, periksa format file Anda.';
        this.isErrorAlertOpen = true;
        this.cdr.detectChanges();
      },
    });
  }

  tutupAlertKustom() {
    this.isSuccessAlertOpen = false;
    this.isErrorAlertOpen = false;
    this.cdr.detectChanges();
  }

  setRatingBintang(bintang: number) {
    this.ratingInput = bintang;
    this.cdr.detectChanges();
  }

  kirimUlasanRatingLive() {
    console.log(
      `Mengirim rating bintang ${this.ratingInput} untuk course ID: ${this.course.id}`
    );
    this.courseService
      .kirimRatingCourse(this.course.id, this.ratingInput)
      .subscribe(
        (res: any) => {
          this.alertMessageCustom =
            res.message || 'Terima kasih, rating bintang berhasil disimpan.';
          this.isSuccessAlertOpen = true;
          this.isModalRatingOpen = false;
          this.getDetail(String(this.course.id));
        },

        (error: any) => {
          console.error('Gagal kirim rating:', error);
          this.alertMessageCustom =
            error.error?.message ||
            'Gagal menyimpan rating, silakan coba lagi.';
          this.isErrorAlertOpen = true;
          this.cdr.detectChanges();
        }
      );
  }

  ambilKontenKurikulum(courseId: number) {
    this.courseService.getCourseContents(courseId).subscribe(
      (res: any) => {
        if (res.success) {
          this.contents = res.data;
          this.cdr.detectChanges();
        }
      },
      (error) => {
        console.log('Materi dikunci:', error);
      }
    );
  }

  klikMateri(contentId: number) {
    if (this.paymentStatus !== 'success') {
      this.alertMessageCustom =
        'Materi ini masih terkunci! Silakan selesaikan pendaftaran dan tunggu verifikasi Admin.';
      this.isErrorAlertOpen = true;
      this.cdr.detectChanges();
    } else {
      console.log('Navigasi klikMateri bawa ID Kursus:', this.course.id);
      this.router.navigate(['/course-player', this.course.id]);
    }
  }

  masukKelas(courseId?: any) {
    const finalId =
      this.course?.id || courseId || this.route.snapshot.paramMap.get('id');
    if (this.isFreeCourse() || this.paymentStatus === 'success') {
      this.router.navigate(['/course-player', finalId]);
    } else {
      this.goToCheckout(finalId);
    }
  }

  toggleWishlist() {
    if (!this.course || !this.course.id) return;
    this.isWishlist = !this.isWishlist;
    this.courseService.toggleWishlistServer(this.course.id).subscribe(
      (res: any) => {
        this.courseService.wishlistChanged$.next(true);
        if (res.success && res.is_wishlist !== undefined) {
          this.isWishlist = res.is_wishlist;
          this.cdr.detectChanges();
        }
      },
      (error) => {
        this.isWishlist = !this.isWishlist;
        this.courseService.wishlistChanged$.next(true);
        this.cdr.detectChanges();
      }
    );
  }

  cekStatusWishlistUser(targetCourseId: number) {
    this.courseService.ambilDaftarWishlist().subscribe((res: any) => {
      if (res.success) {
        const listWishlist = res.data || [];
        this.isWishlist = listWishlist.some(
          (item: any) => Number(item.course_id) === targetCourseId
        );
        this.cdr.detectChanges();
      }
    });
  }

  goToCheckout(subjectId: number) {
    this.router.navigate(['/checkout', subjectId]);
  }
}
