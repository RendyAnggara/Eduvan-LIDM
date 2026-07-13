import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-riwayat-transaksi',
  templateUrl: './riwayat-transaksi.page.html',
  styleUrls: ['./riwayat-transaksi.page.scss'],
  standalone: false,
})
export class RiwayatTransaksiPage implements OnInit {
  listTransaksi: any[] = [];
  isLoading: boolean = false;

  constructor(
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit() {
  }
  ionViewWillEnter() {
    this.ambilRiwayatTransaksiStudent();
  }

  ambilRiwayatTransaksiStudent() {
    this.isLoading = true;
    this.cdr.detectChanges();

    this.courseService.getMyEnrollments().subscribe({
      next: (res: any) => {
        this.isLoading = false;
        console.log('Isi mentah data riwayat transaksi:', res);
        const dataMentah = res.data ? res.data : res;

        if (Array.isArray(dataMentah)) {
          this.listTransaksi = dataMentah;
        }
        
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.isLoading = false;
        console.error('Gagal mengambil riwayat transaksi di frontend:', err);
        this.cdr.detectChanges();
      }
    });
  }
}