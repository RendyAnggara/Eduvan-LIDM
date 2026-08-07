// src/app/services/course.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class CourseService {
  private baseApiUrl = environment.apiUrl;
  private apiUrl = `${environment.apiUrl}/courses`;

  public wishlistChanged$ = new BehaviorSubject<boolean>(false);
  public progressChanged$ = new BehaviorSubject<boolean>(false);
  public notifChanged$ = new BehaviorSubject<boolean>(false);

  constructor(private http: HttpClient) {}

  private dapatkanHeaderAutentikasi(): HttpHeaders {
    // 1. Cek semua kemungkinan key token yang disimpan di LocalStorage
    let tokenUser =
      localStorage.getItem('access_token') ||
      localStorage.getItem('token') ||
      localStorage.getItem('auth_token');

    // 2. Jika tidak ditemukan langsung, cari di dalam objek JSON (user_data / user / response)
    if (!tokenUser) {
      const userDataRaw =
        localStorage.getItem('user_data') ||
        localStorage.getItem('user') ||
        localStorage.getItem('auth_response');

      if (userDataRaw) {
        try {
          const parsedData = JSON.parse(userDataRaw);
          tokenUser =
            parsedData.access_token ||
            parsedData.token ||
            parsedData.auth_token ||
            (parsedData.data && parsedData.data.access_token) ||
            null;
        } catch (e) {
          tokenUser = userDataRaw;
        }
      }
    }

    // 3. Bersihkan petik ganda atau spasi terikut
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    return new HttpHeaders({
      Authorization: tokenUser ? `Bearer ${tokenUser}` : '',
      'Content-Type': 'application/json',
      Accept: 'application/json',
    });
  }

  getCourses(): Observable<any> {
    return this.http.get(this.apiUrl, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getCourseById(id: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  buyCourse(courseId: number): Observable<any> {
    const payload = { course_id: courseId };
    return this.http.post(`${this.baseApiUrl}/enrollments`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getCourseContents(courseId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${courseId}/contents`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getMyEnrollments(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/enrollments`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  saveProgress(
    courseId: number,
    contentId: number,
    isCompleted?: number
  ): Observable<any> {
    const payload = {
      course_id: courseId,
      content_id: contentId,
      is_completed: isCompleted ?? 1,
    };
    return this.http.post(
      `${this.baseApiUrl}/contents/mark-complete`,
      payload,
      {
        headers: this.dapatkanHeaderAutentikasi(),
      }
    );
  }

  ambilDaftarNotifikasi(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/notifications`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  ambilDaftarWishlist(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/wishlist`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  toggleWishlistServer(courseId: number): Observable<any> {
    const payload = { course_id: courseId };
    return this.http.post(`${this.baseApiUrl}/wishlist/toggle`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getQuizQuestions(courseId: number): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/courses/${courseId}/quizzes`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  submitQuizAnswers(courseId: number, answers: any[]): Observable<any> {
    const payload = {
      course_id: courseId,
      answers: answers,
    };
    return this.http.post(`${this.baseApiUrl}/quiz/submit`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  updateQuizProgress(courseId: number, score: number): Observable<any> {
    const payload = {
      course_id: courseId,
      score: score,
    };
    this.progressChanged$.next(true);
    return this.http.post(`${this.baseApiUrl}/progress/submit-quiz`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getMyCertificates(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/my-certificates`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  kirimRatingCourse(courseId: number, bintang: number): Observable<any> {
    const payload = {
      rating: bintang,
    };

    return this.http.post(
      `${this.baseApiUrl}/courses/${courseId}/rate`,
      payload,
      {
        headers: this.dapatkanHeaderAutentikasi(),
      }
    );
  }

  buyCourseManual(formData: FormData): Observable<any> {
    const authHeader = this.dapatkanHeaderAutentikasi();
    // Khusus multipart/form-data: Hapus Content-Type agar browser otomatis generate boundary
    const headers = new HttpHeaders({
      Authorization: authHeader.get('Authorization') || '',
      Accept: 'application/json',
    });
    return this.http.post(`${this.baseApiUrl}/enrollments`, formData, {
      headers,
    });
  }

  getNotificationsCount(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/notifications`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  tandaiNotifikasiTerbaca(idNotif: string): Observable<any> {
    return this.http
      .post(
        `${this.baseApiUrl}/notifications/read/${idNotif}`,
        {},
        {
          headers: this.dapatkanHeaderAutentikasi(),
        }
      )
      .pipe(
        tap((res: any) => {
          if (res && res.status === 'success') {
            this.notifChanged$.next(true);
          }
        })
      );
  }
}
