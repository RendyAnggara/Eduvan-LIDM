import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CourseService } from 'src/app/services/course.service';
import { PaymentService } from 'src/app/services/payment.service';
import { ToastController, LoadingController } from '@ionic/angular';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.page.html',
  styleUrls: ['./checkout.page.scss'],
  standalone: false,
})
export class CheckoutPage implements OnInit {
  subject: any = {
    id: null,
    name: 'Memuat...',
    price: 0,
  };

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private courseService: CourseService,
    private paymentService: PaymentService,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    const subjectId = this.route.snapshot.paramMap.get('id');
    if (subjectId) {
      this.subject.id = Number(subjectId);
      this.loadCourseDetail(subjectId);
    }
  }

  onPaymentSuccess(apiResponse: any) {
    this.router.navigate(['/payment'], {
      state: { paymentData: apiResponse },
    });
  }

  loadCourseDetail(courseId: string) {
    this.courseService.getCourseById(courseId).subscribe({
      next: (res: any) => {
        const data = res.data || res;
        if (data) {
          this.subject.name =
            data.title || data.name || 'Mata Pelajaran Pilihan';
          this.subject.price = Number(data.price || 0);
        }
      },
      error: (err) => {
        console.error('Gagal memuat detail mata pelajaran:', err);
      },
    });
  }

  // async processPayment() {
  //   if (!this.subject.price || this.subject.price === 0) {
  //     this.showToast('Harga mata pelajaran tidak valid.');
  //     return;
  //   }

  //   const loading = await this.loadingCtrl.create({
  //     message: 'Menyiapkan QRIS Pembayaran...',
  //   });
  //   await loading.present();

  //   this.paymentService
  //     .checkoutSubject(this.subject.id, this.subject.price)
  //     .subscribe({
  //       next: async (res: any) => {
  //         await loading.dismiss();

  //         // Cek apakah response berhasil dan membawa data
  //         if (res.success && res.data) {
  //           // ❌ BATALKAN MEMBUKA BROWSER EXTERNAL
  //           // await Browser.open({ url: res.data.payment_url });

  //           // 🟢 GUNAKAN NAVIGASI KE PAGE PAYMENT KAMU
  //           // Pastikan res.data berisi object JSON QRIS (amount, qrData, expiresAt, dll)
  //           this.router.navigate(['/payment'], {
  //             state: { paymentData: res.data },
  //           });
  //         } else {
  //           this.showToast('Gagal memproses transaksi');
  //         }
  //       },
  //       error: async (err) => {
  //         await loading.dismiss();
  //         this.showToast(err.error?.message || 'Gagal terhubung ke server');
  //       },
  //     });
  // }

  async processPayment() {
    if (!this.subject.price || this.subject.price === 0) {
      this.showToast('Harga mata pelajaran tidak valid.');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Menyiapkan transaksi DompetX...',
    });
    await loading.present();

    this.paymentService
      .checkoutSubject(this.subject.id, this.subject.price)
      .subscribe({
        next: async (res: any) => {
          await loading.dismiss();

          // 🔍 LOG ISI ASLI RESPON BACKEND DI SINI
          console.log('=== ISI RESPONSE DARI BACKEND LARAVEL ===');
          console.log(res);
          console.log('==========================================');

          if (res && (res.success || res.status === 'success' || res.data)) {
            // Kirim data utuh 'res' atau 'res.data' ke PaymentPage
            this.router.navigate(['/payment'], {
              state: { paymentData: res },
            });
          } else {
            this.showToast('Gagal memproses transaksi');
          }
        },
        error: async (err) => {
          await loading.dismiss();
          this.showToast(err.error?.message || 'Gagal terhubung ke server');
        },
      });
  }

  async verifyPayment(referenceId: string) {
    this.paymentService.checkStatus(referenceId).subscribe({
      next: (res: any) => {
        if (res.is_paid || res.status === 'success') {
          this.showToast('Pembayaran berhasil! Kelas pilihan telah aktif.');
          this.router.navigate(['/course']);
        } else {
          this.showToast('Pembayaran belum selesai.');
        }
      },
      error: () => {
        this.showToast('Gagal memverifikasi status pembayaran.');
      },
    });
  }

  async showToast(message: string) {
    const toast = await this.toastCtrl.create({
      message,
      duration: 3000,
      position: 'bottom',
    });
    toast.present();
  }
}
