import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false,
})
export class CoursePlayerPage implements OnInit {
  courseId: any;
  lessons: any[] = [];
  quizzes: any[] = [];
  activeLesson: any = null;
  isLoading: boolean = true;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private courseService: CourseService,
    private sanitizer: DomSanitizer,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
    if (this.courseId) {
      this.loadLessons();
    }
  }

  loadLessons() {
    this.isLoading = true;

    this.courseService.getCourseContents(this.courseId).subscribe({
      next: (res: any) => {
        if (res.success && res.data) {
          if (res.data.lessons) {
            this.lessons = res.data.lessons;
            this.quizzes = res.data.quizzes || [];
          } else if (Array.isArray(res.data)) {
            this.lessons = res.data;
          }

          if (this.lessons.length > 0) {
            this.activeLesson = this.lessons[0];
          } else {
            this.activeLesson = null;
          }
        } else {
          this.lessons = [];
          this.quizzes = [];
          this.activeLesson = null;
        }

        this.isLoading = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Gagal memuat materi:', err);
        this.lessons = [];
        this.quizzes = [];
        this.activeLesson = null;
        this.isLoading = false;
        this.cdr.detectChanges();
      },
    });
  }

  selectLesson(lesson: any) {
    this.activeLesson = lesson;
    this.cdr.detectChanges();
  }

  openQuiz(quizId: any) {
    console.log('Membuka Kuis ID:', quizId);
    this.router.navigate(['/quiz', quizId]);
  }

  isDocumentUrl(text: string): boolean {
    if (!text) return false;
    const str = text.trim().toLowerCase();
    return str.startsWith('http://') || str.startsWith('https://');
  }

  getSafeDocumentUrl(url: string): SafeResourceUrl {
    if (!url) return '';
    const embedUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(
      url
    )}&embedded=true`;
    return this.sanitizer.bypassSecurityTrustResourceUrl(embedUrl);
  }

  getSafeVideoUrl(url: string): SafeResourceUrl {
    if (!url) return '';

    let videoId = '';

    if (url.includes('youtu.be/')) {
      const parts = url.split('youtu.be/');
      if (parts[1]) {
        videoId = parts[1].split('?')[0].split('&')[0];
      }
    } else if (url.includes('watch?v=')) {
      const parts = url.split('watch?v=');
      if (parts[1]) {
        videoId = parts[1].split('&')[0];
      }
    } else if (url.includes('embed/')) {
      const parts = url.split('embed/');
      if (parts[1]) {
        videoId = parts[1].split('?')[0];
      }
    }

    if (videoId) {
      const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=0&rel=0`;
      return this.sanitizer.bypassSecurityTrustResourceUrl(embedUrl);
    }

    return this.sanitizer.bypassSecurityTrustResourceUrl(url);
  }
}
