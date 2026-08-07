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

  private get defaultHeaders(): HttpHeaders {
    return new HttpHeaders({
      'Content-Type': 'application/json',
      Accept: 'application/json',
    });
  }
  private get authHeaders(): HttpHeaders {
    return new HttpHeaders({
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: `Bearer ${localStorage.getItem('token') || ''}`,
    });
  }

  initGoogleListener() {
    window.addEventListener(
      'message',
      (event) => {
        const origin = event.origin || '';
        if (
          !origin.includes('edulearn.rehalivan.com')
        ) {
          return;
        }

        const authData = event.data;
        if (authData && authData.success && authData.access_token) {
          console.log('Pesan login Google diterima...');
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
      try {
        this.currentUserSubject.next(JSON.parse(savedUser));
      } catch (e) {
        this.clearStorageState();
      }
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
    return this.http
      .post(`${this.apiUrl}/login`, data, { headers: this.defaultHeaders })
      .pipe(
        tap((res: any) => {
          if (res?.access_token)
            localStorage.setItem('token', res.access_token);
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
    return this.http
      .post(
        `${this.apiUrl}/verify-otp`,
        { email, otp },
        { headers: this.defaultHeaders }
      )
      .pipe(
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
    return this.http.post(
      `${this.apiUrl}/forgot-password/send-otp`,
      { email },
      { headers: this.defaultHeaders }
    );
  }

  sendRegisterOtp(email: string): Observable<any> {
    return this.http.post(
      `${this.apiUrl}/resend-otp`,
      { email },
      { headers: this.defaultHeaders }
    );
  }

  verifyResetOtp(email: string, otp: string): Observable<any> {
    return this.http.post(
      `${this.apiUrl}/forgot-password/verify-otp`,
      {
        email,
        otp,
      },
      { headers: this.defaultHeaders }
    );
  }

  resetPassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/reset`, data, {
      headers: this.defaultHeaders,
    });
  }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data, {
      headers: this.defaultHeaders,
    });
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  logout() {
    this.clearStorageState();
  }

  getProfileFromServer(): Observable<any> {
    return this.http
      .get(`${this.apiUrl}/user`, { headers: this.authHeaders })
      .pipe(
        tap((res: any) => {
          if (res) {
            const profileData = res.user || res.data || res;
            if (profileData) {
              const currentUser = this.currentUserSubject.value || {};
              if (
                profileData.avatar &&
                profileData.avatar.startsWith('http') &&
                currentUser.avatar &&
                !currentUser.avatar.startsWith('http')
              ) {
                profileData.avatar = currentUser.avatar;
              } else if (!profileData.avatar && currentUser.avatar) {
                profileData.avatar = currentUser.avatar;
              }
              const mergedData = { ...currentUser, ...profileData };
              this.updateCurrentUserState(mergedData);
            }
          }
        })
      );
  }

  updateProfile(data: any): Observable<any> {
    return this.http
      .put(`${this.apiUrl}/user/update`, data, { headers: this.authHeaders })
      .pipe(
        tap((res: any) => {
          if (res) {
            const currentUser = this.currentUserSubject.value || {};
            const backendUser = res.user || res.data || {};
            const updatedUser = {
              ...currentUser,
              ...backendUser,
              ...data,
            };
            if (data.avatar) {
              updatedUser.avatar = data.avatar;
            }

            this.updateCurrentUserState(updatedUser);
          }
        })
      );
  }

  getCoursesFromServer(): Observable<any> {
    return this.http.get(`${this.apiUrl}/courses`, {
      headers: this.authHeaders,
    });
  }
}
