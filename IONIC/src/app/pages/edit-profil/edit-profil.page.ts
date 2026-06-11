import { Component, OnInit } from '@angular/core';
import { NavController, ToastController } from '@ionic/angular';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-edit-profil',
  templateUrl: './edit-profil.page.html',
  styleUrls: ['./edit-profil.page.scss'],
  standalone: false,
})
export class EditProfilPage implements OnInit {
  formData: any = {
    name: '',
    email: '',
    instansi: '',
  };

  isLoading: boolean = false;

  constructor(
    private navCtrl: NavController,
    private authService: AuthService,
    private toastCtrl: ToastController
  ) {}

  ngOnInit() {
    this.authService.currentUser$.subscribe({
      next: (user: any) => {
        if (user) {
          this.formData.name = user.name || user.nama || '';
          this.formData.email = user.email || '';
          this.formData.instansi = user.instansi || user.university || '';
        }
      },
    });
  }

  simpanPerubahan() {
    if (!this.formData.name || !this.formData.email) {
      this.tampilkanToast('Nama dan Email tidak boleh kosong!', 'danger');
      return;
    }

    this.isLoading = true;

    this.authService.updateProfile(this.formData).subscribe({
      next: (res: any) => {
        this.isLoading = false;
        // 🟢 KALO SUKSES: Update state pake data dari server
        const updatedUser = res.user || res.data || res;
        this.authService.updateCurrentUserState(updatedUser);

        this.tampilkanToast('Profil berhasil diperbarui!', 'success');
        this.navCtrl.back();
      },
      error: (err) => {
        this.isLoading = false;
        const currentUser = this.authService['currentUserSubject'].getValue();
        const updatedLocalUser = { ...currentUser, ...this.formData };

        this.authService.updateCurrentUserState(updatedLocalUser);

        this.tampilkanToast(
          'Profil diperbarui lokal (Server offline).',
          'warning'
        );
        this.navCtrl.back();
      },
    });
  }

  async tampilkanToast(pesan: string, warna: string) {
    const toast = await this.toastCtrl.create({
      message: pesan,
      duration: 2000,
      color: warna,
      position: 'bottom',
    });
    await toast.present();
  }
}
