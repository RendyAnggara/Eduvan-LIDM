import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.page.html',
  styleUrls: ['./welcome.page.scss'],
  standalone: false,
})
export class WelcomePage {
  isPenggunaBaru: boolean = true;
  currentStep: number = 1;

  setujuTerms: boolean = false;
  setujuPrivasi: boolean = false;

  sudahScrollTerms: boolean = false;
  sudahScrollPrivasi: boolean = false;

  constructor(private router: Router, private toastCtrl: ToastController) {}

  ionViewWillEnter() {
    this.cekStatusPengguna();
  }

  cekStatusPengguna() {
    const statusLama = localStorage.getItem('eduvan_user_registered');
    if (statusLama === 'true') {
      this.isPenggunaBaru = false;
      this.router.navigate(['/login'], { replaceUrl: true });
    } else {
      this.isPenggunaBaru = true;
    }
  }

  cekPosisiScroll(event: any) {
    const targetEl = event.target;
    if (
      targetEl.scrollTop + targetEl.clientHeight >=
      targetEl.scrollHeight - 5
    ) {
      if (this.currentStep === 2) {
        this.sudahScrollTerms = true;
      } else if (this.currentStep === 3) {
        this.sudahScrollPrivasi = true;
      }
    }
  }

  judulToolbarDinamis(): string {
    if (this.currentStep === 2) return 'Syarat & Ketentuan';
    if (this.currentStep === 3) return 'Kebijakan Privasi';
    return '';
  }

  tombolIsDisabled(): boolean {
    if (this.currentStep === 1) return false;
    if (this.currentStep === 2)
      return !this.sudahScrollTerms || !this.setujuTerms;
    if (this.currentStep === 3)
      return !this.sudahScrollPrivasi || !this.setujuPrivasi;
    return true;
  }

  langkahLanjut() {
    if (this.currentStep < 3) {
      this.currentStep++;
    }
  }

  langkahKembali() {
    if (this.currentStep > 1) {
      this.currentStep--;
      if (this.currentStep === 2) {
        this.sudahScrollPrivasi = false;
        this.setujuPrivasi = false;
      } else if (this.currentStep === 1) {
        this.sudahScrollTerms = false;
        this.setujuTerms = false;
      }
    }
  }

  async eksekusiDaftar() {
    if (!this.setujuPrivasi || !this.sudahScrollPrivasi) {
      const toast = await this.toastCtrl.create({
        message: 'Anda harus men-scroll dan menyetujui Kebijakan Privasi.',
        duration: 2000,
        position: 'bottom',
        color: 'danger',
      });
      await toast.present();
      return;
    }
    localStorage.setItem('eduvan_user_registered', 'true');
    this.router.navigate(['/register'], { replaceUrl: true });
  }

  goToLogin() {
    localStorage.setItem('eduvan_user_registered', 'true');
    this.router.navigate(['/login'], { replaceUrl: true });
  }

  handleImageError(event: any) {
    event.target.src = 'assets/icon/computer-science.jpeg';
  }
}
