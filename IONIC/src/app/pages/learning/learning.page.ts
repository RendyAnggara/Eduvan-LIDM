import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { NavController, AlertController } from '@ionic/angular';
import { Router } from '@angular/router';
import { CourseService } from '../../services/course.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit, OnDestroy {
  activeTab: string = 'ongoing';
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  loading: boolean = false;
  isQuizModalOpen: boolean = false;
  selectedCourseTitle: string = '';
  courseLessons: any[] = [];
  loadingQuizModal: boolean = false;

  private progressSub: Subscription = new Subscription();

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private router: Router,
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  getCategoryLogo(category: string): string {
    const cat = (category || '').toLowerCase();

    if (cat.includes('computer science')) {
      return 'assets/icon/computer-science.jpeg';
    } else if (cat.includes('microsoft office')) {
      return 'assets/icon/microsoft-office.jpeg';
    } else {
      return 'assets/icon/favicon.png';
    }
  }

  ngOnInit() {
    this.loadData();

    this.progressSub = this.courseService.progressChanged$.subscribe(
      (berubah) => {
        if (berubah) {
          console.log('Sinyal diterima, menunggu sinkronisasi database...');
          setTimeout(() => {
            console.log('Menarik data segar setelah jeda...');
            this.loadData();
          }, 1000);
        }
      }
    );
  }

  ngOnDestroy() {
    if (this.progressSub) {
      this.progressSub.unsubscribe();
    }
  }

  ionViewWillEnter() {
    console.log(
      'User kembali membuka halaman My Learning, memuat data terbaru...'
    );
    this.loadData();
  }

  loadData() {
    this.loading = true;
    this.courseService.getMyEnrollments().subscribe({
      next: (res: any) => {
        this.loading = false;
        if (res && res.success && res.data) {
          this.allEnrollments = res.data
            .filter((item: any) => {
              const status = String(item.status || '')
                .toLowerCase()
                .trim();
              return (
                status === 'success' ||
                status === 'active' ||
                status === 'checking admin'
              );
            })
            .map((item: any) => {
              item.is_quiz_unlocked = true;
              return item;
            });

          console.log('Data My Learning:', this.allEnrollments);
          this.filterData();
        } else {
          this.allEnrollments = [];
          this.filteredEnrollments = [];
        }
      },
      error: (err: any) => {
        this.loading = false;
        console.error('Gagal memuat My Learning:', err);
        this.allEnrollments = [];
        this.filteredEnrollments = [];
      },
    });
  }

  segmentChanged(event: any) {
    this.activeTab = event.detail.value;
    this.filterData();
  }

  filterData() {
    this.filteredEnrollments = this.allEnrollments.filter((item) => {
      const nilaiProgress = parseInt(item.progress, 10) || 0;

      if (this.activeTab === 'ongoing') {
        return nilaiProgress < 100;
      } else {
        return nilaiProgress >= 100;
      }
    });

    console.log(
      `Data untuk tab [${this.activeTab}]:`,
      this.filteredEnrollments
    );
  }

  goToPlayer(courseId: any) {
    if (!courseId) {
      console.error('ID Kursus tidak ditemukan!');
      return;
    }
    this.navCtrl.navigateForward(['/course-player', courseId]);
  }

  openQuizModal(item: any) {
    const targetId =
      item.course_id || (item.course ? item.course.id : null) || item.id;

    if (!targetId) {
      console.error('ID Kursus murni kosong!');
      return;
    }

    this.selectedCourseTitle = item.course?.title || 'Mata Pelajaran';
    this.isQuizModalOpen = true;
    this.loadingQuizModal = true;

    this.courseService.getCourseContents(targetId).subscribe({
      next: (res: any) => {
        this.loadingQuizModal = false;
        if (res && res.data) {
          this.courseLessons = Array.isArray(res.data)
            ? res.data
            : res.data.lessons || [];
        } else {
          this.courseLessons = [];
        }
        this.cdr.detectChanges();
      },
      error: (err: any) => {
        this.loadingQuizModal = false;
        console.error('Gagal mengambil materi pertemuan:', err);
        this.courseLessons = [];
        this.cdr.detectChanges();
      },
    });
  }

  closeQuizModal() {
    this.isQuizModalOpen = false;
  }
  startQuiz(lessonId: any) {
    this.closeQuizModal();
    this.navCtrl.navigateForward(['/quiz', lessonId]);
  }

  goToSertifikat() {
    this.router.navigate(['/tabs/certificate']);
  }
}
