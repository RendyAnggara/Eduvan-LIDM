import { Injectable } from '@angular/core';
import {
  PushNotifications,
  Token,
  ActionPerformed,
} from '@capacitor/push-notifications';
import { HttpClient } from '@angular/common/http';
import { Capacitor } from '@capacitor/core';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class FcmService {
  constructor(private http: HttpClient) {}

  async initPush() {
    if (Capacitor.isNativePlatform()) {
      await this.registerPush();
    }
  }

  private async registerPush() {
    let permStatus = await PushNotifications.checkPermissions();

    if (permStatus.receive === 'prompt') {
      permStatus = await PushNotifications.requestPermissions();
    }

    if (permStatus.receive === 'granted') {
      await PushNotifications.register();
    } else {
      console.warn('Izin Push Notification ditolak pengguna.');
    }

    PushNotifications.addListener('registration', (token: Token) => {
      console.log('FCM Token Berhasil Didapat:', token.value);
      this.sendTokenToBackend(token.value);
    });

    PushNotifications.addListener('registrationError', (error: any) => {
      console.error('Error Registrasi FCM:', JSON.stringify(error));
    });

    PushNotifications.addListener(
      'pushNotificationReceived',
      (notification) => {
        console.log('Notifikasi Diterima (Foreground):', notification);
      }
    );

    PushNotifications.addListener(
      'pushNotificationActionPerformed',
      (notification: ActionPerformed) => {
        console.log('Notifikasi Di-klik:', notification);
      }
    );
  }

  public sendTokenToBackend(fcmToken: string) {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    const headers = { Authorization: `Bearer ${token}` };

    this.http
      .post(
        `${environment.apiUrl}/user/fcm-token`,
        { fcm_token: fcmToken },
        { headers }
      )
      .subscribe({
        next: (res) => console.log('FCM Token berhasil diperbarui di Laravel!'),
        error: (err) =>
          console.error('Gagal update FCM Token ke Laravel:', err),
      });
  }
}
