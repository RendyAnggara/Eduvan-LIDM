import { Component, OnInit } from '@angular/core';
import { CourseService } from 'src/app/services/course.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LoadingController, ToastController } from '@ionic/angular';
import { Browser } from '@capacitor/browser';

@Component({
  selector: 'app-certificate',
  templateUrl: './certificate.page.html',
  styleUrls: ['./certificate.page.scss'],
  standalone: false,
})
export class CertificatePage implements OnInit {
  listSertifikat: any[] = [];
  isLoading: boolean = false;
  isDownloading: boolean = false;
  activeCertId: number | null = null;

  constructor(
    private courseService: CourseService,
    private http: HttpClient,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController,
  ) {}

  ngOnInit() {
    this.muatDaftarSertifikat();
  }

  ionViewWillEnter() {
    this.muatDaftarSertifikat();
  }

  muatDaftarSertifikat() {
    this.isLoading = true;
    this.courseService.getMyCertificates().subscribe({
      next: (res: any) => {

        this.listSertifikat = res.data || [];
        this.isLoading = false;
        console.log('Sertifikat kamu sukses dimuat:', this.listSertifikat);
      },
      error: (err: any) => {
        console.error('Gagal mengambil data sertifikat dari server:', err);
        this.isLoading = false;
      },
    });
  }

  async downloadPdf(idSertifikat: number, namaKursus: string) {
    this.isDownloading = true;
    this.activeCertId = idSertifikat;

    const loadingSertifikat = await this.loadingCtrl.create({
      message: 'Sedang memproses sertifikat...',
      spinner: 'crescent',
      backdropDismiss: false,
    });
    await loadingSertifikat.present();

    let tokenUser = localStorage.getItem('token');
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    const headers = new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
    });

    const urlApiDownload = `https://eduvan.rehalivan.com/api/certificates/${idSertifikat}/download`;


    this.http.get(urlApiDownload, { headers, responseType: 'blob' }).subscribe({
      next: async (blobData: Blob) => {
        const pembacaFile = new FileReader();
        pembacaFile.readAsDataURL(blobData);
        pembacaFile.onloadend = () => {
          const base64Data = pembacaFile.result as string;
          const linkLokal = document.createElement('a');
          linkLokal.href = base64Data;
          linkLokal.download = `Sertifikat-${namaKursus.replace(/\s+/g, '_')}.pdf`;

          document.body.appendChild(linkLokal);
          linkLokal.click();
          document.body.removeChild(linkLokal);
        };

        console.log('Sertifikat resmi berhasil terunduh');
        await loadingSertifikat.dismiss();
        this.isDownloading = false;
        this.activeCertId = null;
      },
      error: async (err) => {
        console.error('Gagal memproses unduhan sertifikat via API:', err);
        await loadingSertifikat.dismiss();
        this.isDownloading = false;
        this.activeCertId = null;
        const toast = await this.toastCtrl.create({
          message: 'Gagal mendownload sertifikat, pastikan jaringan aman.',
          duration: 3000,
          color: 'danger',
        });
        await toast.present();
      },
    });
  }

  async downloadPdfViaBrowser(idSertifikat: number) {
    if (!idSertifikat) return;
    let tokenUser = localStorage.getItem('token') || '';
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }
    const urlDirectDownload = `https://eduvan.rehalivan.com/api/certificates/${idSertifikat}/download?token=${tokenUser}`;

    try {
      await Browser.open({ url: urlDirectDownload });
      console.log(
        'Membuka browser eksternal dengan token bersih:',
        tokenUser,
      );
    } catch (error) {
      console.error('Gagal membuka browser eksternal:', error);
      window.open(urlDirectDownload, '_blank');
    }
  }
}
