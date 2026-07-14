import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false,
})
export class CoursePlayerPage implements OnInit {
  courseId: string | null = '';
  isCompleted: boolean = false;

  courseDetail: any = {};
  contents: any[] = [];
  videoAktifUrl: string = '';

  safeVideoUrl: SafeResourceUrl | null = null;
  loading: boolean = false;

  activeContentId: number | null = null; // Digunakan sebagai acuan materi aktif di HTML

  constructor(
    private route: ActivatedRoute,
    private toastCtrl: ToastController,
    private courseService: CourseService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
    if (this.courseId) {
      this.muatDataKelasAsli(this.courseId);
    }
  }

  muatDataKelasAsli(id: string) {
    this.loading = true;
    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.courseDetail = res.data;
        }
      },
      (err) => console.error('Gagal memuat info kelas:', err)
    );

    this.courseService.getCourseContents(Number(id)).subscribe(
      (res: any) => {
        this.loading = false;
        if (res.success) {
          this.contents = res.data || [];
          if (this.contents.length > 0) {
            this.putarMateri(this.contents[0]);
          }
        }
      },
      (err) => {
        this.loading = false;
        console.error('Materi gagal dimuat:', err);
      }
    );
  }

  putarMateri(materi: any) {
    console.log('--- DEBUG MATERI YANG DIKLIK ---');
    console.log(materi);
    this.activeContentId = materi.id;
    this.isCompleted = materi.is_completed === 1;
    this.videoAktifUrl =
      materi.content_url || materi.video_url || materi.video || '';
    console.log('Link video yang dideteksi:', this.videoAktifUrl);
    if (this.videoAktifUrl) {
      let embedUrl = this.videoAktifUrl;
      if (this.videoAktifUrl.includes('watch?v=')) {
        const videoId = this.videoAktifUrl.split('watch?v=')[1].split('&')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
      } else if (this.videoAktifUrl.includes('youtu.be/')) {
        const videoId = this.videoAktifUrl.split('youtu.be/')[1].split('?')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
      } else if (!this.videoAktifUrl.includes('embed')) {
        embedUrl = `https://www.youtube.com/embed/${this.videoAktifUrl}`;
      }

      this.safeVideoUrl =
        this.sanitizer.bypassSecurityTrustResourceUrl(embedUrl);
    } else {
      console.warn('field url videonya masih kosong nih!');
      this.safeVideoUrl = null;
    }
  }

  async markAsComplete() {
    if (!this.courseId || !this.activeContentId) {
      console.warn('ID Kursus atau ID Materi kosong!');
      return;
    }
    const statusKirim = this.isCompleted ? 0 : 1;

    this.courseService
      .saveProgress(Number(this.courseId), this.activeContentId, statusKirim)
      .subscribe(
        async (res: any) => {
          if (res.success) {
            this.isCompleted = statusKirim === 1;
            this.courseService.progressChanged$.next(true);

            const toast = await this.toastCtrl.create({
              message:
                statusKirim === 1
                  ? 'Materi berhasil diselesaikan!'
                  : 'Selesai materi dibatalkan!',
              duration: 2000,
              color: statusKirim === 1 ? 'success' : 'warning',
            });
            await toast.present();
            this.muatDataKelasAsli(this.courseId!);
          }
        },
        (err) => {
          console.error('Gagal menyimpan progress ke server Laravel:', err);
        }
      );
  }
}