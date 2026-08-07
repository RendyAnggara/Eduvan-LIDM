import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class PaymentService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  private getAuthHeaders(): HttpHeaders {
    let tokenUser = localStorage.getItem('token');

    if (!tokenUser) {
      const userDataRaw =
        localStorage.getItem('user_data') || localStorage.getItem('user');
      if (userDataRaw) {
        try {
          const parsedData = JSON.parse(userDataRaw);
          tokenUser = parsedData.token || parsedData.access_token || null;
        } catch (e) {
          tokenUser = userDataRaw;
        }
      }
    }

    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    return new HttpHeaders({
      Authorization: `Bearer ${tokenUser || ''}`,
      'Content-Type': 'application/json',
      Accept: 'application/json',
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
