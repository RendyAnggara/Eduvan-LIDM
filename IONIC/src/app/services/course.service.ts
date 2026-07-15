// src/app/services/course.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class CourseService {
  private apiUrl =
    'https://cement-drainpipe-dropbox.ngrok-free.dev/api/courses';
  private baseApiUrl = 'https://cement-drainpipe-dropbox.ngrok-free.dev/api';
  // https://eduvan.rehalivan.com/api/courses
  // https://eduvan.rehalivan.com/api

  public wishlistChanged$ = new BehaviorSubject<boolean>(false);
  public progressChanged$ = new BehaviorSubject<boolean>(false);
  public notifChanged$ = new BehaviorSubject<boolean>(false);

  constructor(private http: HttpClient) {}
  private dapatkanHeaderAutentikasi() {
    // 🟢 PASTIKAN 'token' DISINI SAMA DENGAN NAMA KEY YANG DISIMPAN PAS LOGIN!
    let tokenUser = localStorage.getItem('token');

    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    return new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'ngrok-skip-browser-warning': 'true', // 🟢 WAJIB ADA BIAR TIDAK CORS ERROR
    });
  }

  getCourses(): Observable<any> {
    return this.http.get(this.apiUrl, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  getCourseById(id: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`);
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
    const token = localStorage.getItem('token');
    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    };
    return this.http.get(`${this.baseApiUrl}/notifications`, { headers });
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
    let tokenUser = localStorage.getItem('token');
    if (!tokenUser) {
      const userDataRaw = localStorage.getItem('userData');
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
    const headers = new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
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
