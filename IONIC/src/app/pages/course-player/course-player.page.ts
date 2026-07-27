import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
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
  activeLesson: any = null;
  isLoading: boolean = true; // 🟢 Tambahkan variabel penanda loading

  constructor(
    private route: ActivatedRoute,
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
    this.isLoading = true; // Set loading aktif di awal fetch

    this.courseService.getCourseContents(this.courseId).subscribe({
      next: (res: any) => {
        if (res.success && Array.isArray(res.data)) {
          this.lessons = res.data;

          if (this.lessons.length > 0) {
            this.activeLesson = this.lessons[0];
          } else {
            this.activeLesson = null;
          }
        } else {
          this.lessons = [];
          this.activeLesson = null;
        }

        this.isLoading = false; // 🛑 Matikan loading setelah data resmi dari API siap
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Gagal memuat materi:', err);
        this.lessons = [];
        this.activeLesson = null;
        this.isLoading = false; // 🛑 Matikan loading jika error
        this.cdr.detectChanges();
      },
    });
  }

  selectLesson(lesson: any) {
    this.activeLesson = lesson;
    this.cdr.detectChanges();
  }

  getSafeVideoUrl(url: string): SafeResourceUrl {
    if (!url) return '';
    if (url.includes('watch?v=')) {
      url = url.replace('watch?v=', 'embed/');
    }
    return this.sanitizer.bypassSecurityTrustResourceUrl(url);
  }
}
