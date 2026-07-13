import { Component, OnInit } from '@angular/core';
import { NavController, IonicModule } from '@ionic/angular';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-splash',
  templateUrl: './splash.page.html',
  styleUrls: ['./splash.page.scss'],
  standalone: true,
  imports: [IonicModule, CommonModule],
})
export class SplashPage implements OnInit {
  isFadingOut = false;

  constructor(private navCtrl: NavController) {}

  ngOnInit() {
    this.jalankanSplash();
  }

  async jalankanSplash() {
    await new Promise((resolve) => setTimeout(resolve, 2000));
    this.isFadingOut = true;
    await new Promise((resolve) => setTimeout(resolve, 400));
    const isLoggedIn = localStorage.getItem('token');
    const hasSeenWelcome = localStorage.getItem('hasSeenWelcome');

    if (isLoggedIn) {
      this.navCtrl.navigateRoot('/tabs', {
        animated: true,
        animationDirection: 'forward',
      });
    } else if (hasSeenWelcome === 'true') {
      this.navCtrl.navigateRoot('/login', {
        animated: true,
        animationDirection: 'forward',
      });
    } else {
      localStorage.setItem('hasSeenWelcome', 'true');
      this.navCtrl.navigateRoot('/welcome', {
        animated: true,
        animationDirection: 'forward',
      });
    }
  }
}
