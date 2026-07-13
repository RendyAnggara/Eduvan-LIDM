import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import {
  NavController,
  AlertController,
  ActionSheetController,
} from '@ionic/angular';
import { AuthService } from '../../services/auth';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage implements OnInit {
  userProfile: any = null;

  selectedAvatar: string = 'assets/icon/avatar-neutral.png';

  isSkModalOpen: boolean = false;
  isPrivacyModalOpen: boolean = false;

  angkaKursus: number = 0;
  angkaSertifikat: number = 0;
  isLogoutAlertOpen: boolean = false;

  logoutAlertButtons = [
    {
      text: 'Batal',
      role: 'cancel',
      cssClass: 'alert-btn-batal',
      handler: () => {
        this.isLogoutAlertOpen = false;
        this.cdr.detectChanges();
      },
    },
    {
      text: 'Ya, Keluar',
      role: 'confirm',
      cssClass: 'alert-btn-keluar',
      handler: () => {
        this.isLogoutAlertOpen = false;
        this.authService.logout();
        this.navCtrl.navigateRoot('/login');
        this.cdr.detectChanges();
      },
    },
  ];

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private authService: AuthService,
    private actionSheetCtrl: ActionSheetController,
    private cdr: ChangeDetectorRef,
    private courseService: CourseService
  ) {}

  private getAvatarKey(): string {
    if (this.userProfile && this.userProfile.email) {
      return `user_avatar_${this.userProfile.email}`;
    }
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (localUserData) {
      const parsed = JSON.parse(localUserData);
      if (parsed && parsed.email) {
        return `user_avatar_${parsed.email}`;
      }
    }
    return 'user_avatar_default';
  }

  ngOnInit() {
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (localUserData) {
      this.userProfile = JSON.parse(localUserData);
      this.loadSavedAvatar();
    }

    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        this.userProfile = user;
        const currentSavedAvatar = localStorage.getItem(this.getAvatarKey());
        let targetAvatar = user.avatar;
        if (
          targetAvatar &&
          targetAvatar.startsWith('http') &&
          currentSavedAvatar &&
          !currentSavedAvatar.startsWith('http')
        ) {
          targetAvatar = currentSavedAvatar;
        }

        this.userProfile = { ...user, avatar: targetAvatar };
        if (targetAvatar) {
          this.selectedAvatar = targetAvatar;
        }
        this.cdr.detectChanges();
      }
    });
  }

  ionViewWillEnter() {
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (localUserData) {
      this.userProfile = JSON.parse(localUserData);
      this.loadSavedAvatar();
      this.cdr.detectChanges();
    }

    this.loadProfileFromAPI();
    this.hitungStatistikMandiri();
  }

  loadSavedAvatar() {
    const savedAvatar = localStorage.getItem(this.getAvatarKey());
    if (savedAvatar && !savedAvatar.startsWith('http')) {
      this.selectedAvatar = savedAvatar;
    } else if (
      this.userProfile &&
      this.userProfile.avatar &&
      !this.userProfile.avatar.startsWith('http')
    ) {
      this.selectedAvatar = this.userProfile.avatar;
    } else {
      this.selectedAvatar = 'assets/icon/avatar-neutral.png';
    }
  }

  async changeAvatar() {
    const actionSheet = await this.actionSheetCtrl.create({
      header: 'Pilih Karakter Avatar',
      cssClass: 'premium-avatar-sheet',
      mode: 'ios',
      buttons: [
        {
          text: 'Karakter Laki-laki 👦',
          icon: 'man-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-male.png');
          },
        },
        {
          text: 'Karakter Perempuan 👧',
          icon: 'woman-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-female.png');
          },
        },
        {
          text: 'Gunakan Gambar Netral 👤',
          icon: 'person-circle-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-neutral.png');
          },
        },
        {
          text: 'Batal',
          role: 'cancel',
          icon: 'close',
        },
      ],
    });
    await actionSheet.present();
  }

  updateAvatar(path: string) {
    this.selectedAvatar = path;

    if (this.userProfile) {
      this.userProfile.avatar = path;
    } else {
      const localUserData =
        localStorage.getItem('user_data') || localStorage.getItem('user');
      this.userProfile = localUserData
        ? JSON.parse(localUserData)
        : { name: '', email: '' };
      this.userProfile.avatar = path;
    }
    localStorage.setItem(this.getAvatarKey(), path);
    this.cdr.detectChanges();
    let currentName = this.userProfile?.name;
    let currentEmail = this.userProfile?.email;

    if (!currentName || !currentEmail) {
      const backupData =
        localStorage.getItem('user_data') || localStorage.getItem('user');
      if (backupData) {
        const parsedBackup = JSON.parse(backupData);
        currentName = currentName || parsedBackup.name;
        currentEmail = currentEmail || parsedBackup.email;
      }
    }

    if (currentName && currentEmail) {
      const payload = {
        avatar: path,
        name: currentName,
        email: currentEmail,
      };

      if (typeof this.authService.updateProfile === 'function') {
        this.authService.updateProfile(payload).subscribe({
          next: (res: any) => {
            console.log('✅ Avatar tersimpan di cPanel Server!', res);
            const dataUserTerbaru = res.user ? res.user : res;
            localStorage.setItem('user_data', JSON.stringify(dataUserTerbaru));
            localStorage.setItem('user', JSON.stringify(dataUserTerbaru));
            if (typeof this.authService.updateCurrentUserState === 'function') {
              this.authService.updateCurrentUserState(dataUserTerbaru);
            }
            this.cdr.detectChanges();
          },
          error: (err) => console.error('❌ Gagal sinkronisasi ke DB:', err),
        });
      }
    } else {
      console.warn(
        '⚠️ Gagal kirim API: Data name atau email tidak dapat ditemukan di memori ataupun cache.'
      );
    }
  }

  hitungStatistikMandiri() {
    this.courseService.getMyEnrollments().subscribe({
      next: (enrollRes: any) => {
        const dataKursus = enrollRes.data ? enrollRes.data : enrollRes;
        if (Array.isArray(dataKursus)) {
          this.angkaKursus = dataKursus.length;
          this.cdr.detectChanges();
        }
      },
      error: (err) => console.error('Bypass Kursus Gagal:', err),
    });

    this.courseService.getMyCertificates().subscribe({
      next: (certRes: any) => {
        const dataSertifikat = certRes.data ? certRes.data : certRes;
        if (Array.isArray(dataSertifikat)) {
          this.angkaSertifikat = dataSertifikat.length;
          this.cdr.detectChanges();
        }
      },
      error: (err) => console.error('Bypass Sertifikat Gagal:', err),
    });
  }

  loadProfileFromAPI() {
    this.authService.getProfileFromServer().subscribe({
      next: (res: any) => {
        if (res) {
          const dataTerbaru = res.data ? res.data : res;

          this.userProfile = dataTerbaru;
          const currentSavedAvatar = localStorage.getItem(this.getAvatarKey());

          if (dataTerbaru && dataTerbaru.avatar) {
            if (dataTerbaru.avatar.startsWith('http')) {
              if (
                currentSavedAvatar &&
                !currentSavedAvatar.startsWith('http')
              ) {
                dataTerbaru.avatar = currentSavedAvatar;
              } else {
                dataTerbaru.avatar = 'assets/icon/avatar-neutral.png';
              }
            }
          } else {
            if (currentSavedAvatar && !currentSavedAvatar.startsWith('http')) {
              dataTerbaru.avatar = currentSavedAvatar;
            } else {
              dataTerbaru.avatar = 'assets/icon/avatar-neutral.png';
            }
          }

          this.selectedAvatar = dataTerbaru.avatar;

          localStorage.setItem(this.getAvatarKey(), dataTerbaru.avatar);
          localStorage.setItem('user_data', JSON.stringify(dataTerbaru));
          localStorage.setItem('user', JSON.stringify(dataTerbaru));

          if (typeof this.authService.updateCurrentUserState === 'function') {
            this.authService.updateCurrentUserState(dataTerbaru);
          }

          if (dataTerbaru.enrollments_count !== undefined) {
            this.angkaKursus = dataTerbaru.enrollments_count;
          }
          if (dataTerbaru.certificates_count !== undefined) {
            this.angkaSertifikat = dataTerbaru.certificates_count;
          }

          this.cdr.detectChanges();
        }
      },
      error: (err) => {
        console.error('Error saat load profile:', err);
        this.loadSavedAvatar();
      },
    });
  }

  goToEdit() {
    this.navCtrl.navigateForward('/edit-profil');
  }

  goToCertificate() {
    this.navCtrl.navigateForward('/certificate');
  }

  goToHistory() {
    this.navCtrl.navigateForward('/riwayat-transaksi');
  }

  goToNotif() {
    this.navCtrl.navigateForward('/notifications');
  }

  bukaKonfirmasiKeluar() {
    this.isLogoutAlertOpen = true;
    this.cdr.detectChanges();
  }

  async logout() {
    const alert = await this.alertCtrl.create({
      header: 'Konfirmasi Keluar',
      message: 'Apakah kamu yakin ingin keluar?',
      mode: 'ios',
      buttons: [
        { text: 'Batal', role: 'cancel' },
        {
          text: 'Ya, Keluar',
          handler: () => {
            this.authService.logout();
            this.navCtrl.navigateRoot('/login');
          },
        },
      ],
    });
    await alert.present();
  }

  setSkModal(isOpen: boolean) {
    this.isSkModalOpen = isOpen;
    this.cdr.detectChanges();
  }

  setPrivacyModal(isOpen: boolean) {
    this.isPrivacyModalOpen = isOpen;
    this.cdr.detectChanges();
  }
}
