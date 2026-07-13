import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
  standalone: false,
})
export class NotificationsPage implements OnInit {
  listNotifikasi: any[] = [];
  isLoading: boolean = false;
  constructor(
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.getNotificationData();
  }

  ionViewWillEnter() {
    this.getNotificationData();
  }

  getNotificationData() {
    this.isLoading = true;
    this.cdr.detectChanges();
    this.courseService.ambilDaftarNotifikasi().subscribe({
      next: (res: any) => {
        this.isLoading = false;
        console.log('Notifikasi sukses diambil:', res);
        const dataMentah = res.data ? res.data : res;

        if (Array.isArray(dataMentah)) {
          this.listNotifikasi = dataMentah.reverse();
        } else {
          this.listNotifikasi = [];
        }
        this.cdr.detectChanges();
        console.log(
          'Hasil manipulasi array setelah dibalik:',
          this.listNotifikasi
        );
      },
      error: (err: any) => {
        console.error('Gagal mengambil data notifikasi:', err);
        this.isLoading = false;
        this.cdr.detectChanges();
      },
    });
  }

  bukaPesanNotifikasi(notifId: string) {
    if (!notifId) return;

    this.courseService.tandaiNotifikasiTerbaca(notifId).subscribe({
      next: (res: any) => {
        if (res && res.status === 'success') {
          console.log('Notifikasi sukses ditandai terbaca:', notifId);

          this.getNotificationData();
        }
      },
      error: (err: any) => {
        console.error('Gagal memperbarui status terbaca notifikasi:', err);
      },
    });
  }
}
