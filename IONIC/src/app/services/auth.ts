import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable, tap, BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiUrl;

  private currentUserSubject = new BehaviorSubject<any>(null);
  currentUser$ = this.currentUserSubject.asObservable();

  initGoogleListener() {
    window.addEventListener(
      'message',
      (event) => {
        const origin = event.origin || '';
        if (!origin.includes('rehalivan.com')) {
          return;
        }

        const authData = event.data;
        if (authData && authData.success && authData.access_token) {
          console.log('✅ Pesan login diterima, memproses data...');
          this.handleGoogleLoginSuccess(authData);
        }
      },
      false
    );
  }

  constructor(private http: HttpClient) {
    this.initGoogleListener();

    const token = localStorage.getItem('token');
    const savedUser =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (token && savedUser) {
      this.currentUserSubject.next(JSON.parse(savedUser));
    } else {
      this.clearStorageState();
    }
  }

  triggerRefreshData(userData: any) {
    this.currentUserSubject.next(userData);
  }

  updateCurrentUserState(userData: any) {
    localStorage.setItem('user_data', JSON.stringify(userData));
    localStorage.setItem('user', JSON.stringify(userData));
    this.currentUserSubject.next(userData);
  }

  private clearStorageState() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_data');
    localStorage.removeItem('user');
    this.currentUserSubject.next(null);
  }

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((res: any) => {
        if (res?.access_token) localStorage.setItem('token', res.access_token);
        if (res?.user || res?.data)
          this.updateCurrentUserState(res.user || res.data);
      })
    );
  }

  handleGoogleLoginSuccess(res: any): boolean {
    if (res?.access_token) {
      localStorage.setItem('token', res.access_token);
    }
    if (res?.user) {
      this.updateCurrentUserState(res.user);
    }
    return !!localStorage.getItem('token');
  }

  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        if (res?.access_token || res?.token) {
          localStorage.setItem('token', res.access_token || res.token);
        }
        if (res?.user || res?.data)
          this.updateCurrentUserState(res.user || res.data);
      })
    );
  }

  sendResetOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/send-otp`, { email });
  }

  sendRegisterOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/resend-otp`, { email });
  }

  verifyResetOtp(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/verify-otp`, {
      email,
      otp,
    });
  }

  resetPassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/reset`, data);
  }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  logout() {
    this.clearStorageState();
  }

  getProfileFromServer(): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.get(`${this.apiUrl}/user`, { headers }).pipe(
      tap((res: any) => {
        if (res) {
          const profileData = res.user || res.data || res;
          // 🟢 PERBAIKAN UTAMA: Permudah validasi objek agar data murni Laravel tidak terblokir lek!
          if (profileData) {
            const currentUser = this.currentUserSubject.value || {};

            // 🔒 AMANKAN AVATAR LOKAL: Jika dari API server avatarnya kosong/null, pakai avatar lokal yang sedang aktif lek!
            if (!profileData.avatar && currentUser.avatar) {
              profileData.avatar = currentUser.avatar;
            }

            // Kawinkan data server dengan data statistik lokal yang sudah ada biar tidak hilang
            const mergedData = { ...currentUser, ...profileData };
            this.updateCurrentUserState(mergedData);
          }
        }
      })
    );
  }

  updateProfile(data: any): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.put(`${this.apiUrl}/user/update`, data, { headers }).pipe(
      tap((res: any) => {
        if (res) {
          const currentUser = this.currentUserSubject.value || {};
          const backendUser = res.user || res.data || {};

          // 🟢 PERBAIKAN UTAMA: Gabungkan secara presisi agar field enrollments_count tidak tergilas
          const updatedUser = {
            ...currentUser,
            ...backendUser,
            ...data,
          };

          // 🔒 AMANKAN AVATAR SAAT UPDATE: Pastikan path avatar baru tidak hilang tergilas objek kosong backend
          if (data.avatar) {
            updatedUser.avatar = data.avatar;
          }

          this.updateCurrentUserState(updatedUser);
        }
      })
    );
  }

  getCoursesFromServer(): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.get(`${this.apiUrl}/courses`, { headers });
  }
}
