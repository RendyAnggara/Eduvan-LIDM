import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-payment',
  templateUrl: './payment.page.html',
  styleUrls: ['./payment.page.scss'],
  standalone: false,
})
export class PaymentPage implements OnInit, OnDestroy {
  responseData: any;
  countdownText: string = 'Memuat...';
  isExpired: boolean = false;
  private timerInterval: any;

  constructor(private router: Router) {
    const navigation = this.router.getCurrentNavigation();

    // Cek semua kemungkinan key pengiriman data dari Checkout (paymentData, responseData, atau data)
    if (navigation?.extras?.state) {
      const state = navigation.extras.state;
      const rawData =
        state['paymentData'] || state['responseData'] || state['data'];

      if (rawData) {
        this.responseData = this.extractPaymentData(rawData);
      }
    }
  }

  // Helper method untuk membongkar bungkusan JSON secara otomatis
  private extractPaymentData(obj: any): any {
    if (!obj || typeof obj !== 'object') return null;

    // Utamakan objek yang memiliki struktur QR atau Payment URL
    if (
      'qrData' in obj ||
      'qrString' in obj ||
      'qrImage' in obj ||
      'payment_url' in obj
    ) {
      return obj;
    }

    // Telusuri ke dalam jika terbungkus objek lain (seperti res.data)
    for (const key of Object.keys(obj)) {
      if (typeof obj[key] === 'object' && obj[key] !== null) {
        const found = this.extractPaymentData(obj[key]);
        if (found) return found;
      }
    }

    return null;
  }

  ngOnInit() {
    console.log('Data diterima di PaymentPage:', this.responseData);

    if (this.responseData) {
      // Normalisasi expiresAt jika tidak dikirim dari Backend (Set default 1 jam dari sekarang)
      if (!this.responseData.expiresAt) {
        this.responseData.expiresAt = new Date(
          Date.now() + 60 * 60 * 1000
        ).toISOString();
      }

      console.log('Data valid, mulai timer...');
      this.startCountdown();
    } else {
      console.error('Data transaksi tidak ditemukan dari Checkout!');
      this.countdownText = 'Gagal Memuat';
    }
  }

  startCountdown() {
    const expireTime = new Date(this.responseData.expiresAt).getTime();

    this.timerInterval = setInterval(() => {
      const now = new Date().getTime();
      const distance = expireTime - now;

      if (distance <= 0) {
        clearInterval(this.timerInterval);
        this.countdownText = 'WAKTU HABIS';
        this.isExpired = true;
        return;
      }

      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
      const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;

      this.countdownText = `${formattedMinutes}:${formattedSeconds}`;
    }, 1000);
  }

  ngOnDestroy() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
    }
  }
}
