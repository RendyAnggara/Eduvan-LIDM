import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PaymentService {
  private apiUrl = 'https://cement-drainpipe-dropbox.ngrok-free.dev/api';

  constructor(private http: HttpClient) {}

  private getAuthHeaders() {
    let tokenUser = localStorage.getItem('token');
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }
    return new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'ngrok-skip-browser-warning': '69420',
    });
  }

  // Request URL pembayaran DompetX dari Laravel
  checkoutSubject(
    courseId: number,
    amount: number,
    paymentMethod?: string
  ): Observable<any> {
    const payload: any = {
      course_id: courseId,
      amount: amount,
    };

    // Kirim payment_method jika diberikan
    if (paymentMethod) {
      payload.payment_method = paymentMethod;
    }

    return this.http.post(`${this.apiUrl}/payment/request`, payload, {
      headers: this.getAuthHeaders(),
    });
  }

  // Cek status transaksi
  checkStatus(orderId: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/payment/status/${orderId}`, {
      headers: this.getAuthHeaders(),
    });
  }
}
